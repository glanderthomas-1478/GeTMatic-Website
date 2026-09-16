-- Migration: Ordnerstruktur fuer den internen Bereich (2026-09-16)
-- Einmalig im 1blu-Datenbank-Tool (phpMyAdmin o.ae.) auf der BESTEHENDEN
-- Live-Datenbank ausfuehren (schema.sql wurde dort bereits ausgefuehrt).
--
-- Bestehende Zeilen in intern_files/intern_upload_sessions brauchen kein
-- UPDATE: folder_id ist NULL per Default, das entspricht automatisch der
-- Wurzelebene - alle bisherigen Dateien bleiben ohne Datenverlust sichtbar.

CREATE TABLE intern_folders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    parent_id INT NULL,
    created_by INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES intern_folders(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES intern_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE intern_files
    ADD COLUMN folder_id INT NULL AFTER uploaded_by,
    ADD FOREIGN KEY (folder_id) REFERENCES intern_folders(id) ON DELETE SET NULL;

ALTER TABLE intern_upload_sessions
    ADD COLUMN folder_id INT NULL AFTER created_by,
    ADD FOREIGN KEY (folder_id) REFERENCES intern_folders(id) ON DELETE SET NULL;
