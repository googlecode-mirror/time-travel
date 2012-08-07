DROP TABLE IF EXISTS `journal`.`user`;
CREATE TABLE  `journal`.`user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `name` text NOT NULL,
  `surname` text NOT NULL,
  `email` text NOT NULL,
  `created` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  `fbUsername` text,
  `fbToken` text,
  `gender` text,
  `location` text,
  `work` text,
  `birthday` datetime DEFAULT NULL,
  `timezone` int(10) unsigned DEFAULT NULL,
  `cellphone` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `journal`.`user_day`;
CREATE TABLE  `journal`.`user_day` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `theDate` datetime NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_user_day_userid` (`userid`),
  CONSTRAINT `FK_user_day_userid` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=684 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `journal`.`picture`;
CREATE TABLE  `journal`.`picture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `dayid` int(10) unsigned NOT NULL,
  `location` text,
  `timetaken` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `latitude` text,
  `longitude` text,
  `fileType` text NOT NULL,
  `filename` text NOT NULL,
  `payload` longblob,
  PRIMARY KEY (`id`),
  KEY `FK_picture_day` (`dayid`),
  CONSTRAINT `FK_picture_day` FOREIGN KEY (`dayid`) REFERENCES `user_day` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3607 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `journal`.`status_update`;
CREATE TABLE  `journal`.`status_update` (
  `id` varchar(20) NOT NULL,
  `theDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `message` text NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  `dayid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_status_update_userid` (`userid`),
  CONSTRAINT `FK_status_update_userid` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `journal`.`location` (
  `dayid` INTEGER UNSIGNED NOT NULL,
  `theTimestamp` TIMESTAMP NOT NULL,
  `longitude` TEXT NOT NULL,
  `latitude` TEXT NOT NULL,
  CONSTRAINT `FK_location_dayid` FOREIGN KEY `FK_location_dayid` (`dayid`)
    REFERENCES `user_day` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
)
ENGINE = InnoDB;
