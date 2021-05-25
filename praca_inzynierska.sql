-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 03 Maj 2021, 21:05
-- Wersja serwera: 10.4.17-MariaDB
-- Wersja PHP: 7.4.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `praca_inzynierska`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `hash` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `file_name` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `mime_type` varchar(32) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Zrzut danych tabeli `files`
--

INSERT INTO `files` (`id`, `hash`, `file_name`, `created_at`, `mime_type`) VALUES
(1, 'ihGn18libQjmxJMeazgpw9WQy4DJa096B5bMzDsBP4JjsoeKPrK9rdVUpXkpu3we', '001-moja-wyobraznia-poszla-za-daleko.jpg', '2021-05-03 13:54:49', 'image/jpeg'),
(2, 'Ek5aa3u983t3ZSioJcJzSIk5X6f8SAoGUDuvEpaiv1WFeXTFI3y0oRaBiBnpy9Wj', '09a1c26413fc9a3cf84b12d1c12bd2e9.jpg', '2021-05-03 15:44:19', 'image/jpeg'),
(3, 'Oeo8nBAeoaWh2hTDHxMFNL33F3XNfT9wQPXtbFQf9DJ57EaHVoEr3ltbkZTCyuv6', '001-moja-wyobraznia-poszla-za-daleko.jpg', '2021-05-03 17:28:33', 'image/jpeg'),
(4, 'F7GESrdaZESQKxyboQJJZR9TGDl7qDsZH1sXk2El1N2SLkFrdhx3Qa1HFseNlj1X', '30sposobow020.jpg', '2021-05-03 20:29:51', 'image/jpeg');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `content` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Zrzut danych tabeli `messages`
--

INSERT INTO `messages` (`id`, `created_user_id`, `content`, `created_at`) VALUES
(1, 1, 'test', '2021-04-22 18:34:24'),
(2, 1, 'test 2', '2021-05-03 15:47:38'),
(3, 1, 'dasfdasfasd', '2021-05-03 17:16:50'),
(4, 1, 'test2', '2021-05-03 17:17:15'),
(5, 1, 'afdasfa', '2021-05-03 17:19:35'),
(6, 1, 'sadfdasfdsaf', '2021-05-03 17:26:44'),
(7, 1, 'dsafasdfsaf', '2021-05-03 17:27:44'),
(8, 1, 'asdfddasfadsf', '2021-05-03 17:28:33'),
(9, 1, 'No i to właśnie mamy zrobić', '2021-05-03 20:10:38'),
(10, 1, 'sadfdasfasdf', '2021-05-03 20:12:07'),
(11, 1, 'sadfddsa222', '2021-05-03 20:12:11'),
(12, 1, 'test 32', '2021-05-03 20:29:51');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `messages_files`
--

CREATE TABLE `messages_files` (
  `message_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Zrzut danych tabeli `messages_files`
--

INSERT INTO `messages_files` (`message_id`, `file_id`) VALUES
(8, 3),
(12, 4);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `title` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `content` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `is_read` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Zrzut danych tabeli `notifications`
--

INSERT INTO `notifications` (`id`, `created_user_id`, `title`, `content`, `created_at`, `is_read`) VALUES
(1, 1, 'SYSTEM', 'Zostałeś przydzielony do nowego zadania: Nowe zadanie', '2021-05-03 12:39:53', 0),
(2, 1, 'SYSTEM', 'Pojawiły się zmiany w zadaniu Nowe zadanie. Zapoznaj się z nimi.', '2021-05-03 12:54:22', 0),
(3, 1, 'SYSTEM', 'Pojawiły się zmiany w zadaniu Nowe zadanie. Zapoznaj się z nimi.', '2021-05-03 12:55:05', 0),
(4, 1, 'SYSTEM', 'Pojawiły się zmiany w zadaniu Nowe zadanie. Zapoznaj się z nimi.', '2021-05-03 13:54:49', 0),
(5, 1, 'SYSTEM', 'Pojawiły się zmiany w zadaniu Nowe zadanie. Zapoznaj się z nimi.', '2021-05-03 15:44:19', 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `assigned_user_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` varchar(4096) COLLATE utf8_unicode_ci NOT NULL,
  `progress` int(11) NOT NULL,
  `status` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `deadline_at` datetime DEFAULT NULL,
  `finished_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Zrzut danych tabeli `tasks`
--

INSERT INTO `tasks` (`id`, `created_user_id`, `assigned_user_id`, `name`, `content`, `progress`, `status`, `created_at`, `deadline_at`, `finished_at`) VALUES
(3, NULL, 1, 'Nowe zadanie', '<p>test</p>', 50, 'accepted', '2021-05-03 12:37:56', '2021-05-29 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `tasks_files`
--

CREATE TABLE `tasks_files` (
  `task_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Zrzut danych tabeli `tasks_files`
--

INSERT INTO `tasks_files` (`task_id`, `file_id`) VALUES
(3, 1),
(3, 2);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `image_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `salt` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `user_roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `last_logged_at` datetime DEFAULT NULL,
  `last_active_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `image_name`, `salt`, `user_roles`, `password`, `is_active`, `created_at`, `last_logged_at`, `last_active_at`) VALUES
(1, 'administrator', 'Dawid', 'Jankowski', NULL, 'bab85bbf2fc54c8f43b9b989c6754566', 'a:2:{i:0;s:9:\"ROLE_USER\";i:1;s:10:\"ROLE_ADMIN\";}', '$2y$12$261jvlWrGqkJSXL1ao383.LZVB6oH2aqqTvH1TcI8XEgmgNJtWZOS', 1, '2021-04-22 18:31:45', NULL, '2021-05-03 21:05:00');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_DB021E96E104C1D3` (`created_user_id`);

--
-- Indeksy dla tabeli `messages_files`
--
ALTER TABLE `messages_files`
  ADD PRIMARY KEY (`message_id`,`file_id`),
  ADD UNIQUE KEY `UNIQ_B337622293CB796C` (`file_id`),
  ADD KEY `IDX_B3376222537A1329` (`message_id`);

--
-- Indeksy dla tabeli `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_6000B0D3E104C1D3` (`created_user_id`);

--
-- Indeksy dla tabeli `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_50586597E104C1D3` (`created_user_id`),
  ADD KEY `IDX_50586597ADF66B1A` (`assigned_user_id`);

--
-- Indeksy dla tabeli `tasks_files`
--
ALTER TABLE `tasks_files`
  ADD PRIMARY KEY (`task_id`,`file_id`),
  ADD UNIQUE KEY `UNIQ_BFB97BFE93CB796C` (`file_id`),
  ADD KEY `IDX_BFB97BFE8DB60186` (`task_id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_1483A5E9F85E0677` (`username`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT dla tabeli `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT dla tabeli `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT dla tabeli `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `FK_DB021E96E104C1D3` FOREIGN KEY (`created_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ograniczenia dla tabeli `messages_files`
--
ALTER TABLE `messages_files`
  ADD CONSTRAINT `FK_B3376222537A1329` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`),
  ADD CONSTRAINT `FK_B337622293CB796C` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`);

--
-- Ograniczenia dla tabeli `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `FK_6000B0D3E104C1D3` FOREIGN KEY (`created_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ograniczenia dla tabeli `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `FK_50586597ADF66B1A` FOREIGN KEY (`assigned_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_50586597E104C1D3` FOREIGN KEY (`created_user_id`) REFERENCES `users` (`id`);

--
-- Ograniczenia dla tabeli `tasks_files`
--
ALTER TABLE `tasks_files`
  ADD CONSTRAINT `FK_BFB97BFE8DB60186` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`),
  ADD CONSTRAINT `FK_BFB97BFE93CB796C` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
