-- DzieKas Entertainment Portal - SQLite Schema
-- Normalized database with foreign keys and indexes

PRAGMA foreign_keys = ON;

-- Roles & Permissions
CREATE TABLE IF NOT EXISTS roles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS permissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS role_permissions (
    role_id INTEGER NOT NULL,
    permission_id INTEGER NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

-- Users
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    role_id INTEGER NOT NULL DEFAULT 3,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    display_name VARCHAR(100),
    avatar VARCHAR(255),
    bio TEXT,
    is_active INTEGER DEFAULT 1,
    email_verified_at DATETIME,
    reset_token VARCHAR(255),
    reset_token_expires DATETIME,
    last_login_at DATETIME,
    last_login_ip VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);

-- Taxonomy
CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    parent_id INTEGER,
    sort_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS genres (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS countries (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    code VARCHAR(3),
    flag_emoji VARCHAR(10),
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS languages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    code VARCHAR(10),
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tags (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- People
CREATE TABLE IF NOT EXISTS actors (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    bio TEXT,
    photo VARCHAR(255),
    birth_date DATE,
    nationality VARCHAR(100),
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_actors_slug ON actors(slug);

CREATE TABLE IF NOT EXISTS directors (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    bio TEXT,
    photo VARCHAR(255),
    birth_date DATE,
    nationality VARCHAR(100),
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_directors_slug ON directors(slug);

-- Content: Movies & Media
CREATE TABLE IF NOT EXISTS content (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type VARCHAR(30) NOT NULL DEFAULT 'movie',
    category_id INTEGER,
    title VARCHAR(255) NOT NULL,
    original_title VARCHAR(255),
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    synopsis TEXT,
    poster VARCHAR(255),
    banner VARCHAR(255),
    trailer_url VARCHAR(500),
    runtime INTEGER,
    release_date DATE,
    release_year INTEGER,
    imdb_id VARCHAR(20),
    imdb_rating REAL,
    tmdb_id VARCHAR(20),
    country_id INTEGER,
    language_id INTEGER,
    status VARCHAR(20) DEFAULT 'published',
    is_featured INTEGER DEFAULT 0,
    is_trending INTEGER DEFAULT 0,
    is_editors_pick INTEGER DEFAULT 0,
    view_count INTEGER DEFAULT 0,
    download_count INTEGER DEFAULT 0,
    like_count INTEGER DEFAULT 0,
    user_rating_avg REAL DEFAULT 0,
    user_rating_count INTEGER DEFAULT 0,
    reading_time INTEGER,
    published_at DATETIME,
    video_path VARCHAR(255),
    video_type VARCHAR(20) DEFAULT 'upload',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE SET NULL,
    FOREIGN KEY (language_id) REFERENCES languages(id) ON DELETE SET NULL
);

CREATE INDEX idx_content_slug ON content(slug);
CREATE INDEX idx_content_type ON content(type);
CREATE INDEX idx_content_status ON content(status);
CREATE INDEX idx_content_release_year ON content(release_year);
CREATE INDEX idx_content_view_count ON content(view_count);
CREATE INDEX idx_content_published_at ON content(published_at);
CREATE INDEX idx_content_featured ON content(is_featured);

-- Series Seasons
CREATE TABLE IF NOT EXISTS seasons (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    content_id INTEGER NOT NULL,
    season_number INTEGER NOT NULL,
    title VARCHAR(255),
    description TEXT,
    poster VARCHAR(255),
    release_date DATE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE,
    UNIQUE(content_id, season_number)
);

-- Episodes
CREATE TABLE IF NOT EXISTS episodes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    content_id INTEGER NOT NULL,
    season_id INTEGER NOT NULL,
    episode_number INTEGER NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT,
    poster VARCHAR(255),
    runtime INTEGER,
    air_date DATE,
    view_count INTEGER DEFAULT 0,
    video_path VARCHAR(255),
    video_type VARCHAR(20) DEFAULT 'upload',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE,
    FOREIGN KEY (season_id) REFERENCES seasons(id) ON DELETE CASCADE,
    UNIQUE(season_id, episode_number)
);

CREATE INDEX idx_episodes_content ON episodes(content_id);
CREATE INDEX idx_episodes_slug ON episodes(slug);

-- Pivot Tables
CREATE TABLE IF NOT EXISTS content_genres (
    content_id INTEGER NOT NULL,
    genre_id INTEGER NOT NULL,
    PRIMARY KEY (content_id, genre_id),
    FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS content_actors (
    content_id INTEGER NOT NULL,
    actor_id INTEGER NOT NULL,
    character_name VARCHAR(200),
    sort_order INTEGER DEFAULT 0,
    PRIMARY KEY (content_id, actor_id),
    FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE,
    FOREIGN KEY (actor_id) REFERENCES actors(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS content_directors (
    content_id INTEGER NOT NULL,
    director_id INTEGER NOT NULL,
    PRIMARY KEY (content_id, director_id),
    FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE,
    FOREIGN KEY (director_id) REFERENCES directors(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS content_tags (
    content_id INTEGER NOT NULL,
    tag_id INTEGER NOT NULL,
    PRIMARY KEY (content_id, tag_id),
    FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- Media Assets
CREATE TABLE IF NOT EXISTS screenshots (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    content_id INTEGER NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    caption VARCHAR(255),
    sort_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS trailers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    content_id INTEGER NOT NULL,
    title VARCHAR(255),
    url VARCHAR(500) NOT NULL,
    provider VARCHAR(50) DEFAULT 'youtube',
    sort_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE
);

-- Downloads & Streaming
CREATE TABLE IF NOT EXISTS downloads (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    content_id INTEGER,
    episode_id INTEGER,
    title VARCHAR(255) NOT NULL,
    url VARCHAR(1000) NOT NULL,
    quality VARCHAR(20),
    size VARCHAR(50),
    format VARCHAR(20),
    server_name VARCHAR(100),
    is_active INTEGER DEFAULT 1,
    download_count INTEGER DEFAULT 0,
    sort_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE,
    FOREIGN KEY (episode_id) REFERENCES episodes(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS streaming_links (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    content_id INTEGER,
    episode_id INTEGER,
    title VARCHAR(255) NOT NULL,
    url VARCHAR(1000) NOT NULL,
    provider VARCHAR(50),
    quality VARCHAR(20),
    is_active INTEGER DEFAULT 1,
    sort_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE,
    FOREIGN KEY (episode_id) REFERENCES episodes(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS subtitles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    content_id INTEGER,
    episode_id INTEGER,
    language VARCHAR(50) NOT NULL,
    file_path VARCHAR(255),
    url VARCHAR(1000),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE,
    FOREIGN KEY (episode_id) REFERENCES episodes(id) ON DELETE CASCADE
);

-- User Interactions
CREATE TABLE IF NOT EXISTS ratings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    content_id INTEGER NOT NULL,
    rating INTEGER NOT NULL CHECK(rating >= 1 AND rating <= 10),
    review TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE,
    UNIQUE(user_id, content_id)
);

CREATE TABLE IF NOT EXISTS comments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    content_id INTEGER NOT NULL,
    parent_id INTEGER,
    body TEXT NOT NULL,
    is_approved INTEGER DEFAULT 1,
    like_count INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE
);

CREATE INDEX idx_comments_content ON comments(content_id);

CREATE TABLE IF NOT EXISTS bookmarks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    content_id INTEGER NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE,
    UNIQUE(user_id, content_id)
);

CREATE TABLE IF NOT EXISTS watch_history (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    content_id INTEGER NOT NULL,
    episode_id INTEGER,
    progress INTEGER DEFAULT 0,
    completed INTEGER DEFAULT 0,
    watched_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE,
    FOREIGN KEY (episode_id) REFERENCES episodes(id) ON DELETE CASCADE
);

CREATE INDEX idx_watch_history_user ON watch_history(user_id);

CREATE TABLE IF NOT EXISTS content_likes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    content_id INTEGER NOT NULL,
    ip_address VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS link_reports (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    download_id INTEGER,
    streaming_link_id INTEGER,
    reason TEXT,
    status VARCHAR(20) DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (download_id) REFERENCES downloads(id) ON DELETE CASCADE,
    FOREIGN KEY (streaming_link_id) REFERENCES streaming_links(id) ON DELETE CASCADE
);

-- Featured & Announcements
CREATE TABLE IF NOT EXISTS featured_content (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    content_id INTEGER NOT NULL,
    section VARCHAR(50) NOT NULL,
    sort_order INTEGER DEFAULT 0,
    starts_at DATETIME,
    ends_at DATETIME,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (content_id) REFERENCES content(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS announcements (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    body TEXT,
    type VARCHAR(30) DEFAULT 'info',
    is_active INTEGER DEFAULT 1,
    starts_at DATETIME,
    ends_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Advertisements
CREATE TABLE IF NOT EXISTS advertisements (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    position VARCHAR(50) NOT NULL,
    ad_code TEXT,
    image_url VARCHAR(500),
    link_url VARCHAR(500),
    alt_text VARCHAR(255),
    is_active INTEGER DEFAULT 1,
    impressions INTEGER DEFAULT 0,
    clicks INTEGER DEFAULT 0,
    starts_at DATETIME,
    ends_at DATETIME,
    sort_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_ads_position ON advertisements(position);

-- SEO
CREATE TABLE IF NOT EXISTS seo_metadata (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    entity_type VARCHAR(50) NOT NULL,
    entity_id INTEGER NOT NULL,
    meta_title VARCHAR(255),
    meta_description TEXT,
    meta_keywords VARCHAR(500),
    canonical_url VARCHAR(500),
    og_image VARCHAR(500),
    noindex INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(entity_type, entity_id)
);

-- Site Settings
CREATE TABLE IF NOT EXISTS site_settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    key VARCHAR(100) NOT NULL UNIQUE,
    value TEXT,
    type VARCHAR(20) DEFAULT 'string',
    group_name VARCHAR(50) DEFAULT 'general',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Newsletter
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    is_active INTEGER DEFAULT 1,
    subscribed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at DATETIME
);

-- Notifications
CREATE TABLE IF NOT EXISTS notifications (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT,
    type VARCHAR(30) DEFAULT 'info',
    link VARCHAR(500),
    is_read INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX idx_notifications_user ON notifications(user_id);

-- Audit Logs
CREATE TABLE IF NOT EXISTS audit_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INTEGER,
    old_values TEXT,
    new_values TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE INDEX idx_audit_logs_user ON audit_logs(user_id);
CREATE INDEX idx_audit_logs_created ON audit_logs(created_at);

-- Homepage Sections Config
CREATE TABLE IF NOT EXISTS homepage_sections (
     id INTEGER PRIMARY KEY AUTOINCREMENT,
     name VARCHAR(100) NOT NULL,
     slug VARCHAR(100) NOT NULL UNIQUE,
     title VARCHAR(255),
     query_type VARCHAR(50) NOT NULL,
     query_params TEXT,
     limit_count INTEGER DEFAULT 12,
     sort_order INTEGER DEFAULT 0,
     is_active INTEGER DEFAULT 1,
     created_at DATETIME DEFAULT CURRENT_TIMESTAMP
 );

-- Media Library
CREATE TABLE IF NOT EXISTS media_folders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    parent_id INTEGER DEFAULT NULL,
    path VARCHAR(1000) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES media_folders(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS media_files (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    folder_id INTEGER NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(500) NOT NULL,
    mime_type VARCHAR(100),
    file_size INTEGER DEFAULT 0,
    path VARCHAR(1000) NOT NULL,
    thumbnail_path VARCHAR(1000),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (folder_id) REFERENCES media_folders(id) ON DELETE CASCADE
);

CREATE INDEX idx_media_folders_parent ON media_folders(parent_id);
CREATE INDEX idx_media_files_folder ON media_files(folder_id);
