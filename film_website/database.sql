-- Tạo DB và bảng
CREATE DATABASE IF NOT EXISTS film_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE film_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','user') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS movies (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  thumbnail VARCHAR(255),
  year YEAR,
  duration VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng trung gian cho quan hệ nhiều-nhiều giữa movies và categories
CREATE TABLE IF NOT EXISTS movie_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  movie_id INT NOT NULL,
  category_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
  UNIQUE KEY unique_movie_category (movie_id, category_id)
);

-- Bảng lưu trữ các tập phim
CREATE TABLE IF NOT EXISTS episodes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  movie_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  episode_number INT NOT NULL,
  description TEXT,
  video_url VARCHAR(255),
  duration VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
  UNIQUE KEY unique_movie_episode (movie_id, episode_number)
);

CREATE TABLE IF NOT EXISTS favorites (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  movie_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (movie_id) REFERENCES movies(id)
);

-- Admin mặc định
INSERT INTO users (username, email, password, role)
VALUES ('admin', 'admin@gmail.com', MD5('admin123'), 'admin')
ON DUPLICATE KEY UPDATE email=email;




-- Bổ sung, chỉnh sửa về sau
ALTER TABLE favorites DROP FOREIGN KEY favorites_ibfk_2;
ALTER TABLE favorites
  ADD CONSTRAINT favorites_ibfk_2
  FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE;



ALTER TABLE movies
  ADD COLUMN video_url TEXT NULL AFTER duration;