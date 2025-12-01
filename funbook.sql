-- Buat database
CREATE DATABASE IF NOT EXISTS funbook;
USE funbook;

-- Tabel users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    bio TEXT,
    profile_picture VARCHAR(255) DEFAULT 'default.jpg',
    cover_photo VARCHAR(255),
    is_admin TINYINT(1) DEFAULT 0,
    is_banned TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel posts dengan content_type
CREATE TABLE posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    content TEXT,
    image VARCHAR(255),
    content_type ENUM('post', 'meme', 'video_post') DEFAULT 'post',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel reels dengan content_type
CREATE TABLE reels (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    video VARCHAR(255) NOT NULL,
    caption TEXT,
    content_type ENUM('reel', 'profile_video') DEFAULT 'reel',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel likes
CREATE TABLE post_likes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    post_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    UNIQUE (user_id, post_id)
);

-- Tabel comments
CREATE TABLE comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    post_id INT,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- Tabel friends
CREATE TABLE friends (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id1 INT,
    user_id2 INT,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id1) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id2) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel groups
CREATE TABLE groups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel group_members
CREATE TABLE group_members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    group_id INT,
    user_id INT,
    role VARCHAR(20) DEFAULT 'member',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE (group_id, user_id)
);

-- Reels like
CREATE TABLE reel_likes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    reel_id INT NOT NULL,
    type ENUM('like','dislike') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (user_id, reel_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reel_id) REFERENCES reels(id) ON DELETE CASCADE
);

-- Insert user admin
INSERT INTO users (username, email, password, full_name, is_admin) VALUES 
('admin', 'admin@funbook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 1);

-- Insert sample users
INSERT INTO users (username, email, password, full_name) VALUES 
('user1', 'user1@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'User Satu'),
('user2', 'user2@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'User Dua');

-- Insert sample posts dengan berbagai content_type
INSERT INTO posts (user_id, content, image, content_type) VALUES 
(1, 'Halo! Selamat datang di Funbook!', NULL, 'post'),
(2, 'Meme lucu hari ini!', 'meme1.jpg', 'meme'),
(3, 'Video keren dari perjalanan saya!', 'video1.mp4', 'video_post');

-- Insert sample reels
INSERT INTO reels (user_id, video, caption, content_type) VALUES 
(1, 'reel1.mp4', 'Reel pertama saya!', 'reel'),
(2, 'profile_video1.mp4', 'Video dari profile saya', 'profile_video');

-- Insert sample groups
INSERT INTO groups (name, description, created_by) VALUES 
('Programmer Indonesia', 'Komunitas programmer Indonesia', 1),
('Gaming Community', 'Komunitas gamers Indonesia', 1);

-- Insert sample group members
INSERT INTO group_members (group_id, user_id, role) VALUES 
(1, 1, 'admin'),
(1, 2, 'member'),
(1, 3, 'member'),
(2, 1, 'admin'),
(2, 3, 'moderator');

-- Insert sample likes
INSERT INTO post_likes (user_id, post_id) VALUES 
(2, 1),
(3, 1),
(1, 2);

-- Insert sample comments
INSERT INTO comments (user_id, post_id, content) VALUES 
(2, 1, 'Selamat datang admin!'),
(3, 1, 'Funbook memang keren!'),
(1, 2, 'Selamat bergabung!');

-- Insert sample friends
INSERT INTO friends (user_id1, user_id2, status) VALUES 
(1, 2, 'accepted'),
(1, 3, 'pending');