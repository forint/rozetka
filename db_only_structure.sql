CREATE TABLE IF NOT EXISTS `films` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text,
  `img` varchar(255) NOT NULL DEFAULT '',
  `director` varchar(255) DEFAULT '',
  `country` varchar(100) DEFAULT '',
  `year` INT(5) DEFAULT NULL,
  `duration` varchar(5) DEFAULT NULL,
  `created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `published` TIMESTAMP DEFAULT 0,
  `status` BOOLEAN NOT NULL DEFAULT FALSE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `booking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `film_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `session` int(11) NOT NULL,
  `seats` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT fk_booking_films_id FOREIGN KEY (`film_id`) REFERENCES films(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;