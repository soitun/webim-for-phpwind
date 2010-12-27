<?php

global $_IMC, $imdb, $im_queries;

$charset_collate = '';
if ( ! empty($_IMC['dbcharset']) )
	$charset_collate = "DEFAULT CHARACTER SET {$_IMC['dbcharset']}";
if ( ! empty($wpdb->collate) )
	$charset_collate .= " COLLATE $wpdb->collate";

/** Create WordPress database tables SQL */
$im_queries = "CREATE TABLE $imdb->webim_histories (
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
) ENGINE=MyISAM $charset_collate;
CREATE TABLE $imdb->webim_settings (
	`id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	`uid` mediumint(8) unsigned NOT NULL,
	`web` blob,
	`air` blob,
	`created_at` date DEFAULT NULL,
	`updated_at` date DEFAULT NULL,
	PRIMARY KEY (`id`) 
) ENGINE=MyISAM $charset_collate;";

