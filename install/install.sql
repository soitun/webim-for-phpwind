/**
 * Replace '@charset' to database charset at first.
 * And then add prefix before the string of 'webim_' if need.
 */
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_connection  = @charset */ ;
/*!50003 SET character_set_results = utf8 */ ;

/*Remove the old version of 'webim_setting', rename it to 'webim_settings'*/
DROP TABLE IF EXISTS webim_setting;

/*Direct clear histories when re install or upgrade*/
DROP TABLE IF EXISTS webim_histories;

CREATE TABLE webim_histories (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`send` tinyint(1) DEFAULT NULL,
	`type` varchar(20) DEFAULT NULL,
	`to` varchar(20) DEFAULT NULL,
	`from` varchar(20) DEFAULT NULL,
	`nick` varchar(20) DEFAULT NULL COMMENT 'from nick',
	`body` text,
	`style` varchar(150) DEFAULT NULL,
	`timestamp` double DEFAULT NULL,
	`todel` tinyint(1) NOT NULL DEFAULT '0',
	`fromdel` tinyint(1) NOT NULL DEFAULT '0',
	`created_at` date DEFAULT NULL,
	`updated_at` date DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `todel` (`todel`),
	KEY `fromdel` (`fromdel`),
	KEY `timestamp` (`timestamp`),
	KEY `to` (`to`),
	KEY `from` (`from`),
	KEY `send` (`send`)
) ENGINE=MyISAM DEFAULT CHARSET=@charset;

CREATE TABLE IF NOT EXISTS webim_settings(
	`id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	`uid` mediumint(8) unsigned NOT NULL,
	`web` blob,
	`air` blob,
	`created_at` date DEFAULT NULL,
	`updated_at` date DEFAULT NULL,
	PRIMARY KEY (`id`) 
)ENGINE=MyISAM DEFAULT CHARSET=@charset;
