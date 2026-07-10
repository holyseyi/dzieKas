-- DzieKas Seed Data

-- Roles
INSERT OR IGNORE INTO roles (id, name, slug, description) VALUES
(1, 'Super Admin', 'super_admin', 'Full system access'),
(2, 'Admin', 'admin', 'Administrative access'),
(3, 'Editor', 'editor', 'Content management access'),
(4, 'User', 'user', 'Regular user access');

-- Permissions
INSERT OR IGNORE INTO permissions (id, name, slug) VALUES
(1, 'Manage Users', 'manage_users'),
(2, 'Manage Content', 'manage_content'),
(3, 'Manage Settings', 'manage_settings'),
(4, 'Manage Ads', 'manage_ads'),
(5, 'View Analytics', 'view_analytics'),
(6, 'Manage Comments', 'manage_comments');

-- Role Permissions
INSERT OR IGNORE INTO role_permissions (role_id, permission_id) VALUES
(1,1),(1,2),(1,3),(1,4),(1,5),(1,6),
(2,1),(2,2),(2,3),(2,4),(2,5),(2,6),
(3,2),(3,6);

-- Categories
INSERT OR IGNORE INTO categories (id, name, slug, description, icon) VALUES
(1, 'Movies', 'movies', 'Feature films and cinema', 'film'),
(2, 'TV Series', 'tv-series', 'Television series and shows', 'tv'),
(3, 'Anime', 'anime', 'Japanese animation', 'sparkles'),
(4, 'K-Drama', 'k-drama', 'Korean dramas', 'heart'),
(5, 'Documentary', 'documentary', 'Documentary films', 'book-open'),
(6, 'Music Video', 'music-video', 'Music videos', 'music'),
(7, 'Articles', 'articles', 'Entertainment articles', 'newspaper'),
(8, 'News', 'news', 'Entertainment news', 'megaphone');

-- Genres
INSERT OR IGNORE INTO genres (id, name, slug) VALUES
(1, 'Action', 'action'),
(2, 'Comedy', 'comedy'),
(3, 'Horror', 'horror'),
(4, 'Romance', 'romance'),
(5, 'Thriller', 'thriller'),
(6, 'Crime', 'crime'),
(7, 'Animation', 'animation'),
(8, 'Sci-Fi', 'sci-fi'),
(9, 'Family', 'family'),
(10, 'Adventure', 'adventure'),
(11, 'Documentary', 'documentary'),
(12, 'Fantasy', 'fantasy'),
(13, 'Mystery', 'mystery'),
(14, 'War', 'war'),
(15, 'History', 'history'),
(16, 'Biography', 'biography'),
(17, 'Musical', 'musical'),
(18, 'Others', 'others');

-- Countries
INSERT OR IGNORE INTO countries (id, name, slug, code, flag_emoji) VALUES
(1, 'Nigeria', 'nigeria', 'NG', '🇳🇬'),
(2, 'United States', 'united-states', 'US', '🇺🇸'),
(3, 'India', 'india', 'IN', '🇮🇳'),
(4, 'South Korea', 'south-korea', 'KR', '🇰🇷'),
(5, 'Japan', 'japan', 'JP', '🇯🇵'),
(6, 'United Kingdom', 'united-kingdom', 'GB', '🇬🇧'),
(7, 'France', 'france', 'FR', '🇫🇷'),
(8, 'Canada', 'canada', 'CA', '🇨🇦');

-- Languages
INSERT OR IGNORE INTO languages (id, name, slug, code) VALUES
(1, 'English', 'english', 'en'),
(2, 'Yoruba', 'yoruba', 'yo'),
(3, 'Igbo', 'igbo', 'ig'),
(4, 'Hausa', 'hausa', 'ha'),
(5, 'Hindi', 'hindi', 'hi'),
(6, 'Korean', 'korean', 'ko'),
(7, 'Japanese', 'japanese', 'ja'),
(8, 'French', 'french', 'fr');

-- Site Settings
INSERT OR IGNORE INTO site_settings (key, value, type, group_name) VALUES
('site_name', 'DzieKas Entertainment', 'string', 'general'),
('site_tagline', 'Your Ultimate Movie & Entertainment Portal', 'string', 'general'),
('site_description', 'Stream and download the latest movies, TV series, anime, K-dramas and more.', 'string', 'general'),
('maintenance_mode', '0', 'boolean', 'general'),
('allow_registration', '1', 'boolean', 'general'),
('items_per_page', '24', 'integer', 'general'),
('trending_algorithm_days', '7', 'integer', 'general'),
('dark_mode_default', '1', 'boolean', 'theme'),
('primary_color', '#e11d48', 'string', 'theme'),
('contact_email', 'contact@dziekas.com', 'string', 'contact'),
('social_twitter', '', 'string', 'social'),
('social_facebook', '', 'string', 'social'),
('social_instagram', '', 'string', 'social');

-- Homepage Sections
INSERT OR IGNORE INTO homepage_sections (name, slug, title, query_type, limit_count, sort_order) VALUES
('Hero Slider', 'hero', 'Featured', 'featured', 5, 1),
('Featured Movies', 'featured-movies', 'Featured Movies', 'featured', 12, 2),
('Latest Uploads', 'latest', 'Latest Uploads', 'latest', 12, 3),
('Trending', 'trending', 'Trending Now', 'trending', 12, 4),
('Recently Updated Series', 'updated-series', 'Recently Updated Series', 'updated_series', 12, 5),
('Popular This Week', 'popular-week', 'Popular This Week', 'popular_week', 12, 6),
('Anime', 'anime', 'Anime', 'category', 12, 7),
('K-Dramas', 'k-dramas', 'K-Dramas', 'category', 12, 8),
('Nollywood', 'nollywood', 'Nollywood', 'country', 12, 9),
('Hollywood', 'hollywood', 'Hollywood', 'country', 12, 10),
('Bollywood', 'bollywood', 'Bollywood', 'country', 12, 11),
('TV Shows', 'tv-shows', 'TV Shows', 'category', 12, 12);

-- Sample Directors
INSERT OR IGNORE INTO directors (id, name, slug, nationality) VALUES
(1, 'Christopher Nolan', 'christopher-nolan', 'British'),
(2, 'Bong Joon-ho', 'bong-joon-ho', 'South Korean'),
(3, 'Kunle Afolayan', 'kunle-afolayan', 'Nigerian'),
(4, 'Rajkumar Hirani', 'rajkumar-hirani', 'Indian');

-- Sample Actors
INSERT OR IGNORE INTO actors (id, name, slug, nationality) VALUES
(1, 'Leonardo DiCaprio', 'leonardo-dicaprio', 'American'),
(2, 'Song Kang', 'song-kang', 'South Korean'),
(3, 'Genevieve Nnaji', 'genevieve-nnaji', 'Nigerian'),
(4, 'Shah Rukh Khan', 'shah-rukh-khan', 'Indian'),
(5, 'Scarlett Johansson', 'scarlett-johansson', 'American');

-- Sample Content
INSERT OR IGNORE INTO content (id, type, category_id, title, original_title, slug, description, poster, banner, runtime, release_date, release_year, imdb_rating, country_id, language_id, status, is_featured, view_count, published_at) VALUES
(1, 'movie', 1, 'Inception', 'Inception', 'inception', 'A thief who steals corporate secrets through dream-sharing technology is given the inverse task of planting an idea.', 'posters/inception.jpg', 'banners/inception.jpg', 148, '2010-07-16', 2010, 8.8, 2, 1, 'published', 1, 15420, datetime('now')),
(2, 'movie', 1, 'Parasite', '기생충', 'parasite', 'Greed and class discrimination threaten the newly formed symbiotic relationship between the wealthy Park family and the destitute Kim clan.', 'posters/parasite.jpg', 'banners/parasite.jpg', 132, '2019-05-30', 2019, 8.5, 4, 6, 'published', 1, 12300, datetime('now')),
(3, 'movie', 1, 'The Figurine', 'Araromire', 'the-figurine', 'A mystical figurine changes the lives of two friends in unexpected ways.', 'posters/figurine.jpg', 'banners/figurine.jpg', 120, '2009-10-30', 2009, 7.2, 1, 1, 'published', 0, 8500, datetime('now')),
(4, 'series', 2, 'Breaking Boundaries', 'Breaking Boundaries', 'breaking-boundaries', 'A gripping drama series following detectives solving complex cases.', 'posters/breaking-boundaries.jpg', 'banners/breaking-boundaries.jpg', NULL, '2023-01-15', 2023, 8.1, 2, 1, 'published', 1, 9800, datetime('now')),
(5, 'anime', 3, 'Celestial Warriors', '天空の戦士', 'celestial-warriors', 'Young warriors protect their realm from ancient evil forces.', 'posters/celestial-warriors.jpg', 'banners/celestial-warriors.jpg', 24, '2024-03-01', 2024, 8.4, 5, 7, 'published', 1, 11200, datetime('now')),
(6, 'k-drama', 4, 'Autumn Hearts', '가을의 마음', 'autumn-hearts', 'Two strangers find love during a transformative autumn season in Seoul.', 'posters/autumn-hearts.jpg', 'banners/autumn-hearts.jpg', NULL, '2024-09-20', 2024, 8.0, 4, 6, 'published', 1, 10500, datetime('now')),
(7, 'movie', 1, '3 Idiots', '3 Idiots', '3-idiots', 'Two friends search for their long lost companion who inspired them to think differently.', 'posters/3-idiots.jpg', 'banners/3-idiots.jpg', 170, '2009-12-25', 2009, 8.4, 3, 5, 'published', 0, 14200, datetime('now')),
(8, 'documentary', 5, 'Planet Earth Revisited', 'Planet Earth Revisited', 'planet-earth-revisited', 'A stunning journey through the worlds most breathtaking natural landscapes.', 'posters/planet-earth.jpg', 'banners/planet-earth.jpg', 90, '2024-06-01', 2024, 9.0, 6, 1, 'published', 0, 6700, datetime('now'));

-- Content Genres
INSERT OR IGNORE INTO content_genres (content_id, genre_id) VALUES
(1, 5), (1, 8), (1, 13),
(2, 5), (2, 6), (2, 4),
(3, 12), (3, 13),
(4, 5), (4, 6),
(5, 7), (5, 10), (5, 12),
(6, 4), (6, 13),
(7, 2), (7, 16),
(8, 11);

-- Content Directors
INSERT OR IGNORE INTO content_directors (content_id, director_id) VALUES
(1, 1), (2, 2), (3, 3), (7, 4);

-- Content Actors
INSERT OR IGNORE INTO content_actors (content_id, actor_id, character_name) VALUES
(1, 1, 'Cobb'), (1, 5, 'Ariadne'),
(2, 2, 'Ki-woo'),
(3, 3, 'Araromire'),
(6, 2, 'Min-jun'),
(7, 4, 'Rancho');

-- Seasons for Breaking Boundaries
INSERT OR IGNORE INTO seasons (id, content_id, season_number, title) VALUES
(1, 4, 1, 'Season 1'),
(2, 4, 2, 'Season 2');

-- Episodes
INSERT OR IGNORE INTO episodes (id, content_id, season_id, episode_number, title, slug, description, runtime, air_date) VALUES
(1, 4, 1, 1, 'Pilot', 'breaking-boundaries-s01e01', 'The team investigates their first major case.', 45, '2023-01-15'),
(2, 4, 1, 2, 'Shadows', 'breaking-boundaries-s01e02', 'A new lead emerges in the cold case.', 42, '2023-01-22'),
(3, 4, 1, 3, 'Revelations', 'breaking-boundaries-s01e03', 'Secrets from the past surface.', 44, '2023-01-29'),
(4, 4, 2, 1, 'New Beginnings', 'breaking-boundaries-s02e01', 'Season two opens with a shocking discovery.', 48, '2024-03-10');

-- Sample Downloads
INSERT OR IGNORE INTO downloads (content_id, title, url, quality, format, server_name) VALUES
(1, 'Inception 1080p', 'https://example.com/dl/inception-1080p.mkv', '1080p', 'mkv', 'Server 1'),
(1, 'Inception 720p', 'https://example.com/dl/inception-720p.mp4', '720p', 'mp4', 'Server 2'),
(2, 'Parasite 1080p', 'https://example.com/dl/parasite-1080p.mkv', '1080p', 'mkv', 'Server 1'),
(3, 'The Figurine 720p', 'https://example.com/dl/figurine-720p.mp4', '720p', 'mp4', 'Server 1');

-- Sample Streaming Links
INSERT OR IGNORE INTO streaming_links (content_id, title, url, provider, quality) VALUES
(1, 'Watch Inception HD', 'https://example.com/stream/inception', 'embed', '1080p'),
(2, 'Watch Parasite HD', 'https://example.com/stream/parasite', 'embed', '1080p'),
(4, 'Watch Breaking Boundaries', 'https://example.com/stream/breaking-boundaries', 'embed', '720p');

-- Featured Content
INSERT OR IGNORE INTO featured_content (content_id, section, sort_order) VALUES
(1, 'hero', 1), (2, 'hero', 2), (5, 'hero', 3),
(1, 'featured', 1), (2, 'featured', 2), (6, 'featured', 3);

-- Sample Announcement
INSERT OR IGNORE INTO announcements (title, body, type, is_active) VALUES
('Welcome to DzieKas!', 'Your new entertainment portal is ready. Enjoy the latest movies and shows.', 'info', 1);
