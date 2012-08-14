
begin;
DROP TABLE IF EXISTS `journal`.`contenttype`;
CREATE TABLE  `journal`.`contenttype` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(20) NOT NULL,
  `tablename` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

insert into contenttype values (1, 'picture', 'picture');
insert into contenttype values (2, 'status_update', 'status_update');
insert into contenttype values (3, 'sms_gmail', 'communicationcontent');
insert into contenttype values (4, 'email_gmail', 'communicationcontent');
insert into contenttype values (5, 'geolocation', 'location');


DROP TABLE IF EXISTS `journal`.`sharedcontent`;
CREATE TABLE  `journal`.`sharedcontent` (
  `contentid` int(10) unsigned NOT NULL,
  `contenttype` int(10) unsigned NOT NULL,
  `sharerid` int(10) unsigned NOT NULL,
  `sharedforid` int(10) unsigned NOT NULL,
  KEY `FK_sharedcontent_sharerid` (`sharerid`),
  KEY `FK_sharedcontent_sharedtoid` (`sharedforid`),
  KEY `FK_sharedcontent_contentid` (`contenttype`),
  CONSTRAINT `FK_sharedcontent_contentid` FOREIGN KEY (`contenttype`) REFERENCES `contenttype` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_sharedcontent_sharedtoid` FOREIGN KEY (`sharedforid`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_sharedcontent_sharerid` FOREIGN KEY (`sharerid`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `journal`.`sharedcontent` ADD UNIQUE INDEX `sharedcontent_Index_Unique_Key`(`contentid`, `contenttype`, `sharerid`, `sharedforid`);

ALTER TABLE `journal`.`status_update` ADD INDEX `Index_status_update_i0`(`dayid`);


commit;