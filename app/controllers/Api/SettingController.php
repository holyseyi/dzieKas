<?php

/**
 * API Settings Controller
 *
 * @package DzieKas\Controllers\Api
 */

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index(): void
    {
        $settingModel = new Setting();
        $settings = $settingModel->getAll();

        // Only expose public settings
        $public = [
            'site_name' => $settings['site_name'] ?? $this->config['name'],
            'site_tagline' => $settings['site_tagline'] ?? $this->config['tagline'],
            'site_description' => $settings['site_description'] ?? '',
            'allow_registration' => $settings['allow_registration'] ?? true,
            'dark_mode_default' => $settings['dark_mode_default'] ?? true,
        ];

        $this->json(['success' => true, 'data' => $public]);
    }
}
