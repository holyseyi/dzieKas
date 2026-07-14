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
        $files = $db->fetchAll('SELECT * FROM media_files WHERE folder_id = ? ORDER BY original_name', [$folder['id']]);
        $breadcrumbs = $this->getBreadcrumbs($db, (int) $folder['id']);

        $this->view('admin/media/index', [
            'title' => 'Media Library',
            'folder' => $folder,
            'folders' => $folders,
            'files' => $files,
            'breadcrumbs' => $breadcrumbs,
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

        if (empty($_FILES['file']['name']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Please select a file to upload.');
            $this->redirect('/admin/media?folder_id=' . $folderId);
        }

        $folder = $db->fetchOne('SELECT * FROM media_folders WHERE id = ?', [$folderId]);
        if (!$folder) {
            Session::flash('error', 'Invalid folder.');
            $this->redirect('/admin/media');
        }

        $file = $_FILES['file'];
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = Security::generateToken(16) . '.' . $extension;
        $uploadDir = dirname(__DIR__, 3) . '/public/storage/media' . $folder['path'];
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $destination = $uploadDir . '/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $db->insert('media_files', [
                'folder_id' => $folderId,
                'filename' => $filename,
                'original_name' => Security::sanitize($file['name']),
                'mime_type' => $mimeType,
                'file_size' => (int) $file['size'],
                'path' => rtrim($folder['path'], '/') . '/' . $filename,
            ]);
            Session::flash('success', 'File uploaded successfully.');
        } else {
            Session::flash('error', 'Failed to upload file.');
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
}
