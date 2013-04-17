CREATE TABLE `wikipage` (
  `wikipage_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `content` text,
  `created_datetime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `created_ipaddress` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`wikipage_id`),
  UNIQUE KEY `Name_UNIQUE` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `wikipage` ADD INDEX (`title`);