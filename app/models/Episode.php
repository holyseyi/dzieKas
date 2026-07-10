<?php

/**
 * Episode Model
 *
 * @package DzieKas\Models
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Episode extends Model
{
    protected string $table = 'episodes';

    /**
     * Get seasons with episodes for a series.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getSeasonsWithEpisodes(int $contentId): array
    {
        $seasons = $this->db->fetchAll(
            "SELECT * FROM seasons WHERE content_id = ? ORDER BY season_number",
            [$contentId]
        );

        foreach ($seasons as &$season) {
            $season['episodes'] = $this->db->fetchAll(
                "SELECT e.*,
                    (SELECT COUNT(*) FROM downloads d WHERE d.episode_id = e.id AND d.is_active = 1) as download_count,
                    (SELECT COUNT(*) FROM streaming_links s WHERE s.episode_id = e.id AND s.is_active = 1) as stream_count
                 FROM episodes e WHERE e.season_id = ? ORDER BY e.episode_number",
                [$season['id']]
            );
        }

        return $seasons;
    }

    /**
     * Get episode with download/stream links.
     *
     * @return array<string, mixed>|null
     */
    public function getFull(int $episodeId): ?array
    {
        $episode = $this->find($episodeId);
        if (!$episode) {
            return null;
        }

        $episode['downloads'] = $this->db->fetchAll(
            "SELECT * FROM downloads WHERE episode_id = ? AND is_active = 1 ORDER BY sort_order",
            [$episodeId]
        );

        $episode['streaming_links'] = $this->db->fetchAll(
            "SELECT * FROM streaming_links WHERE episode_id = ? AND is_active = 1 ORDER BY sort_order",
            [$episodeId]
        );

        return $episode;
    }
}
