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
  category_id INT,
  thumbnail VARCHAR(255),
  year YEAR,
  duration VARCHAR(50),
  video_url VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
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

-- Thể loại mẫu
INSERT INTO categories (name, description) VALUES
('Hành động', 'Phim hành động gay cấn'),
('Tình cảm', 'Phim lãng mạn'),
('Hài', 'Phim hài hước'),
('Kinh dị', 'Phim kinh dị rùng rợn'),
('Viễn tưởng', 'Phim khoa học viễn tưởng');

-- Phim mẫu
INSERT INTO movies (title, description, category_id, thumbnail, year, duration, video_url) VALUES
('Cuộc đua tử thần', 'Phim hành động tốc độ cao.', 1, 'https://picsum.photos/400/600?random=901', 2024, '120 phút', 'https://example.com/video1.mp4'),
('Tình yêu mùa hạ', 'Chuyện tình đôi bạn trẻ.', 2, 'https://picsum.photos/400/600?random=902', 2023, '105 phút', 'https://example.com/video2.mp4'),
('Cười lên nào', 'Tiếng cười sảng khoái.', 3, 'https://picsum.photos/400/600?random=903', 2022, '95 phút', 'https://example.com/video3.mp4'),
('Đêm ám ảnh', 'Nỗi sợ hãi bao trùm.', 4, 'https://picsum.photos/400/600?random=904', 2025, '110 phút', 'https://example.com/video4.mp4'),
('Không gian vô tận', 'Phiêu lưu giữa vũ trụ.', 5, 'https://picsum.photos/400/600?random=905', 2021, '130 phút', 'https://example.com/video5.mp4');

