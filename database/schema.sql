-- Lost and Found Management System
-- PostgreSQL Database
-- Student-friendly version

-- Create the database manually first:
-- CREATE DATABASE lostfound_db;

-- Connect to lostfound_db before running the remaining commands.

-- 1. Users table
CREATE TABLE users (
    user_id SERIAL PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(120) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user'
        CHECK (role IN ('user', 'admin')),
    status VARCHAR(20) DEFAULT 'active'
        CHECK (status IN ('active', 'blocked')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Categories table
CREATE TABLE categories (
    category_id SERIAL PRIMARY KEY,
    category_name VARCHAR(50) UNIQUE NOT NULL
);

-- 3. Items table
CREATE TABLE items (
    item_id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    item_name VARCHAR(120) NOT NULL,
    item_type VARCHAR(10) NOT NULL
        CHECK (item_type IN ('lost', 'found')),
    color VARCHAR(50),
    item_date DATE NOT NULL,
    location VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    image_name VARCHAR(255),
    status VARCHAR(20) DEFAULT 'open'
        CHECK (status IN ('open', 'matched', 'returned', 'closed')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_item_user
        FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE,

    CONSTRAINT fk_item_category
        FOREIGN KEY (category_id)
        REFERENCES categories(category_id)
        ON DELETE RESTRICT
);

-- 4. Claims table
CREATE TABLE claims (
    claim_id SERIAL PRIMARY KEY,
    item_id INT NOT NULL,
    claimant_id INT NOT NULL,
    claim_message TEXT NOT NULL,
    proof_details TEXT,
    claim_status VARCHAR(20) DEFAULT 'pending'
        CHECK (claim_status IN ('pending', 'approved', 'rejected')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_claim_item
        FOREIGN KEY (item_id)
        REFERENCES items(item_id)
        ON DELETE CASCADE,

    CONSTRAINT fk_claim_user
        FOREIGN KEY (claimant_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE
);

-- 5. Contact messages table
CREATE TABLE contact_messages (
    message_id SERIAL PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL,
    subject VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    message_status VARCHAR(20) DEFAULT 'unread'
        CHECK (message_status IN ('unread', 'read', 'replied')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6. Notifications table
CREATE TABLE notifications (
    notification_id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    message VARCHAR(255) NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_notification_user
        FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE
);

-- Insert default categories
INSERT INTO categories (category_name) VALUES
('Electronics'),
('Documents'),
('Bags'),
('Keys'),
('Books'),
('Accessories'),
('Clothing'),
('Other');

-- Optional admin account
-- Password must be hashed using PHP password_hash().
-- Replace the sample password value before using.
INSERT INTO users (
    full_name,
    email,
    phone,
    password,
    role
) VALUES (
    'System Admin',
    'admin@lostfound.com',
    '9800000000',
    '$2y$10$replace_this_with_a_real_php_password_hash',
    'admin'
);

-- Useful indexes
CREATE INDEX idx_items_type ON items(item_type);
CREATE INDEX idx_items_status ON items(status);
CREATE INDEX idx_items_category ON items(category_id);
CREATE INDEX idx_items_created_at ON items(created_at);
CREATE INDEX idx_claims_status ON claims(claim_status);
