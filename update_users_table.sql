-- Add address field to users table if it doesn't exist
ALTER TABLE users ADD COLUMN address TEXT AFTER phone;