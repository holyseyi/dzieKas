<?php

/**
 * Content Model - Movies, Series, Anime, etc.
 *
 * @package DzieKas\Models
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Content extends Model
{
    protected string $table = 'content';

    /**
     * Get content with related data.
     *
     * @return array<string, mixed>|null
     */
    public function getFull(string $slug): ?array
    {
        $sql = "SELECT c.*, cat.name as category_name, cat.slug as category_slug,
                       co.name as country_name, co.slug as country_slug,
                       l.name as language_name
                FROM content c
                LEFT JOIN categories cat ON c.category_id = cat.id
                LEFT JOIN countries co ON c.country_id = co.id
                LEFT JOIN languages l ON c.language_id = l.id
                WHERE c.slug = ? AND c.status = 'published'
                LIMIT 1";

        $content = $this->db->fetchOne($sql, [$slug]);
        if (!$content) {
            return null;
        }

        $content['genres'] = $this->getGenres((int) $content['id']);
        $content['actors'] = $this->getActors((int) $content['id']);
        $content['directors'] = $this->getDirectors((int) $content['id']);
        $content['downloads'] = $this->getDownloads((int) $content['id']);
        $content['streaming_links'] = $this->getStreamingLinks((int) $content['id']);
        $content['screenshots'] = $this->getScreenshots((int) $content['id']);
        $content['trailers'] = $this->getTrailers((int) $content['id']);

        return $content;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getGenres(int $contentId): array
    {
        $sql = "SELECT g.* FROM genres g
                JOIN content_genres cg ON g.id = cg.genre_id
                WHERE cg.content_id = ?";
        return $this->db->fetchAll($sql, [$contentId]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getActors(int $contentId): array
    {
        $sql = "SELECT a.*, ca.character_name FROM actors a
                JOIN content_actors ca ON a.id = ca.actor_id
                WHERE ca.content_id = ? ORDER BY ca.sort_order";
        return $this->db->fetchAll($sql, [$contentId]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getDirectors(int $contentId): array
    {
        $sql = "SELECT d.* FROM directors d
                JOIN content_directors cd ON d.id = cd.director_id
                WHERE cd.content_id = ?";
        return $this->db->fetchAll($sql, [$contentId]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getDownloads(int $contentId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM downloads WHERE content_id = ? AND episode_id IS NULL AND is_active = 1 ORDER BY sort_order",
            [$contentId]
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getStreamingLinks(int $contentId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM streaming_links WHERE content_id = ? AND episode_id IS NULL AND is_active = 1 ORDER BY sort_order",
            [$contentId]
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getScreenshots(int $contentId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM screenshots WHERE content_id = ? ORDER BY sort_order",
            [$contentId]
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getTrailers(int $contentId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM trailers WHERE content_id = ? ORDER BY sort_order",
            [$contentId]
        );
    }

    /**
     * Get latest published content.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getLatest(int $limit = 12, ?string $type = null): array
    {
        $where = "status = 'published'";
        $params = [];

        if ($type) {
            $where .= " AND type = ?";
            $params[] = $type;
        }

        return $this->all($where, $params, 'published_at DESC', $limit);
    }

    /**
     * Get trending content based on views in last N days.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getTrending(int $limit = 12, int $days = 7): array
    {
        $sql = "SELECT c.* FROM content c
                WHERE c.status = 'published'
                AND c.updated_at >= datetime('now', '-' || ? || ' days')
                ORDER BY (c.view_count * 0.4 + c.download_count * 0.3 + c.like_count * 0.3) DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$days, $limit]);
    }

    /**
     * Get featured content for a section.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getFeatured(string $section = 'featured', int $limit = 12): array
    {
        $sql = "SELECT c.* FROM content c
                JOIN featured_content fc ON c.id = fc.content_id
                WHERE fc.section = ? AND fc.is_active = 1 AND c.status = 'published'
                AND (fc.starts_at IS NULL OR fc.starts_at <= datetime('now'))
                AND (fc.ends_at IS NULL OR fc.ends_at >= datetime('now'))
                ORDER BY fc.sort_order LIMIT ?";

        return $this->db->fetchAll($sql, [$section, $limit]);
    }

    /**
     * Get content by category slug.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getByCategory(string $categorySlug, int $limit = 12): array
    {
        $sql = "SELECT c.* FROM content c
                JOIN categories cat ON c.category_id = cat.id
                WHERE cat.slug = ? AND c.status = 'published'
                ORDER BY c.published_at DESC LIMIT ?";

        return $this->db->fetchAll($sql, [$categorySlug, $limit]);
    }

    /**
     * Get content by country slug.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getByCountry(string $countrySlug, int $limit = 12): array
    {
        $sql = "SELECT c.* FROM content c
                JOIN countries co ON c.country_id = co.id
                WHERE co.slug = ? AND c.status = 'published'
                ORDER BY c.published_at DESC LIMIT ?";

        return $this->db->fetchAll($sql, [$countrySlug, $limit]);
    }

    /**
     * Get content by genre slug.
     *
     * @return array{data: array<int, array<string, mixed>>, total: int, page: int, per_page: int, total_pages: int}
     */
    public function getByGenre(string $genreSlug, int $page = 1, int $perPage = 24): array
    {
        $offset = ($page - 1) * $perPage;

        $countSql = "SELECT COUNT(*) as total FROM content c
                     JOIN content_genres cg ON c.id = cg.content_id
                     JOIN genres g ON cg.genre_id = g.id
                     WHERE g.slug = ? AND c.status = 'published'";

        $total = (int) ($this->db->fetchOne($countSql, [$genreSlug])['total'] ?? 0);

        $sql = "SELECT c.* FROM content c
                JOIN content_genres cg ON c.id = cg.content_id
                JOIN genres g ON cg.genre_id = g.id
                WHERE g.slug = ? AND c.status = 'published'
                ORDER BY c.published_at DESC LIMIT ? OFFSET ?";

        return [
            'data' => $this->db->fetchAll($sql, [$genreSlug, $perPage, $offset]),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Get content by release year.
     *
     * @return array{data: array<int, array<string, mixed>>, total: int, page: int, per_page: int, total_pages: int}
     */
    public function getByYear(int $year, int $page = 1, int $perPage = 24): array
    {
        return $this->paginate($page, $perPage, "release_year = ? AND status = 'published'", [$year], 'published_at DESC');
    }

    /**
     * Search content across multiple fields.
     *
     * @return array<int, array<string, mixed>>
     */
    public function search(string $query, int $limit = 20): array
    {
        $term = '%' . $query . '%';

        $sql = "SELECT DISTINCT c.* FROM content c
                LEFT JOIN content_actors ca ON c.id = ca.content_id
                LEFT JOIN actors a ON ca.actor_id = a.id
                LEFT JOIN content_directors cd ON c.id = cd.content_id
                LEFT JOIN directors d ON cd.director_id = d.id
                LEFT JOIN content_genres cg ON c.id = cg.content_id
                LEFT JOIN genres g ON cg.genre_id = g.id
                LEFT JOIN countries co ON c.country_id = co.id
                LEFT JOIN languages l ON c.language_id = l.id
                WHERE c.status = 'published' AND (
                    c.title LIKE ? OR c.original_title LIKE ? OR
                    a.name LIKE ? OR d.name LIKE ? OR
                    g.name LIKE ? OR co.name LIKE ? OR
                    CAST(c.release_year AS TEXT) LIKE ? OR
                    l.name LIKE ?
                )
                ORDER BY c.view_count DESC LIMIT ?";

        return $this->db->fetchAll($sql, array_fill(0, 8, $term) + [$limit]);
    }

    /**
     * Get related content by shared genres.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getRelated(int $contentId, int $limit = 8): array
    {
        $sql = "SELECT DISTINCT c.* FROM content c
                JOIN content_genres cg ON c.id = cg.content_id
                WHERE cg.genre_id IN (
                    SELECT genre_id FROM content_genres WHERE content_id = ?
                )
                AND c.id != ? AND c.status = 'published'
                ORDER BY c.view_count DESC LIMIT ?";

        return $this->db->fetchAll($sql, [$contentId, $contentId, $limit]);
    }

    /**
     * Increment view count.
     */
    public function incrementViews(int $id): void
    {
        $this->db->query('UPDATE content SET view_count = view_count + 1 WHERE id = ?', [$id]);
    }

    /**
     * Get recently updated series.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getRecentlyUpdatedSeries(int $limit = 12): array
    {
        $sql = "SELECT c.* FROM content c
                WHERE c.type IN ('series', 'anime', 'k-drama') AND c.status = 'published'
                ORDER BY c.updated_at DESC LIMIT ?";

        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Get popular content this week.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getPopularThisWeek(int $limit = 12): array
    {
        $sql = "SELECT c.* FROM content c
                WHERE c.status = 'published'
                AND c.updated_at >= datetime('now', '-7 days')
                ORDER BY c.view_count DESC LIMIT ?";

        return $this->db->fetchAll($sql, [$limit]);
    }

    /**
     * Get latest uploaded videos.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getLatestVideos(int $limit = 12): array
    {
        $sql = "SELECT c.* FROM content c
                WHERE c.status = 'published'
                AND c.video_path IS NOT NULL AND c.video_path != ''
                ORDER BY c.published_at DESC LIMIT ?";

        return $this->db->fetchAll($sql, [$limit]);
    }
}
