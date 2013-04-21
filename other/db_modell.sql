CREATE TABLE `article` (
  `article_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `content` text,
  `created_datetime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`article_id`),
  UNIQUE KEY `Name_UNIQUE` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--ALTER TABLE `article` ADD INDEX (`title`);