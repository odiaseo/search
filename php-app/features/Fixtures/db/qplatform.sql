DROP TABLE IF EXISTS `content_domains`;
CREATE TABLE `content_domains` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `currency` char(3) COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `timezone` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `amazon_s3_folder` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `base_url` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `access_token` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=202 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `content_domains` VALUES
  (1,'quidco','Quidco','GBP','en-GB','Europe/London','en','http://www.quidco.com','1'),
  (2,'qipu','Qipu','EUR','de-DE','Europe/Berlin','de','https://www.qipu.de','2'),
  (200,'shoop','Shoop','EUR','fr-FR','Europe/Paris','fr','http://mvpbeta5.quidco.com','200'),
  (201,'',NULL,'','','',NULL,'','201');