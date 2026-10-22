-- NurseryNest: create the database and the application account.
-- Run once as an administrative MySQL user (for example root) in MySQL Workbench.
-- Replace CHANGE_ME_STRONG_PASSWORD with a strong password and put the same value in .env (DB_PASS).

CREATE DATABASE IF NOT EXISTS nurserynest
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_0900_ai_ci;

-- Least-privilege application account, never root (NFR-10).
CREATE USER IF NOT EXISTS 'nurserynest_app'@'localhost' IDENTIFIED BY 'CHANGE_ME_STRONG_PASSWORD';

GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE ON nurserynest.* TO 'nurserynest_app'@'localhost';

FLUSH PRIVILEGES;
