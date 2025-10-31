-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 18, 2025 at 09:23 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `film_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Action', 'Phim có nhiều cảnh chiến đấu, rượt đuổi, bắn súng, hoặc các pha nguy hiểm gay cấn. Thường mang đến cảm giác hồi hộp và kịch tính.', '2025-10-17 19:30:10'),
(2, 'Adventure', 'Nhân vật chính thường trải qua hành trình khám phá, vượt qua thử thách ở những vùng đất xa lạ hoặc bí ẩn.', '2025-10-17 19:30:56'),
(3, 'Drama', 'Khai thác chiều sâu cảm xúc, mối quan hệ và xung đột trong cuộc sống con người.', '2025-10-17 19:31:27'),
(4, 'Romance', 'Tập trung vào câu chuyện tình yêu, cảm xúc và mối quan hệ giữa các nhân vật.', '2025-10-17 19:32:04'),
(5, 'Horror', 'Gây sợ hãi, căng thẳng bằng yếu tố ma quỷ, tâm linh, hoặc những tình huống rùng rợn.', '2025-10-17 19:32:30'),
(6, 'Thriller', 'Đưa người xem vào cảm giác hồi hộp, căng thẳng với các bí ẩn, âm mưu hoặc tội ác cần khám phá.', '2025-10-17 19:33:06'),
(7, 'Science Fiction – Sci-Fi', 'Khai thác ý tưởng về tương lai, công nghệ, không gian, người máy hoặc thế giới ngoài hành tinh.', '2025-10-17 19:33:31'),
(8, 'Fantasy', 'Thế giới phép thuật, sinh vật huyền bí và các yếu tố siêu nhiên không có thật trong đời thực.', '2025-10-17 19:34:19'),
(9, 'Crime', 'Khai thác thế giới ngầm, các vụ cướp, mafia hoặc điều tra hình sự.', '2025-10-17 19:34:57'),
(10, 'Comedy', 'Tập trung vào tình huống vui nhộn, lời thoại dí dỏm, giúp người xem thư giãn và cười sảng khoái.', '2025-10-18 17:24:50'),
(11, 'Animation', 'Phim được sản xuất bằng kỹ thuật vẽ hoặc dựng hình 3D, thường dành cho mọi lứa tuổi.', '2025-10-18 17:25:33'),
(12, 'Mystery', 'Tập trung vào việc giải mã vụ án hoặc khám phá sự thật ẩn giấu.', '2025-10-18 17:27:00');

-- --------------------------------------------------------

--
-- Table structure for table `episodes`
--

CREATE TABLE `episodes` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `episode_number` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `episodes`
--

INSERT INTO `episodes` (`id`, `movie_id`, `title`, `episode_number`, `description`, `video_url`, `duration`, `created_at`) VALUES
(5, 12, 'Tập 1', 1, 'Cuộc hành trình kỳ vĩ của bốn thầy trò Đường Tăng vượt muôn trùng hiểm nguy, diệt trừ yêu ma, cảm hóa lòng người và tìm đến Tây Thiên để thỉnh chân kinh.', 'https://youtu.be/hGhdUz8A4OI?si=FM7GfRgEKsztddZq', '45 phút', '2025-10-18 18:27:40'),
(6, 12, 'Tập 2', 2, 'Cuộc hành trình kỳ vĩ của bốn thầy trò Đường Tăng vượt muôn trùng hiểm nguy, diệt trừ yêu ma, cảm hóa lòng người và tìm đến Tây Thiên để thỉnh chân kinh.', 'https://youtu.be/v6skZ0v2fgU?si=NmaiFNFhoXoZ0cbM', '45 phút', '2025-10-18 18:27:40'),
(7, 12, 'Tập 3', 3, 'Cuộc hành trình kỳ vĩ của bốn thầy trò Đường Tăng vượt muôn trùng hiểm nguy, diệt trừ yêu ma, cảm hóa lòng người và tìm đến Tây Thiên để thỉnh chân kinh.', 'https://youtu.be/OlFMQPh8dOY?si=NmH3R5c54_GSZ00I', '45 phút', '2025-10-18 18:27:40'),
(8, 12, 'Tập 4', 4, 'Cuộc hành trình kỳ vĩ của bốn thầy trò Đường Tăng vượt muôn trùng hiểm nguy, diệt trừ yêu ma, cảm hóa lòng người và tìm đến Tây Thiên để thỉnh chân kinh.', 'https://youtu.be/KUUWJFeScQE?si=LbaTaB_ueEeABuPn', '45 phút', '2025-10-18 18:27:40'),
(9, 12, 'Tập 5', 5, 'Cuộc hành trình kỳ vĩ của bốn thầy trò Đường Tăng vượt muôn trùng hiểm nguy, diệt trừ yêu ma, cảm hóa lòng người và tìm đến Tây Thiên để thỉnh chân kinh.', 'https://youtu.be/D7_cf0TP3oA?si=hzWXl7rqO83y9ql0', '45 phút', '2025-10-18 18:27:40'),
(10, 12, 'Tập 6', 6, 'Cuộc hành trình kỳ vĩ của bốn thầy trò Đường Tăng vượt muôn trùng hiểm nguy, diệt trừ yêu ma, cảm hóa lòng người và tìm đến Tây Thiên để thỉnh chân kinh.', 'https://youtu.be/bI-bez2Bc1o?si=o-GK7emdty3MbZAe', '45 phút', '2025-10-18 18:27:40'),
(11, 12, 'Tập 7', 7, 'Cuộc hành trình kỳ vĩ của bốn thầy trò Đường Tăng vượt muôn trùng hiểm nguy, diệt trừ yêu ma, cảm hóa lòng người và tìm đến Tây Thiên để thỉnh chân kinh.', 'https://youtu.be/0Ifmwka6JZQ?si=F3ATDKdBfd5YNeUl', '45 phút', '2025-10-18 18:27:40'),
(12, 12, 'Tập 8', 8, 'Cuộc hành trình kỳ vĩ của bốn thầy trò Đường Tăng vượt muôn trùng hiểm nguy, diệt trừ yêu ma, cảm hóa lòng người và tìm đến Tây Thiên để thỉnh chân kinh.', 'https://youtu.be/aSbngiIj1Zs?si=FB8Tyba62YxMMb0A', '45 phút', '2025-10-18 18:27:40'),
(13, 12, 'Tập 9', 9, 'Cuộc hành trình kỳ vĩ của bốn thầy trò Đường Tăng vượt muôn trùng hiểm nguy, diệt trừ yêu ma, cảm hóa lòng người và tìm đến Tây Thiên để thỉnh chân kinh.', 'https://youtu.be/nFYVyYQ53vY?si=_2r_YiK2w3fkQrDJ', '45 phút', '2025-10-18 18:27:40'),
(14, 12, 'Tập 10', 10, 'Cuộc hành trình kỳ vĩ của bốn thầy trò Đường Tăng vượt muôn trùng hiểm nguy, diệt trừ yêu ma, cảm hóa lòng người và tìm đến Tây Thiên để thỉnh chân kinh.', 'https://youtu.be/4onNDlHX2p0?si=C6T-N9MxQh-gQqLm', '45 phút', '2025-10-18 18:27:40');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `movie_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `video_url` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `title`, `description`, `thumbnail`, `year`, `duration`, `video_url`, `created_at`) VALUES
(4, 'IP Man 1', 'Phim kể về Diệp Vấn, một cao thủ Vịnh Xuân quyền sống ở Phật Sơn vào thập niên 1930. Khi chiến tranh Trung – Nhật bùng nổ, ông mất hết tài sản và bị chiếm đóng. Dù hoàn cảnh khó khăn, Diệp Vấn vẫn giữ vững lòng tự trọng và tinh thần võ đạo, dùng kungfu để bảo vệ người dân và khẳng định tinh thần dân tộc Trung Hoa.', 'https://i.pinimg.com/736x/c6/e0/a8/c6e0a8c93a22d4895eaea22a3923b0f5.jpg', '2008', '102 phút', 'https://youtu.be/CF0lMPJYDpc?si=meTFrwDqERxRXaD-', '2025-10-18 07:38:56'),
(6, 'IP Man 2', 'Sau khi rời Phật Sơn, Diệp Vấn đến Hồng Kông để mở võ quán và truyền dạy Vịnh Xuân quyền. Tuy nhiên, ông phải đối mặt với sự cạnh tranh gay gắt từ các võ sư địa phương và cả sự kỳ thị của người Anh. Cuối cùng, Diệp Vấn chứng minh tinh thần võ đạo và lòng tự tôn dân tộc qua trận đấu kinh điển với võ sĩ quyền Anh Twister.', 'https://i.pinimg.com/736x/e6/66/cd/e666cde66a4e809d39e061f33d393166.jpg', '2010', '105 phút', 'https://youtu.be/YBD5Do3o_aM?si=vogJgNMX0O0puT3Q', '2025-10-18 07:42:43'),
(7, 'IP Man 3', 'Bộ phim kể về giai đoạn sau khi Diệp Vấn đã nổi tiếng ở Hong Kong, ông phải đối mặt với một băng nhóm tội phạm do Mike Tyson thủ vai trùm xã hội đen cầm đầu, âm mưu chiếm trường học nơi con trai ông theo học. Bên cạnh những trận đấu võ Wing Chun mãn nhãn, phim còn khắc họa sâu sắc tình cảm gia đình và tinh thần võ đạo của Diệp Vấn – coi trọng nghĩa, lý và lòng nhân hơn là sức mạnh.', 'https://i.pinimg.com/736x/3d/37/5a/3d375abd219f30e56936749c260466e4.jpg', '2015', '100 phút', 'https://youtu.be/eO9IdjHj0Qk?si=PgolVbAyI40TxPGW', '2025-10-18 07:44:53'),
(8, 'IP Man 4', 'Sau khi vợ mất, Diệp Vấn sang Mỹ để tìm trường học cho con trai và tình cờ bị cuốn vào xung đột giữa cộng đồng người Hoa và võ sĩ người Mỹ. Tại đây, ông phải đối mặt với sự phân biệt đối xử và dùng Vịnh Xuân Quyền để bảo vệ danh dự của người Trung Hoa. Phim khép lại hành trình của Diệp Vấn với thông điệp về lòng kiên định, tôn trọng và tinh thần võ đạo chân chính.', 'https://i.pinimg.com/736x/18/b6/a4/18b6a454405ea69adfcabf61e3062a74.jpg', '2019', '101 phút', 'https://youtu.be/H0Xt-sOf9NA?si=CJIQ2wKYfEWbkHLW', '2025-10-18 07:46:34'),
(9, 'Tee Yod 1', 'Một cô gái trẻ ở một ngôi làng hẻo lánh bị linh hồn ám nhập, nghe tiếng rợn người “Tee Yod… Tee Yod…” vang lên trong đêm. Anh trai của cô – Yak – quay về gia đình và cùng pháp sư cố gắng trục xuất linh hồn trước khi cái ác chiếm lấy em gái.', 'https://i.pinimg.com/736x/b7/a1/61/b7a1618a273f7cbc49c3863bd67c6549.jpg', '2023', '120 phút', 'https://youtu.be/iR933CLpVtE?si=9ZLMS7pf-kDXFZiR', '2025-10-18 07:57:34'),
(10, 'Điệp viên 007', 'Phim là một parody (hài nhại) theo phong cách James Bond, kể về điệp viên ngu ngơ Ling Ling Chat (007) được cử điều tra vụ trộm đầu hóa thạch khủng long quốc gia. Trong quá trình làm nhiệm vụ, anh gặp nhiều tình huống dở khóc dở cười, vừa hành động vừa hài hước đậm chất Châu Tinh Trì, châm biếm phim điệp viên phương Tây bằng phong cách “một mình chống thế giới”.', 'https://i.pinimg.com/1200x/60/c4/39/60c4392e790fd2aca47c7c70a9379ff7.jpg', '1994', '80 phút', 'https://youtu.be/t42YRSzhMnM?si=n7ChU1gjWCDt4BsL', '2025-10-18 08:11:51'),
(12, 'Tây Du Ký', 'Tây Du Ký là bộ phim thần thoại Trung Quốc kể về hành trình Đường Tăng cùng ba đồ đệ Tôn Ngộ Không, Trư Bát Giới và Sa Tăng sang Tây Thiên thỉnh kinh theo lệnh của Phật Tổ. Trên đường đi, họ phải vượt qua nhiều kiếp nạn, chiến đấu với yêu ma quỷ quái và vượt qua cám dỗ để rèn luyện lòng kiên định, tình nghĩa thầy trò và tinh thần hướng thiện.', 'https://cdn.tienphong.vn/images/acaaf3972f12824005b323aa5fc6b75a70d5a7b30b99d0613716ffcbc01dde523a488803bb2d41aeb3bb113e805554ea43d935b1ce3ef1b79fc26ee44455cb29/tien_phong_tayduky2_pqur.jpg', '1986', '48 tập', '', '2025-10-18 18:27:40');

-- --------------------------------------------------------

--
-- Table structure for table `movie_categories`
--

CREATE TABLE `movie_categories` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `movie_categories`
--

INSERT INTO `movie_categories` (`id`, `movie_id`, `category_id`, `created_at`) VALUES
(28, 8, 1, '2025-10-18 07:58:12'),
(29, 8, 9, '2025-10-18 07:58:12'),
(30, 8, 6, '2025-10-18 07:58:12'),
(31, 7, 1, '2025-10-18 07:58:24'),
(32, 7, 9, '2025-10-18 07:58:24'),
(33, 7, 6, '2025-10-18 07:58:24'),
(34, 6, 1, '2025-10-18 07:58:33'),
(35, 6, 9, '2025-10-18 07:58:33'),
(36, 6, 6, '2025-10-18 07:58:33'),
(37, 4, 1, '2025-10-18 07:58:43'),
(38, 4, 9, '2025-10-18 07:58:43'),
(39, 4, 6, '2025-10-18 07:58:43'),
(42, 10, 1, '2025-10-18 17:27:39'),
(43, 10, 10, '2025-10-18 17:27:39'),
(44, 10, 6, '2025-10-18 17:27:39'),
(45, 9, 5, '2025-10-18 17:27:54'),
(46, 9, 12, '2025-10-18 17:27:54'),
(47, 9, 6, '2025-10-18 17:27:54'),
(52, 12, 2, '2025-10-18 19:21:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'admin', 'admin@gmail.com', '0192023a7bbd73250516f069df18b500', 'admin', '2025-10-17 19:29:10'),
(2, 'Huỳnh Vĩ Khang', 'user@gmail.com', '$2y$10$rIOULQZCPqap7F69KX30s.2QiJ29r.RQnoB6M4aKt3/I03H7IcDHO', 'user', '2025-10-18 17:56:29');

-- --------------------------------------------------------

--
-- Table structure for table `watched`
--

CREATE TABLE `watched` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `movie_id` int(11) DEFAULT NULL,
  `episode_id` int(11) DEFAULT NULL,
  `watched_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `watched`
--

INSERT INTO `watched` (`id`, `user_id`, `movie_id`, `episode_id`, `watched_at`) VALUES
(6, 2, 10, NULL, '2025-10-19 01:49:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `episodes`
--
ALTER TABLE `episodes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_movie_episode` (`movie_id`,`episode_number`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `favorites_ibfk_2` (`movie_id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movie_categories`
--
ALTER TABLE `movie_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_movie_category` (`movie_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `watched`
--
ALTER TABLE `watched`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_watch` (`user_id`,`movie_id`,`episode_id`),
  ADD KEY `fk_watched_movie` (`movie_id`),
  ADD KEY `fk_watched_episode` (`episode_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `episodes`
--
ALTER TABLE `episodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `movie_categories`
--
ALTER TABLE `movie_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `watched`
--
ALTER TABLE `watched`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `episodes`
--
ALTER TABLE `episodes`
  ADD CONSTRAINT `episodes_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `movie_categories`
--
ALTER TABLE `movie_categories`
  ADD CONSTRAINT `movie_categories_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `movie_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `watched`
--
ALTER TABLE `watched`
  ADD CONSTRAINT `fk_watched_episode` FOREIGN KEY (`episode_id`) REFERENCES `episodes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_watched_movie` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_watched_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
