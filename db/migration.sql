-- Non-destructive migration suggestions for StudentPortal
-- Review each statement before running. Back up your data first.

USE studentportal;

-- 1) Add indexes if missing
ALTER TABLE users
  ADD COLUMN IF NOT EXISTS idx_role_tmp VARCHAR(1) GENERATED ALWAYS AS (role) VIRTUAL; -- placeholder

-- Note: MySQL doesn't support ADD COLUMN IF NOT EXISTS for generated columns in older versions.
-- Instead run the specific ALTERs below only if your table lacks the index/column.

-- Add indexes (example):
-- ALTER TABLE users ADD INDEX idx_users_role (role);
-- ALTER TABLE users ADD INDEX idx_users_status (status);
-- ALTER TABLE students ADD INDEX idx_students_user (user_id);
-- ALTER TABLE students ADD INDEX idx_students_email (email);
-- ALTER TABLE events ADD INDEX idx_events_date (date);
-- ALTER TABLE events ADD INDEX idx_events_status (status);

-- 2) Change engine/charset if necessary
-- ALTER TABLE users CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, ENGINE=InnoDB;
-- ALTER TABLE students CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, ENGINE=InnoDB;
-- ALTER TABLE events CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, ENGINE=InnoDB;

-- 3) Ensure created_at is NOT NULL with default
-- ALTER TABLE users MODIFY created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
-- ALTER TABLE students MODIFY created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
-- ALTER TABLE events MODIFY created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

-- 4) Example: add a default admin account if it doesn't exist
INSERT INTO users (username, email, password, role, status)
SELECT 'admin', 'admin@example.com', '$2y$10$u8K1s9dFh3pQmL2zVxY4Ou6q7w8e9r0tABCDefghijkLMNOPQRstu', 'admin', 'active'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'admin' OR email = 'admin@example.com');
