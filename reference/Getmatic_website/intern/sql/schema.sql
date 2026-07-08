-- Interner Mitarbeiterbereich (intern/) — Datenbankschema
-- Einmalig im 1blu-Datenbank-Tool (phpMyAdmin o.ä.) ausfuehren.
-- Alle Tabellen mit Praefix "intern_", um Kollisionen mit anderen
-- Anwendungen in derselben Datenbank zu vermeiden.

CREATE TABLE intern_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- filesize_bytes bewusst BIGINT statt INT: bei bis zu 20 GB pro Datei
-- (~21.5 Milliarden Bytes) wuerde ein normaler INT (max. ~2.1 Milliarden) ueberlaufen.
CREATE TABLE intern_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_filename VARCHAR(255) NOT NULL,
    stored_filename VARCHAR(64) NOT NULL UNIQUE,
    mime_type VARCHAR(100) NOT NULL,
    filesize_bytes BIGINT UNSIGNED NOT NULL,
    uploaded_by INT NOT NULL,
    uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES intern_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE intern_login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    success TINYINT(1) NOT NULL,
    INDEX idx_username_time (username, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE intern_upload_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(64) NOT NULL UNIQUE,
    original_filename VARCHAR(255) NOT NULL,
    total_size_bytes BIGINT UNSIGNED NOT NULL,
    received_bytes BIGINT UNSIGNED NOT NULL DEFAULT 0,
    created_by INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('in_progress','completed','failed') NOT NULL DEFAULT 'in_progress',
    FOREIGN KEY (created_by) REFERENCES intern_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
