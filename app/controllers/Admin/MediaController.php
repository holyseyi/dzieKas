<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Helpers\Security;
use App\Helpers\Session;
use App\Helpers\Slug;

class MediaController extends Controller
{
    public function index(): void
    {
        $db = Database::getInstance();
        $folderId = $this->input('folder_id');
        $folder = null;
        if ($folderId) {
            $folder = $db->fetchOne('SELECT * FROM media_folders WHERE id = ?', [(int) $folderId]);
        }
        if (!$folder) {
            $folder = $db->fetchOne('SELECT * FROM media_folders WHERE parent_id IS NULL ORDER BY id ASC LIMIT 1');
        }
        if (!$folder) {
            $folderId = (int) $db->insert('media_folders', ['name' => 'Root', 'parent_id' => null, 'path' => '/']);
            $folder = $db->fetchOne('SELECT * FROM media_folders WHERE id = ?', [$folderId]);
        }

        $folders = $db->fetchAll('SELECT * FROM media_folders WHERE parent_id = ? ORDER BY name', [$folder['id']]);

        $search = $this->query('q', '');
        $fileQuery = 'SELECT * FROM media_files WHERE folder_id = ?';
        $fileParams = [$folder['id']];
        if ($search) {
            $fileQuery .= ' AND original_name LIKE ?';
            $fileParams[] = '%' . $search . '%';
        }
        $fileQuery .= ' ORDER BY original_name';
        $files = $db->fetchAll($fileQuery, $fileParams);

        $allFolders = $db->fetchAll('SELECT * FROM media_folders ORDER BY name');

        $breadcrumbs = $this->getBreadcrumbs($db, (int) $folder['id']);

        $this->view('admin/media/index', [
            'title' => 'Media Library',
            'folder' => $folder,
            'folders' => $folders,
            'files' => $files,
            'breadcrumbs' => $breadcrumbs,
            'allFolders' => $allFolders,
            'search' => $search,
        ], 'layouts/admin');
    }

    public function createFolder(): void
    {
        $this->validateCsrf();
        $db = Database::getInstance();
        $parentId = $this->input('parent_id') ?: null;
        $name = Security::sanitize((string) $this->input('name', ''));

        if (!$name) {
            Session::flash('error', 'Folder name is required.');
            $this->redirect('/admin/media');
        }

        $parentPath = '/';
        if ($parentId) {
            $parent = $db->fetchOne('SELECT * FROM media_folders WHERE id = ?', [(int) $parentId]);
            if ($parent) {
                $parentPath = $parent['path'];
            }
        }

        $path = rtrim($parentPath, '/') . '/' . Slug::generate($name);

        $db->insert('media_folders', [
            'name' => $name,
            'parent_id' => $parentId ?: null,
            'path' => $path,
        ]);

        Session::flash('success', 'Folder created.');
        $this->redirect('/admin/media?folder_id=' . ($parentId ?: ''));
    }

    public function upload(): void
    {
        $this->validateCsrf();
        $db = Database::getInstance();
        $folderId = (int) $this->input('folder_id', 0);

        $folder = $db->fetchOne('SELECT * FROM media_folders WHERE id = ?', [$folderId]);
        if (!$folder) {
            Session::flash('error', 'Invalid folder.');
            $this->redirect('/admin/media');
        }

        $uploadDir = dirname(__DIR__, 3) . '/public/storage/media' . $folder['path'];
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $files = $_FILES['files'] ?? null;
        if (!$files || empty($files['name'][0])) {
            Session::flash('error', 'Please select files to upload.');
            $this->redirect('/admin/media?folder_id=' . $folderId);
        }

        $uploaded = 0;
        $failed = 0;

        foreach ($files['name'] as $index => $name) {
            if ($files['error'][$index] !== UPLOAD_ERR_OK) {
                $failed++;
                continue;
            }

            $file = [
                'name' => $name,
                'type' => $files['type'][$index],
                'tmp_name' => $files['tmp_name'][$index],
                'error' => $files['error'][$index],
                'size' => $files['size'][$index],
            ];

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = Security::generateToken(16) . '.' . $extension;
            $destination = $uploadDir . '/' . $filename;
            $thumbnailPath = null;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                if (str_starts_with((string) $mimeType, 'video/')) {
                    $thumbnailFilename = pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
                    $thumbnailFullPath = $uploadDir . '/' . $thumbnailFilename;
                    $this->generateThumbnail($destination, $thumbnailFullPath);
                    if (file_exists($thumbnailFullPath)) {
                        $thumbnailPath = rtrim($folder['path'], '/') . '/' . $thumbnailFilename;
                    }
                }
                $db->insert('media_files', [
                    'folder_id' => $folderId,
                    'filename' => $filename,
                    'original_name' => Security::sanitize($file['name']),
                    'mime_type' => $mimeType,
                    'file_size' => (int) $file['size'],
                    'path' => rtrim($folder['path'], '/') . '/' . $filename,
                    'thumbnail_path' => $thumbnailPath,
                ]);
                $uploaded++;
            } else {
                $failed++;
            }
        }

        if ($uploaded > 0) {
            Session::flash('success', "{$uploaded} file(s) uploaded successfully." . ($failed > 0 ? " {$failed} failed." : ''));
        } else {
            Session::flash('error', 'Failed to upload files.');
        }

        $this->redirect('/admin/media?folder_id=' . $folderId);
    }

    public function deleteFolder(string $id): void
    {
        $this->validateCsrf();
        $db = Database::getInstance();
        $folder = $db->fetchOne('SELECT * FROM media_folders WHERE id = ?', [$id]);

        if (!$folder) {
            Session::flash('error', 'Folder not found.');
            $this->redirect('/admin/media');
        }

        $db->delete('media_folders', 'id = ?', [$id]);
        Session::flash('success', 'Folder deleted.');
        $this->redirect('/admin/media?folder_id=' . ($folder['parent_id'] ?: ''));
    }

    public function deleteFile(string $id): void
    {
        $this->validateCsrf();
        $db = Database::getInstance();
        $file = $db->fetchOne('SELECT * FROM media_files WHERE id = ?', [$id]);

        if (!$file) {
            Session::flash('error', 'File not found.');
            $this->redirect('/admin/media');
        }

        $filePath = dirname(__DIR__, 3) . '/public/storage/' . ltrim($file['path'], '/');
        if (file_exists($filePath)) {
            @unlink($filePath);
        }

        $db->delete('media_files', 'id = ?', [$id]);
        Session::flash('success', 'File deleted.');
        $this->redirect('/admin/media?folder_id=' . $file['folder_id']);
    }

    public function moveFile(string $id): void
    {
        $this->validateCsrf();
        $db = Database::getInstance();
        $file = $db->fetchOne('SELECT * FROM media_files WHERE id = ?', [$id]);

        if (!$file) {
            Session::flash('error', 'File not found.');
            $this->redirect('/admin/media');
        }

        $targetFolderId = (int) $this->input('target_folder_id', 0);
        if (!$targetFolderId) {
            Session::flash('error', 'Invalid target folder.');
            $this->redirect('/admin/media?folder_id=' . $file['folder_id']);
        }

        $targetFolder = $db->fetchOne('SELECT * FROM media_folders WHERE id = ?', [$targetFolderId]);
        if (!$targetFolder) {
            Session::flash('error', 'Target folder not found.');
            $this->redirect('/admin/media?folder_id=' . $file['folder_id']);
        }

        $sourcePath = dirname(__DIR__, 3) . '/public/storage/' . ltrim($file['path'], '/');
        $newFilename = $file['filename'];
        $targetDir = dirname(__DIR__, 3) . '/public/storage/media' . $targetFolder['path'];
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $targetPath = $targetDir . '/' . $newFilename;
        $newRelativePath = rtrim($targetFolder['path'], '/') . '/' . $newFilename;

        if (rename($sourcePath, $targetPath)) {
            $db->update('media_files', ['folder_id' => $targetFolderId, 'path' => $newRelativePath], 'id = ?', [$id]);
            Session::flash('success', 'File moved successfully.');
        } else {
            Session::flash('error', 'Failed to move file.');
        }

        $this->redirect('/admin/media?folder_id=' . $file['folder_id']);
    }

    public function videos(): void
    {
        $db = Database::getInstance();
        $videos = $db->fetchAll(
            "SELECT mf.*, mf2.name as folder_name FROM media_files mf
             JOIN media_folders mf2 ON mf.folder_id = mf2.id
             WHERE mf.mime_type LIKE 'video/%'
             ORDER BY mf.created_at DESC"
        );

        header('Content-Type: application/json');
        echo json_encode(array_map(function ($v) {
            return [
                'id' => (int) $v['id'],
                'original_name' => $v['original_name'],
                'path' => $v['path'],
                'url' => rtrim($this->config['url'], '/') . '/storage/' . ltrim($v['path'], '/'),
                'folder_name' => $v['folder_name'],
                'file_size' => $this->formatBytes((int) $v['file_size']),
            ];
        }, $videos));
        exit;
    }

    private function getBreadcrumbs(Database $db, int $folderId): array
    {
        $breadcrumbs = [];
        $current = $db->fetchOne('SELECT * FROM media_folders WHERE id = ?', [$folderId]);

        while ($current) {
            array_unshift($breadcrumbs, $current);
            $parentId = $current['parent_id'] ?: null;
            $current = $parentId ? $db->fetchOne('SELECT * FROM media_folders WHERE id = ?', [$parentId]) : null;
        }

        return $breadcrumbs;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    private function generateThumbnail(string $videoPath, string $thumbnailPath): bool
    {
        if (!file_exists($videoPath)) {
            return false;
        }

        $cmd = sprintf(
            'ffmpeg -y -i %s -ss 00:00:01 -vframes 1 -q:v 2 %s 2>/dev/null',
            escapeshellarg($videoPath),
            escapeshellarg($thumbnailPath)
        );

        exec($cmd, $output, $returnCode);

        return $returnCode === 0 && file_exists($thumbnailPath);
    }
}
