begin;

CREATE TABLE  `journal`.`accessdetails` (
  `accesstype` varchar(10) NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index_accessdetails_unique` (`accesstype`,`userid`),
  KEY `FK_accessdetails_userid` (`userid`),
  CONSTRAINT `FK_accessdetails_userid` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

CREATE TABLE  `journal`.`accessdetailsentries` (
  `accessdetailsid` int(10) unsigned NOT NULL,
  `key` text NOT NULL,
  `value` text NOT NULL,
  KEY `FK_accessdetailsentries_accessdetailsid` (`accessdetailsid`),
  CONSTRAINT `FK_accessdetailsentries_accessdetailsid` FOREIGN KEY (`accessdetailsid`) REFERENCES `accessdetails` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE  `journal`.`contentupdate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` text NOT NULL,
  `lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL,
  `userid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

CREATE TABLE  `journal`.`contentupdatedetails` (
  `contentupdateid` int(10) unsigned NOT NULL,
  `contentkey` text NOT NULL,
  KEY `FK_contentupdatedetails_contentid` (`contentupdateid`),
  CONSTRAINT `FK_contentupdatedetails_contentid` FOREIGN KEY (`contentupdateid`) REFERENCES `contentupdate` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `journal`.`communicationcontent`;
CREATE TABLE  `journal`.`communicationcontent` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(40) NOT NULL,
  `body` text NOT NULL,
  `theTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `dayid` int(10) unsigned NOT NULL,
  `source` text NOT NULL,
  `communicationtype` text NOT NULL,
  `recipient` text NOT NULL,
  UNIQUE KEY `Index_UNIQUE_KEY` (`id`),
  UNIQUE KEY `Index_communicationcontent_UNIQUE` (`title`,`theTimestamp`,`dayid`),
  KEY `FK_communicationcontent_userday` (`dayid`),
  CONSTRAINT `FK_communicationcontent_userday` FOREIGN KEY (`dayid`) REFERENCES `user_day` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8;
commit;