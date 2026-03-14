-- Run this once in your MySQL (phpMyAdmin) for DB art_gallery
ALTER TABLE users
  ADD COLUMN role ENUM('admin','artist','customer') NOT NULL DEFAULT 'customer';
-- Example: make your account an admin
-- UPDATE users SET role='admin' WHERE email='you@example.com';
