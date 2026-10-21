/*M!999999\- enable the sandbox mode */ 

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `wp_commentmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_commentmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `comment_id` (`comment_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `wp_commentmeta` WRITE;
/*!40000 ALTER TABLE `wp_commentmeta` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_commentmeta` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `wp_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_comments` (
  `comment_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `comment_post_ID` bigint(20) unsigned NOT NULL DEFAULT 0,
  `comment_author` tinytext NOT NULL,
  `comment_author_email` varchar(100) NOT NULL DEFAULT '',
  `comment_author_url` varchar(200) NOT NULL DEFAULT '',
  `comment_author_IP` varchar(100) NOT NULL DEFAULT '',
  `comment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_content` text NOT NULL,
  `comment_karma` int(11) NOT NULL DEFAULT 0,
  `comment_approved` varchar(20) NOT NULL DEFAULT '1',
  `comment_agent` varchar(255) NOT NULL DEFAULT '',
  `comment_type` varchar(20) NOT NULL DEFAULT 'comment',
  `comment_parent` bigint(20) unsigned NOT NULL DEFAULT 0,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`comment_ID`),
  KEY `comment_post_ID` (`comment_post_ID`),
  KEY `comment_approved_date_gmt` (`comment_approved`,`comment_date_gmt`),
  KEY `comment_date_gmt` (`comment_date_gmt`),
  KEY `comment_parent` (`comment_parent`),
  KEY `comment_author_email` (`comment_author_email`(10))
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `wp_comments` WRITE;
/*!40000 ALTER TABLE `wp_comments` DISABLE KEYS */;
INSERT INTO `wp_comments` VALUES (1,1,'A WordPress Commenter','wapuu@wordpress.example','https://wordpress.org/','','2026-10-21 09:58:58','2026-10-21 09:58:58','Hi, this is a comment.\nTo get started with moderating, editing, and deleting comments, please visit the Comments screen in the dashboard.\nCommenter avatars come from <a href=\"https://gravatar.com/\">Gravatar</a>.',0,'1','','comment',0,0);
/*!40000 ALTER TABLE `wp_comments` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `wp_dli_custom_translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_dli_custom_translations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `label` text NOT NULL,
  `domain` varchar(100) NOT NULL,
  `lang` varchar(2) NOT NULL,
  `translation` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_text_domain` (`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `wp_dli_custom_translations` WRITE;
/*!40000 ALTER TABLE `wp_dli_custom_translations` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_dli_custom_translations` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `wp_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_links` (
  `link_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `link_url` varchar(255) NOT NULL DEFAULT '',
  `link_name` varchar(255) NOT NULL DEFAULT '',
  `link_image` varchar(255) NOT NULL DEFAULT '',
  `link_target` varchar(25) NOT NULL DEFAULT '',
  `link_description` varchar(255) NOT NULL DEFAULT '',
  `link_visible` varchar(20) NOT NULL DEFAULT 'Y',
  `link_owner` bigint(20) unsigned NOT NULL DEFAULT 1,
  `link_rating` int(11) NOT NULL DEFAULT 0,
  `link_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `link_rel` varchar(255) NOT NULL DEFAULT '',
  `link_notes` mediumtext NOT NULL,
  `link_rss` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`link_id`),
  KEY `link_visible` (`link_visible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `wp_links` WRITE;
/*!40000 ALTER TABLE `wp_links` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_links` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `wp_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_options` (
  `option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(191) NOT NULL DEFAULT '',
  `option_value` longtext NOT NULL,
  `autoload` varchar(20) NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`option_id`),
  UNIQUE KEY `option_name` (`option_name`),
  KEY `autoload` (`autoload`)
) ENGINE=InnoDB AUTO_INCREMENT=298 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `wp_options` WRITE;
/*!40000 ALTER TABLE `wp_options` DISABLE KEYS */;
INSERT INTO `wp_options` VALUES (1,'cron','a:11:{i:1792587539;a:1:{s:34:\"wp_privacy_delete_old_export_files\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"hourly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:3600;}}}i:1792620082;a:1:{s:21:\"wp_update_user_counts\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1792623538;a:1:{s:16:\"wp_version_check\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1792625338;a:1:{s:17:\"wp_update_plugins\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1792627138;a:1:{s:16:\"wp_update_themes\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1792663139;a:3:{s:30:\"wp_site_health_scheduled_check\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"weekly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:604800;}}s:32:\"recovery_mode_clean_expired_keys\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}s:41:\"wp_privacy_personal_data_cleanup_requests\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1792663282;a:2:{s:19:\"wp_scheduled_delete\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}s:25:\"delete_expired_transients\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1792663285;a:1:{s:30:\"wp_scheduled_auto_draft_delete\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1792663467;a:1:{s:27:\"acf_update_site_health_data\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1793181819;a:1:{s:30:\"wp_delete_temp_updater_backups\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"weekly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:604800;}}}s:7:\"version\";i:2;}','on');
INSERT INTO `wp_options` VALUES (2,'siteurl','http://dli-wp-e2e.local','on');
INSERT INTO `wp_options` VALUES (3,'home','http://dli-wp-e2e.local','on');
INSERT INTO `wp_options` VALUES (4,'blogname','dli-wp-e2e','on');
INSERT INTO `wp_options` VALUES (5,'blogdescription','','on');
INSERT INTO `wp_options` VALUES (6,'users_can_register','0','on');
INSERT INTO `wp_options` VALUES (7,'admin_email','e2e-admin@example.test','on');
INSERT INTO `wp_options` VALUES (8,'start_of_week','1','on');
INSERT INTO `wp_options` VALUES (9,'use_balanceTags','0','on');
INSERT INTO `wp_options` VALUES (10,'use_smilies','1','on');
INSERT INTO `wp_options` VALUES (11,'require_name_email','1','on');
INSERT INTO `wp_options` VALUES (12,'comments_notify','1','on');
INSERT INTO `wp_options` VALUES (13,'posts_per_rss','10','on');
INSERT INTO `wp_options` VALUES (14,'rss_use_excerpt','0','on');
INSERT INTO `wp_options` VALUES (15,'mailserver_url','mail.example.com','on');
INSERT INTO `wp_options` VALUES (16,'mailserver_login','login@example.com','on');
INSERT INTO `wp_options` VALUES (17,'mailserver_pass','','on');
INSERT INTO `wp_options` VALUES (18,'mailserver_port','110','on');
INSERT INTO `wp_options` VALUES (19,'default_category','1','on');
INSERT INTO `wp_options` VALUES (20,'default_comment_status','closed','on');
INSERT INTO `wp_options` VALUES (21,'default_ping_status','open','on');
INSERT INTO `wp_options` VALUES (22,'default_pingback_flag','1','on');
INSERT INTO `wp_options` VALUES (23,'posts_per_page','10','on');
INSERT INTO `wp_options` VALUES (24,'date_format','F j, Y','on');
INSERT INTO `wp_options` VALUES (25,'time_format','g:i a','on');
INSERT INTO `wp_options` VALUES (26,'links_updated_date_format','F j, Y g:i a','on');
INSERT INTO `wp_options` VALUES (27,'comment_moderation','0','on');
INSERT INTO `wp_options` VALUES (28,'moderation_notify','1','on');
INSERT INTO `wp_options` VALUES (29,'permalink_structure','/%postname%/','on');
INSERT INTO `wp_options` VALUES (31,'hack_file','0','on');
INSERT INTO `wp_options` VALUES (32,'blog_charset','UTF-8','on');
INSERT INTO `wp_options` VALUES (33,'moderation_keys','','off');
INSERT INTO `wp_options` VALUES (34,'active_plugins','a:3:{i:0;s:21:\"polylang/polylang.php\";i:1;s:33:\"acf-openstreetmap-field/index.php\";i:2;s:30:\"advanced-custom-fields/acf.php\";}','on');
INSERT INTO `wp_options` VALUES (35,'category_base','','on');
INSERT INTO `wp_options` VALUES (36,'ping_sites','https://rpc.pingomatic.com/','on');
INSERT INTO `wp_options` VALUES (37,'comment_max_links','2','on');
INSERT INTO `wp_options` VALUES (38,'gmt_offset','0','on');
INSERT INTO `wp_options` VALUES (39,'default_email_category','1','on');
INSERT INTO `wp_options` VALUES (40,'recently_edited','','off');
INSERT INTO `wp_options` VALUES (41,'template','design-laboratori-wordpress-theme','on');
INSERT INTO `wp_options` VALUES (42,'stylesheet','design-laboratori-wordpress-theme','on');
INSERT INTO `wp_options` VALUES (43,'comment_registration','0','on');
INSERT INTO `wp_options` VALUES (44,'html_type','text/html','on');
INSERT INTO `wp_options` VALUES (45,'use_trackback','0','on');
INSERT INTO `wp_options` VALUES (46,'default_role','subscriber','on');
INSERT INTO `wp_options` VALUES (47,'db_version','61833','on');
INSERT INTO `wp_options` VALUES (48,'uploads_use_yearmonth_folders','1','on');
INSERT INTO `wp_options` VALUES (49,'upload_path','','on');
INSERT INTO `wp_options` VALUES (50,'blog_public','1','on');
INSERT INTO `wp_options` VALUES (51,'default_link_category','2','on');
INSERT INTO `wp_options` VALUES (52,'show_on_front','posts','on');
INSERT INTO `wp_options` VALUES (53,'tag_base','argomento','on');
INSERT INTO `wp_options` VALUES (54,'show_avatars','1','on');
INSERT INTO `wp_options` VALUES (55,'avatar_rating','G','on');
INSERT INTO `wp_options` VALUES (56,'upload_url_path','','on');
INSERT INTO `wp_options` VALUES (57,'thumbnail_size_w','150','on');
INSERT INTO `wp_options` VALUES (58,'thumbnail_size_h','150','on');
INSERT INTO `wp_options` VALUES (59,'thumbnail_crop','1','on');
INSERT INTO `wp_options` VALUES (60,'medium_size_w','300','on');
INSERT INTO `wp_options` VALUES (61,'medium_size_h','300','on');
INSERT INTO `wp_options` VALUES (62,'avatar_default','mystery','on');
INSERT INTO `wp_options` VALUES (63,'large_size_w','1024','on');
INSERT INTO `wp_options` VALUES (64,'large_size_h','1024','on');
INSERT INTO `wp_options` VALUES (65,'image_default_link_type','none','on');
INSERT INTO `wp_options` VALUES (66,'image_default_size','','on');
INSERT INTO `wp_options` VALUES (67,'image_default_align','','on');
INSERT INTO `wp_options` VALUES (68,'close_comments_for_old_posts','0','on');
INSERT INTO `wp_options` VALUES (69,'close_comments_days_old','14','on');
INSERT INTO `wp_options` VALUES (70,'thread_comments','1','on');
INSERT INTO `wp_options` VALUES (71,'thread_comments_depth','5','on');
INSERT INTO `wp_options` VALUES (72,'page_comments','0','on');
INSERT INTO `wp_options` VALUES (73,'comments_per_page','50','on');
INSERT INTO `wp_options` VALUES (74,'default_comments_page','newest','on');
INSERT INTO `wp_options` VALUES (75,'comment_order','asc','on');
INSERT INTO `wp_options` VALUES (76,'sticky_posts','a:0:{}','on');
INSERT INTO `wp_options` VALUES (77,'widget_categories','a:0:{}','on');
INSERT INTO `wp_options` VALUES (78,'widget_text','a:0:{}','on');
INSERT INTO `wp_options` VALUES (79,'widget_rss','a:0:{}','on');
INSERT INTO `wp_options` VALUES (80,'uninstall_plugins','a:0:{}','off');
INSERT INTO `wp_options` VALUES (81,'timezone_string','','on');
INSERT INTO `wp_options` VALUES (82,'page_for_posts','0','on');
INSERT INTO `wp_options` VALUES (83,'page_on_front','0','on');
INSERT INTO `wp_options` VALUES (84,'default_post_format','0','on');
INSERT INTO `wp_options` VALUES (85,'link_manager_enabled','0','on');
INSERT INTO `wp_options` VALUES (86,'finished_splitting_shared_terms','1','on');
INSERT INTO `wp_options` VALUES (87,'site_icon','0','on');
INSERT INTO `wp_options` VALUES (88,'medium_large_size_w','768','on');
INSERT INTO `wp_options` VALUES (89,'medium_large_size_h','0','on');
INSERT INTO `wp_options` VALUES (90,'wp_page_for_privacy_policy','3','on');
INSERT INTO `wp_options` VALUES (91,'show_comments_cookies_opt_in','1','on');
INSERT INTO `wp_options` VALUES (92,'admin_email_lifespan','1808128738','on');
INSERT INTO `wp_options` VALUES (93,'disallowed_keys','','off');
INSERT INTO `wp_options` VALUES (94,'comment_previously_approved','1','on');
INSERT INTO `wp_options` VALUES (95,'auto_plugin_theme_update_emails','a:0:{}','off');
INSERT INTO `wp_options` VALUES (96,'auto_update_core_dev','enabled','on');
INSERT INTO `wp_options` VALUES (97,'auto_update_core_minor','enabled','on');
INSERT INTO `wp_options` VALUES (98,'auto_update_core_major','enabled','on');
INSERT INTO `wp_options` VALUES (99,'wp_force_deactivated_plugins','a:0:{}','on');
INSERT INTO `wp_options` VALUES (100,'wp_attachment_pages_enabled','0','on');
INSERT INTO `wp_options` VALUES (101,'wp_notes_notify','1','on');
INSERT INTO `wp_options` VALUES (102,'initial_db_version','61833','on');
INSERT INTO `wp_options` VALUES (103,'wp_user_roles','a:6:{s:13:\"administrator\";a:2:{s:4:\"name\";s:13:\"Administrator\";s:12:\"capabilities\";a:62:{s:13:\"switch_themes\";b:1;s:11:\"edit_themes\";b:1;s:16:\"activate_plugins\";b:1;s:12:\"edit_plugins\";b:1;s:10:\"edit_users\";b:1;s:10:\"edit_files\";b:1;s:14:\"manage_options\";b:1;s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:6:\"import\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:8:\"level_10\";b:1;s:7:\"level_9\";b:1;s:7:\"level_8\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;s:12:\"delete_users\";b:1;s:12:\"create_users\";b:1;s:17:\"unfiltered_upload\";b:1;s:14:\"edit_dashboard\";b:1;s:14:\"update_plugins\";b:1;s:14:\"delete_plugins\";b:1;s:15:\"install_plugins\";b:1;s:13:\"update_themes\";b:1;s:14:\"install_themes\";b:1;s:11:\"update_core\";b:1;s:10:\"list_users\";b:1;s:12:\"remove_users\";b:1;s:13:\"promote_users\";b:1;s:18:\"edit_theme_options\";b:1;s:13:\"delete_themes\";b:1;s:6:\"export\";b:1;s:27:\"dli_edit_site_configuration\";b:1;}}s:6:\"editor\";a:2:{s:4:\"name\";s:6:\"Editor\";s:12:\"capabilities\";a:34:{s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;}}s:6:\"author\";a:2:{s:4:\"name\";s:6:\"Author\";s:12:\"capabilities\";a:10:{s:12:\"upload_files\";b:1;s:10:\"edit_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;s:22:\"delete_published_posts\";b:1;}}s:11:\"contributor\";a:2:{s:4:\"name\";s:11:\"Contributor\";s:12:\"capabilities\";a:5:{s:10:\"edit_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;}}s:10:\"subscriber\";a:2:{s:4:\"name\";s:10:\"Subscriber\";s:12:\"capabilities\";a:2:{s:4:\"read\";b:1;s:7:\"level_0\";b:1;}}s:16:\"dli_super_editor\";a:2:{s:4:\"name\";s:12:\"Super Editor\";s:12:\"capabilities\";a:36:{s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;s:18:\"edit_theme_options\";b:1;s:27:\"dli_edit_site_configuration\";b:1;}}}','on');
INSERT INTO `wp_options` VALUES (104,'fresh_site','0','off');
INSERT INTO `wp_options` VALUES (105,'user_count','3','off');
INSERT INTO `wp_options` VALUES (106,'widget_block','a:6:{i:2;a:1:{s:7:\"content\";s:19:\"<!-- wp:search /-->\";}i:3;a:1:{s:7:\"content\";s:154:\"<!-- wp:group --><div class=\"wp-block-group\"><!-- wp:heading --><h2>Recent Posts</h2><!-- /wp:heading --><!-- wp:latest-posts /--></div><!-- /wp:group -->\";}i:4;a:1:{s:7:\"content\";s:227:\"<!-- wp:group --><div class=\"wp-block-group\"><!-- wp:heading --><h2>Recent Comments</h2><!-- /wp:heading --><!-- wp:latest-comments {\"displayAvatar\":false,\"displayDate\":false,\"displayExcerpt\":false} /--></div><!-- /wp:group -->\";}i:5;a:1:{s:7:\"content\";s:146:\"<!-- wp:group --><div class=\"wp-block-group\"><!-- wp:heading --><h2>Archives</h2><!-- /wp:heading --><!-- wp:archives /--></div><!-- /wp:group -->\";}i:6;a:1:{s:7:\"content\";s:150:\"<!-- wp:group --><div class=\"wp-block-group\"><!-- wp:heading --><h2>Categories</h2><!-- /wp:heading --><!-- wp:categories /--></div><!-- /wp:group -->\";}s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (107,'sidebars_widgets','a:2:{s:19:\"wp_inactive_widgets\";a:5:{i:0;s:7:\"block-2\";i:1;s:7:\"block-3\";i:2;s:7:\"block-4\";i:3;s:7:\"block-5\";i:4;s:7:\"block-6\";}s:13:\"array_version\";i:3;}','auto');
INSERT INTO `wp_options` VALUES (108,'widget_pages','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (109,'widget_calendar','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (110,'widget_archives','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (111,'widget_media_audio','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (112,'widget_media_image','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (113,'widget_media_gallery','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (114,'widget_media_video','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (115,'widget_meta','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (116,'widget_search','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (117,'widget_recent-posts','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (118,'widget_recent-comments','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (119,'widget_tag_cloud','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (120,'widget_nav_menu','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (121,'widget_custom_html','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (126,'recovery_keys','a:0:{}','off');
INSERT INTO `wp_options` VALUES (127,'WPLANG','','auto');
INSERT INTO `wp_options` VALUES (140,'can_compress_scripts','1','on');
INSERT INTO `wp_options` VALUES (153,'finished_updating_comment_type','1','auto');
INSERT INTO `wp_options` VALUES (155,'recently_activated','a:0:{}','off');
INSERT INTO `wp_options` VALUES (159,'theme_mods_twentytwentyfive','a:1:{s:16:\"sidebars_widgets\";a:2:{s:4:\"time\";i:1792577054;s:4:\"data\";a:3:{s:19:\"wp_inactive_widgets\";a:0:{}s:9:\"sidebar-1\";a:3:{i:0;s:7:\"block-2\";i:1;s:7:\"block-3\";i:2;s:7:\"block-4\";}s:9:\"sidebar-2\";a:2:{i:0;s:7:\"block-5\";i:1;s:7:\"block-6\";}}}}','off');
INSERT INTO `wp_options` VALUES (160,'current_theme','Design Laboratori Italia','auto');
INSERT INTO `wp_options` VALUES (161,'theme_mods_design-laboratori-wordpress-theme','a:3:{i:0;b:0;s:18:\"nav_menu_locations\";a:5:{s:8:\"menu-lab\";i:80;s:10:\"menu-right\";i:81;s:17:\"menu-header-right\";i:82;s:10:\"menu-links\";i:84;s:11:\"menu-footer\";i:83;}s:18:\"custom_css_post_id\";i:-1;}','on');
INSERT INTO `wp_options` VALUES (162,'theme_switched','','auto');
INSERT INTO `wp_options` VALUES (165,'dli_pwa_rules_version','1','auto');
INSERT INTO `wp_options` VALUES (166,'acf_first_activated_version','6.8.10','on');
INSERT INTO `wp_options` VALUES (167,'acf_site_health','{\"version\":\"6.8.10\",\"plugin_type\":\"Free\",\"update_source\":\"wordpress.org\",\"wp_version\":\"7.1.3\",\"mysql_version\":\"10.11.18-MariaDB\",\"is_multisite\":false,\"active_theme\":{\"name\":\"Design Laboratori Italia\",\"version\":\"2.0.2\",\"theme_uri\":\"https:\\/\\/github.com\\/ScuolaNormaleSuperiore\\/design-laboratori-wordpress-theme\",\"stylesheet\":false},\"active_plugins\":{\"advanced-custom-fields\\/acf.php\":{\"name\":\"Advanced Custom Fields\",\"version\":\"6.8.10\",\"plugin_uri\":\"https:\\/\\/www.advancedcustomfields.com\"}},\"ui_field_groups\":\"0\",\"php_field_groups\":\"0\",\"json_field_groups\":\"0\",\"rest_field_groups\":\"9\",\"all_location_rules\":[\"post_type==post\",\"post_type==banner\",\"post_type==brevetto\",\"post_type==evento\",\"post_type==indirizzo-di-ricerca\",\"post_type==luogo\",\"post_type==notizia\",\"post_type==page\",\"post_type==persona\",\"post_type==progetto\",\"post_type==pubblicazione\",\"post_type==risorsa-tecnica\",\"post_type==spinoff\",\"post_type==sponsor\",\"post_type==tipologia-persona\"],\"field_groups_by_post_type\":[{\"post_type\":\"banner\",\"field_group_count\":1},{\"post_type\":\"brevetto\",\"field_group_count\":1},{\"post_type\":\"evento\",\"field_group_count\":1},{\"post_type\":\"indirizzo-di-ricerca\",\"field_group_count\":1},{\"post_type\":\"luogo\",\"field_group_count\":1},{\"post_type\":\"notizia\",\"field_group_count\":1},{\"post_type\":\"page\",\"field_group_count\":1},{\"post_type\":\"persona\",\"field_group_count\":1},{\"post_type\":\"post\",\"field_group_count\":1},{\"post_type\":\"progetto\",\"field_group_count\":1},{\"post_type\":\"pubblicazione\",\"field_group_count\":1},{\"post_type\":\"risorsa-tecnica\",\"field_group_count\":1},{\"post_type\":\"spinoff\",\"field_group_count\":1},{\"post_type\":\"sponsor\",\"field_group_count\":1},{\"post_type\":\"tipologia-persona\",\"field_group_count\":1}],\"number_of_fields_by_type\":{\"true_false\":24,\"text\":35,\"number\":9,\"url\":12,\"textarea\":9,\"wysiwyg\":2,\"select\":5,\"date_picker\":5,\"time_picker\":2,\"file\":13,\"relationship\":15,\"email\":4,\"image\":2},\"number_of_third_party_fields_by_type\":{\"open_street_map\":1},\"post_types_enabled\":true,\"ui_post_types\":\"0\",\"json_post_types\":\"0\",\"ui_taxonomies\":\"0\",\"json_taxonomies\":\"0\",\"rest_api_format\":\"light\",\"admin_ui_enabled\":true,\"field_type-modal_enabled\":true,\"field_settings_tabs_enabled\":false,\"shortcode_enabled\":false,\"registered_acf_forms\":\"0\",\"json_save_paths\":1,\"json_load_paths\":1,\"ai_enabled\":false,\"schema_support\":false,\"schema_ready_objects\":{\"blocks\":0,\"post_types\":0},\"event_first_activated\":1792577067,\"last_updated\":1792577068}','off');
INSERT INTO `wp_options` VALUES (169,'acf_version','6.8.10','auto');
INSERT INTO `wp_options` VALUES (172,'polylang','a:15:{s:10:\"force_lang\";i:1;s:7:\"domains\";a:0:{}s:12:\"hide_default\";b:1;s:7:\"rewrite\";b:1;s:13:\"redirect_lang\";b:0;s:7:\"browser\";b:0;s:13:\"media_support\";b:1;s:10:\"post_types\";a:0:{}s:10:\"taxonomies\";a:0:{}s:4:\"sync\";a:0:{}s:12:\"default_lang\";s:2:\"it\";s:9:\"nav_menus\";a:1:{s:33:\"design-laboratori-wordpress-theme\";a:5:{s:8:\"menu-lab\";a:2:{s:2:\"en\";i:85;s:2:\"it\";i:80;}s:10:\"menu-right\";a:2:{s:2:\"en\";i:86;s:2:\"it\";i:81;}s:17:\"menu-header-right\";a:2:{s:2:\"en\";i:87;s:2:\"it\";i:82;}s:10:\"menu-links\";a:2:{s:2:\"en\";i:89;s:2:\"it\";i:84;}s:11:\"menu-footer\";a:2:{s:2:\"en\";i:88;s:2:\"it\";i:83;}}}s:16:\"first_activation\";i:1792577071;s:16:\"previous_version\";s:0:\"\";s:7:\"version\";s:5:\"3.9.0\";}','auto');
INSERT INTO `wp_options` VALUES (173,'pll_language_from_content_available','no','auto');
INSERT INTO `wp_options` VALUES (174,'polylang_wpml_strings','a:0:{}','auto');
INSERT INTO `wp_options` VALUES (175,'pll_language_taxonomies','a:0:{}','auto');
INSERT INTO `wp_options` VALUES (176,'widget_polylang','a:1:{s:12:\"_multiwidget\";i:1;}','auto');
INSERT INTO `wp_options` VALUES (177,'rewrite_rules','a:607:{s:11:\"^wp-json/?$\";s:22:\"index.php?rest_route=/\";s:14:\"^wp-json/(.*)?\";s:33:\"index.php?rest_route=/$matches[1]\";s:21:\"^index.php/wp-json/?$\";s:22:\"index.php?rest_route=/\";s:24:\"^index.php/wp-json/(.*)?\";s:33:\"index.php?rest_route=/$matches[1]\";s:17:\"^wp-sitemap\\.xml$\";s:23:\"index.php?sitemap=index\";s:17:\"^wp-sitemap\\.xsl$\";s:36:\"index.php?sitemap-stylesheet=sitemap\";s:23:\"^wp-sitemap-index\\.xsl$\";s:34:\"index.php?sitemap-stylesheet=index\";s:53:\"^(en)/wp-sitemap-([a-z]+?)-([a-z\\d_-]+?)-(\\d+?)\\.xml$\";s:92:\"index.php?lang=$matches[1]&sitemap=$matches[2]&sitemap-subtype=$matches[3]&paged=$matches[4]\";s:48:\"^wp-sitemap-([a-z]+?)-([a-z\\d_-]+?)-(\\d+?)\\.xml$\";s:75:\"index.php?sitemap=$matches[1]&sitemap-subtype=$matches[2]&paged=$matches[3]\";s:39:\"^(en)/wp-sitemap-([a-z]+?)-(\\d+?)\\.xml$\";s:64:\"index.php?lang=$matches[1]&sitemap=$matches[2]&paged=$matches[3]\";s:34:\"^wp-sitemap-([a-z]+?)-(\\d+?)\\.xml$\";s:47:\"index.php?sitemap=$matches[1]&paged=$matches[2]\";s:14:\"^sitemap\\.xml$\";s:27:\"index.php?dli_sitemap=index\";s:17:\"^sitemap-it\\.xml$\";s:24:\"index.php?dli_sitemap=it\";s:17:\"^sitemap-en\\.xml$\";s:24:\"index.php?dli_sitemap=en\";s:16:\"^manifest\\.json$\";s:26:\"index.php?dli_pwa=manifest\";s:52:\"(en)/category/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:69:\"index.php?lang=$matches[1]&category_name=$matches[2]&feed=$matches[3]\";s:47:\"category/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:47:\"(en)/category/(.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:69:\"index.php?lang=$matches[1]&category_name=$matches[2]&feed=$matches[3]\";s:42:\"category/(.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:28:\"(en)/category/(.+?)/embed/?$\";s:63:\"index.php?lang=$matches[1]&category_name=$matches[2]&embed=true\";s:23:\"category/(.+?)/embed/?$\";s:46:\"index.php?category_name=$matches[1]&embed=true\";s:40:\"(en)/category/(.+?)/page/?([0-9]{1,})/?$\";s:70:\"index.php?lang=$matches[1]&category_name=$matches[2]&paged=$matches[3]\";s:35:\"category/(.+?)/page/?([0-9]{1,})/?$\";s:53:\"index.php?category_name=$matches[1]&paged=$matches[2]\";s:22:\"(en)/category/(.+?)/?$\";s:52:\"index.php?lang=$matches[1]&category_name=$matches[2]\";s:17:\"category/(.+?)/?$\";s:35:\"index.php?category_name=$matches[1]\";s:55:\"(en)/argomento/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:59:\"index.php?lang=$matches[1]&tag=$matches[2]&feed=$matches[3]\";s:50:\"argomento/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:50:\"(en)/argomento/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:59:\"index.php?lang=$matches[1]&tag=$matches[2]&feed=$matches[3]\";s:45:\"argomento/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:31:\"(en)/argomento/([^/]+)/embed/?$\";s:53:\"index.php?lang=$matches[1]&tag=$matches[2]&embed=true\";s:26:\"argomento/([^/]+)/embed/?$\";s:36:\"index.php?tag=$matches[1]&embed=true\";s:43:\"(en)/argomento/([^/]+)/page/?([0-9]{1,})/?$\";s:60:\"index.php?lang=$matches[1]&tag=$matches[2]&paged=$matches[3]\";s:38:\"argomento/([^/]+)/page/?([0-9]{1,})/?$\";s:43:\"index.php?tag=$matches[1]&paged=$matches[2]\";s:25:\"(en)/argomento/([^/]+)/?$\";s:42:\"index.php?lang=$matches[1]&tag=$matches[2]\";s:20:\"argomento/([^/]+)/?$\";s:25:\"index.php?tag=$matches[1]\";s:50:\"(en)/type/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:67:\"index.php?lang=$matches[1]&post_format=$matches[2]&feed=$matches[3]\";s:45:\"type/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:58:\"index.php?lang=it&post_format=$matches[1]&feed=$matches[2]\";s:45:\"(en)/type/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:67:\"index.php?lang=$matches[1]&post_format=$matches[2]&feed=$matches[3]\";s:40:\"type/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:58:\"index.php?lang=it&post_format=$matches[1]&feed=$matches[2]\";s:26:\"(en)/type/([^/]+)/embed/?$\";s:61:\"index.php?lang=$matches[1]&post_format=$matches[2]&embed=true\";s:21:\"type/([^/]+)/embed/?$\";s:52:\"index.php?lang=it&post_format=$matches[1]&embed=true\";s:38:\"(en)/type/([^/]+)/page/?([0-9]{1,})/?$\";s:68:\"index.php?lang=$matches[1]&post_format=$matches[2]&paged=$matches[3]\";s:33:\"type/([^/]+)/page/?([0-9]{1,})/?$\";s:59:\"index.php?lang=it&post_format=$matches[1]&paged=$matches[2]\";s:20:\"(en)/type/([^/]+)/?$\";s:50:\"index.php?lang=$matches[1]&post_format=$matches[2]\";s:15:\"type/([^/]+)/?$\";s:41:\"index.php?lang=it&post_format=$matches[1]\";s:55:\"(en)/struttura/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:65:\"index.php?lang=$matches[1]&struttura=$matches[2]&feed=$matches[3]\";s:50:\"struttura/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:48:\"index.php?struttura=$matches[1]&feed=$matches[2]\";s:50:\"(en)/struttura/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:65:\"index.php?lang=$matches[1]&struttura=$matches[2]&feed=$matches[3]\";s:45:\"struttura/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:48:\"index.php?struttura=$matches[1]&feed=$matches[2]\";s:31:\"(en)/struttura/([^/]+)/embed/?$\";s:59:\"index.php?lang=$matches[1]&struttura=$matches[2]&embed=true\";s:26:\"struttura/([^/]+)/embed/?$\";s:42:\"index.php?struttura=$matches[1]&embed=true\";s:43:\"(en)/struttura/([^/]+)/page/?([0-9]{1,})/?$\";s:66:\"index.php?lang=$matches[1]&struttura=$matches[2]&paged=$matches[3]\";s:38:\"struttura/([^/]+)/page/?([0-9]{1,})/?$\";s:49:\"index.php?struttura=$matches[1]&paged=$matches[2]\";s:25:\"(en)/struttura/([^/]+)/?$\";s:48:\"index.php?lang=$matches[1]&struttura=$matches[2]\";s:20:\"struttura/([^/]+)/?$\";s:31:\"index.php?struttura=$matches[1]\";s:38:\"(en)/persone/.+?/attachment/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:33:\"persone/.+?/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:48:\"(en)/persone/.+?/attachment/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:43:\"persone/.+?/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:68:\"(en)/persone/.+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:63:\"persone/.+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:63:\"(en)/persone/.+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:58:\"persone/.+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:63:\"(en)/persone/.+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:58:\"persone/.+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:44:\"(en)/persone/.+?/attachment/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:39:\"persone/.+?/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:27:\"(en)/persone/(.+?)/embed/?$\";s:57:\"index.php?lang=$matches[1]&persona=$matches[2]&embed=true\";s:22:\"persone/(.+?)/embed/?$\";s:40:\"index.php?persona=$matches[1]&embed=true\";s:31:\"(en)/persone/(.+?)/trackback/?$\";s:51:\"index.php?lang=$matches[1]&persona=$matches[2]&tb=1\";s:26:\"persone/(.+?)/trackback/?$\";s:34:\"index.php?persona=$matches[1]&tb=1\";s:39:\"(en)/persone/(.+?)/page/?([0-9]{1,})/?$\";s:64:\"index.php?lang=$matches[1]&persona=$matches[2]&paged=$matches[3]\";s:34:\"persone/(.+?)/page/?([0-9]{1,})/?$\";s:47:\"index.php?persona=$matches[1]&paged=$matches[2]\";s:46:\"(en)/persone/(.+?)/comment-page-([0-9]{1,})/?$\";s:64:\"index.php?lang=$matches[1]&persona=$matches[2]&cpage=$matches[3]\";s:41:\"persone/(.+?)/comment-page-([0-9]{1,})/?$\";s:47:\"index.php?persona=$matches[1]&cpage=$matches[2]\";s:35:\"(en)/persone/(.+?)(?:/([0-9]+))?/?$\";s:63:\"index.php?lang=$matches[1]&persona=$matches[2]&page=$matches[3]\";s:30:\"persone/(.+?)(?:/([0-9]+))?/?$\";s:46:\"index.php?persona=$matches[1]&page=$matches[2]\";s:50:\"(en)/tipologia-persone/[^/]+/attachment/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:45:\"tipologia-persone/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:60:\"(en)/tipologia-persone/[^/]+/attachment/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:55:\"tipologia-persone/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:80:\"(en)/tipologia-persone/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:75:\"tipologia-persone/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:75:\"(en)/tipologia-persone/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:70:\"tipologia-persone/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:75:\"(en)/tipologia-persone/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:70:\"tipologia-persone/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:56:\"(en)/tipologia-persone/[^/]+/attachment/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:51:\"tipologia-persone/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:39:\"(en)/tipologia-persone/([^/]+)/embed/?$\";s:67:\"index.php?lang=$matches[1]&tipologia-persona=$matches[2]&embed=true\";s:34:\"tipologia-persone/([^/]+)/embed/?$\";s:50:\"index.php?tipologia-persona=$matches[1]&embed=true\";s:43:\"(en)/tipologia-persone/([^/]+)/trackback/?$\";s:61:\"index.php?lang=$matches[1]&tipologia-persona=$matches[2]&tb=1\";s:38:\"tipologia-persone/([^/]+)/trackback/?$\";s:44:\"index.php?tipologia-persona=$matches[1]&tb=1\";s:51:\"(en)/tipologia-persone/([^/]+)/page/?([0-9]{1,})/?$\";s:74:\"index.php?lang=$matches[1]&tipologia-persona=$matches[2]&paged=$matches[3]\";s:46:\"tipologia-persone/([^/]+)/page/?([0-9]{1,})/?$\";s:57:\"index.php?tipologia-persona=$matches[1]&paged=$matches[2]\";s:58:\"(en)/tipologia-persone/([^/]+)/comment-page-([0-9]{1,})/?$\";s:74:\"index.php?lang=$matches[1]&tipologia-persona=$matches[2]&cpage=$matches[3]\";s:53:\"tipologia-persone/([^/]+)/comment-page-([0-9]{1,})/?$\";s:57:\"index.php?tipologia-persona=$matches[1]&cpage=$matches[2]\";s:47:\"(en)/tipologia-persone/([^/]+)(?:/([0-9]+))?/?$\";s:73:\"index.php?lang=$matches[1]&tipologia-persona=$matches[2]&page=$matches[3]\";s:42:\"tipologia-persone/([^/]+)(?:/([0-9]+))?/?$\";s:56:\"index.php?tipologia-persona=$matches[1]&page=$matches[2]\";s:39:\"(en)/tipologia-persone/[^/]+/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:34:\"tipologia-persone/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:49:\"(en)/tipologia-persone/[^/]+/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:44:\"tipologia-persone/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:69:\"(en)/tipologia-persone/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:64:\"tipologia-persone/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:64:\"(en)/tipologia-persone/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:59:\"tipologia-persone/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:64:\"(en)/tipologia-persone/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:59:\"tipologia-persone/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:45:\"(en)/tipologia-persone/[^/]+/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:40:\"tipologia-persone/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:39:\"(en)/progetto/.+?/attachment/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:34:\"progetto/.+?/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:49:\"(en)/progetto/.+?/attachment/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:44:\"progetto/.+?/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:69:\"(en)/progetto/.+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:64:\"progetto/.+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:64:\"(en)/progetto/.+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:59:\"progetto/.+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:64:\"(en)/progetto/.+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:59:\"progetto/.+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:45:\"(en)/progetto/.+?/attachment/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:40:\"progetto/.+?/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:28:\"(en)/progetto/(.+?)/embed/?$\";s:58:\"index.php?lang=$matches[1]&progetto=$matches[2]&embed=true\";s:23:\"progetto/(.+?)/embed/?$\";s:41:\"index.php?progetto=$matches[1]&embed=true\";s:32:\"(en)/progetto/(.+?)/trackback/?$\";s:52:\"index.php?lang=$matches[1]&progetto=$matches[2]&tb=1\";s:27:\"progetto/(.+?)/trackback/?$\";s:35:\"index.php?progetto=$matches[1]&tb=1\";s:40:\"(en)/progetto/(.+?)/page/?([0-9]{1,})/?$\";s:65:\"index.php?lang=$matches[1]&progetto=$matches[2]&paged=$matches[3]\";s:35:\"progetto/(.+?)/page/?([0-9]{1,})/?$\";s:48:\"index.php?progetto=$matches[1]&paged=$matches[2]\";s:47:\"(en)/progetto/(.+?)/comment-page-([0-9]{1,})/?$\";s:65:\"index.php?lang=$matches[1]&progetto=$matches[2]&cpage=$matches[3]\";s:42:\"progetto/(.+?)/comment-page-([0-9]{1,})/?$\";s:48:\"index.php?progetto=$matches[1]&cpage=$matches[2]\";s:36:\"(en)/progetto/(.+?)(?:/([0-9]+))?/?$\";s:64:\"index.php?lang=$matches[1]&progetto=$matches[2]&page=$matches[3]\";s:31:\"progetto/(.+?)(?:/([0-9]+))?/?$\";s:47:\"index.php?progetto=$matches[1]&page=$matches[2]\";s:64:\"(en)/tipo-pubblicazione/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:74:\"index.php?lang=$matches[1]&tipo-pubblicazione=$matches[2]&feed=$matches[3]\";s:59:\"tipo-pubblicazione/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:57:\"index.php?tipo-pubblicazione=$matches[1]&feed=$matches[2]\";s:59:\"(en)/tipo-pubblicazione/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:74:\"index.php?lang=$matches[1]&tipo-pubblicazione=$matches[2]&feed=$matches[3]\";s:54:\"tipo-pubblicazione/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:57:\"index.php?tipo-pubblicazione=$matches[1]&feed=$matches[2]\";s:40:\"(en)/tipo-pubblicazione/([^/]+)/embed/?$\";s:68:\"index.php?lang=$matches[1]&tipo-pubblicazione=$matches[2]&embed=true\";s:35:\"tipo-pubblicazione/([^/]+)/embed/?$\";s:51:\"index.php?tipo-pubblicazione=$matches[1]&embed=true\";s:52:\"(en)/tipo-pubblicazione/([^/]+)/page/?([0-9]{1,})/?$\";s:75:\"index.php?lang=$matches[1]&tipo-pubblicazione=$matches[2]&paged=$matches[3]\";s:47:\"tipo-pubblicazione/([^/]+)/page/?([0-9]{1,})/?$\";s:58:\"index.php?tipo-pubblicazione=$matches[1]&paged=$matches[2]\";s:34:\"(en)/tipo-pubblicazione/([^/]+)/?$\";s:57:\"index.php?lang=$matches[1]&tipo-pubblicazione=$matches[2]\";s:29:\"tipo-pubblicazione/([^/]+)/?$\";s:40:\"index.php?tipo-pubblicazione=$matches[1]\";s:59:\"(en)/area-tematica/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:69:\"index.php?lang=$matches[1]&area-tematica=$matches[2]&feed=$matches[3]\";s:54:\"area-tematica/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?area-tematica=$matches[1]&feed=$matches[2]\";s:54:\"(en)/area-tematica/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:69:\"index.php?lang=$matches[1]&area-tematica=$matches[2]&feed=$matches[3]\";s:49:\"area-tematica/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?area-tematica=$matches[1]&feed=$matches[2]\";s:35:\"(en)/area-tematica/([^/]+)/embed/?$\";s:63:\"index.php?lang=$matches[1]&area-tematica=$matches[2]&embed=true\";s:30:\"area-tematica/([^/]+)/embed/?$\";s:46:\"index.php?area-tematica=$matches[1]&embed=true\";s:47:\"(en)/area-tematica/([^/]+)/page/?([0-9]{1,})/?$\";s:70:\"index.php?lang=$matches[1]&area-tematica=$matches[2]&paged=$matches[3]\";s:42:\"area-tematica/([^/]+)/page/?([0-9]{1,})/?$\";s:53:\"index.php?area-tematica=$matches[1]&paged=$matches[2]\";s:29:\"(en)/area-tematica/([^/]+)/?$\";s:52:\"index.php?lang=$matches[1]&area-tematica=$matches[2]\";s:24:\"area-tematica/([^/]+)/?$\";s:35:\"index.php?area-tematica=$matches[1]\";s:41:\"(en)/brevetto/[^/]+/attachment/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:36:\"brevetto/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:51:\"(en)/brevetto/[^/]+/attachment/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:46:\"brevetto/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:71:\"(en)/brevetto/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:66:\"brevetto/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:66:\"(en)/brevetto/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:61:\"brevetto/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:66:\"(en)/brevetto/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:61:\"brevetto/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:47:\"(en)/brevetto/[^/]+/attachment/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:42:\"brevetto/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:30:\"(en)/brevetto/([^/]+)/embed/?$\";s:58:\"index.php?lang=$matches[1]&brevetto=$matches[2]&embed=true\";s:25:\"brevetto/([^/]+)/embed/?$\";s:41:\"index.php?brevetto=$matches[1]&embed=true\";s:34:\"(en)/brevetto/([^/]+)/trackback/?$\";s:52:\"index.php?lang=$matches[1]&brevetto=$matches[2]&tb=1\";s:29:\"brevetto/([^/]+)/trackback/?$\";s:35:\"index.php?brevetto=$matches[1]&tb=1\";s:42:\"(en)/brevetto/([^/]+)/page/?([0-9]{1,})/?$\";s:65:\"index.php?lang=$matches[1]&brevetto=$matches[2]&paged=$matches[3]\";s:37:\"brevetto/([^/]+)/page/?([0-9]{1,})/?$\";s:48:\"index.php?brevetto=$matches[1]&paged=$matches[2]\";s:49:\"(en)/brevetto/([^/]+)/comment-page-([0-9]{1,})/?$\";s:65:\"index.php?lang=$matches[1]&brevetto=$matches[2]&cpage=$matches[3]\";s:44:\"brevetto/([^/]+)/comment-page-([0-9]{1,})/?$\";s:48:\"index.php?brevetto=$matches[1]&cpage=$matches[2]\";s:38:\"(en)/brevetto/([^/]+)(?:/([0-9]+))?/?$\";s:64:\"index.php?lang=$matches[1]&brevetto=$matches[2]&page=$matches[3]\";s:33:\"brevetto/([^/]+)(?:/([0-9]+))?/?$\";s:47:\"index.php?brevetto=$matches[1]&page=$matches[2]\";s:30:\"(en)/brevetto/[^/]+/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:25:\"brevetto/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:40:\"(en)/brevetto/[^/]+/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:35:\"brevetto/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:60:\"(en)/brevetto/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:55:\"brevetto/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:55:\"(en)/brevetto/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:50:\"brevetto/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:55:\"(en)/brevetto/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:50:\"brevetto/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:36:\"(en)/brevetto/[^/]+/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:31:\"brevetto/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:53:\"(en)/indirizzi-di-ricerca/[^/]+/attachment/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:48:\"indirizzi-di-ricerca/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:63:\"(en)/indirizzi-di-ricerca/[^/]+/attachment/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:58:\"indirizzi-di-ricerca/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:83:\"(en)/indirizzi-di-ricerca/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:78:\"indirizzi-di-ricerca/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:78:\"(en)/indirizzi-di-ricerca/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:73:\"indirizzi-di-ricerca/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:78:\"(en)/indirizzi-di-ricerca/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:73:\"indirizzi-di-ricerca/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:59:\"(en)/indirizzi-di-ricerca/[^/]+/attachment/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:54:\"indirizzi-di-ricerca/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:42:\"(en)/indirizzi-di-ricerca/([^/]+)/embed/?$\";s:70:\"index.php?lang=$matches[1]&indirizzo-di-ricerca=$matches[2]&embed=true\";s:37:\"indirizzi-di-ricerca/([^/]+)/embed/?$\";s:53:\"index.php?indirizzo-di-ricerca=$matches[1]&embed=true\";s:46:\"(en)/indirizzi-di-ricerca/([^/]+)/trackback/?$\";s:64:\"index.php?lang=$matches[1]&indirizzo-di-ricerca=$matches[2]&tb=1\";s:41:\"indirizzi-di-ricerca/([^/]+)/trackback/?$\";s:47:\"index.php?indirizzo-di-ricerca=$matches[1]&tb=1\";s:54:\"(en)/indirizzi-di-ricerca/([^/]+)/page/?([0-9]{1,})/?$\";s:77:\"index.php?lang=$matches[1]&indirizzo-di-ricerca=$matches[2]&paged=$matches[3]\";s:49:\"indirizzi-di-ricerca/([^/]+)/page/?([0-9]{1,})/?$\";s:60:\"index.php?indirizzo-di-ricerca=$matches[1]&paged=$matches[2]\";s:61:\"(en)/indirizzi-di-ricerca/([^/]+)/comment-page-([0-9]{1,})/?$\";s:77:\"index.php?lang=$matches[1]&indirizzo-di-ricerca=$matches[2]&cpage=$matches[3]\";s:56:\"indirizzi-di-ricerca/([^/]+)/comment-page-([0-9]{1,})/?$\";s:60:\"index.php?indirizzo-di-ricerca=$matches[1]&cpage=$matches[2]\";s:50:\"(en)/indirizzi-di-ricerca/([^/]+)(?:/([0-9]+))?/?$\";s:76:\"index.php?lang=$matches[1]&indirizzo-di-ricerca=$matches[2]&page=$matches[3]\";s:45:\"indirizzi-di-ricerca/([^/]+)(?:/([0-9]+))?/?$\";s:59:\"index.php?indirizzo-di-ricerca=$matches[1]&page=$matches[2]\";s:42:\"(en)/indirizzi-di-ricerca/[^/]+/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:37:\"indirizzi-di-ricerca/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:52:\"(en)/indirizzi-di-ricerca/[^/]+/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:47:\"indirizzi-di-ricerca/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:72:\"(en)/indirizzi-di-ricerca/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:67:\"indirizzi-di-ricerca/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:67:\"(en)/indirizzi-di-ricerca/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:62:\"indirizzi-di-ricerca/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:67:\"(en)/indirizzi-di-ricerca/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:62:\"indirizzi-di-ricerca/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:48:\"(en)/indirizzi-di-ricerca/[^/]+/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:43:\"indirizzi-di-ricerca/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:40:\"(en)/notizia/[^/]+/attachment/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:35:\"notizia/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:50:\"(en)/notizia/[^/]+/attachment/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:45:\"notizia/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:70:\"(en)/notizia/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:65:\"notizia/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:65:\"(en)/notizia/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:60:\"notizia/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:65:\"(en)/notizia/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:60:\"notizia/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:46:\"(en)/notizia/[^/]+/attachment/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:41:\"notizia/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:29:\"(en)/notizia/([^/]+)/embed/?$\";s:57:\"index.php?lang=$matches[1]&notizia=$matches[2]&embed=true\";s:24:\"notizia/([^/]+)/embed/?$\";s:40:\"index.php?notizia=$matches[1]&embed=true\";s:33:\"(en)/notizia/([^/]+)/trackback/?$\";s:51:\"index.php?lang=$matches[1]&notizia=$matches[2]&tb=1\";s:28:\"notizia/([^/]+)/trackback/?$\";s:34:\"index.php?notizia=$matches[1]&tb=1\";s:41:\"(en)/notizia/([^/]+)/page/?([0-9]{1,})/?$\";s:64:\"index.php?lang=$matches[1]&notizia=$matches[2]&paged=$matches[3]\";s:36:\"notizia/([^/]+)/page/?([0-9]{1,})/?$\";s:47:\"index.php?notizia=$matches[1]&paged=$matches[2]\";s:48:\"(en)/notizia/([^/]+)/comment-page-([0-9]{1,})/?$\";s:64:\"index.php?lang=$matches[1]&notizia=$matches[2]&cpage=$matches[3]\";s:43:\"notizia/([^/]+)/comment-page-([0-9]{1,})/?$\";s:47:\"index.php?notizia=$matches[1]&cpage=$matches[2]\";s:37:\"(en)/notizia/([^/]+)(?:/([0-9]+))?/?$\";s:63:\"index.php?lang=$matches[1]&notizia=$matches[2]&page=$matches[3]\";s:32:\"notizia/([^/]+)(?:/([0-9]+))?/?$\";s:46:\"index.php?notizia=$matches[1]&page=$matches[2]\";s:29:\"(en)/notizia/[^/]+/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:24:\"notizia/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:39:\"(en)/notizia/[^/]+/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:34:\"notizia/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:59:\"(en)/notizia/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:54:\"notizia/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:54:\"(en)/notizia/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:49:\"notizia/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:54:\"(en)/notizia/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:49:\"notizia/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:35:\"(en)/notizia/[^/]+/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:30:\"notizia/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:39:\"(en)/evento/[^/]+/attachment/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:34:\"evento/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:49:\"(en)/evento/[^/]+/attachment/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:44:\"evento/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:69:\"(en)/evento/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:64:\"evento/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:64:\"(en)/evento/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:59:\"evento/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:64:\"(en)/evento/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:59:\"evento/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:45:\"(en)/evento/[^/]+/attachment/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:40:\"evento/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:28:\"(en)/evento/([^/]+)/embed/?$\";s:56:\"index.php?lang=$matches[1]&evento=$matches[2]&embed=true\";s:23:\"evento/([^/]+)/embed/?$\";s:39:\"index.php?evento=$matches[1]&embed=true\";s:32:\"(en)/evento/([^/]+)/trackback/?$\";s:50:\"index.php?lang=$matches[1]&evento=$matches[2]&tb=1\";s:27:\"evento/([^/]+)/trackback/?$\";s:33:\"index.php?evento=$matches[1]&tb=1\";s:40:\"(en)/evento/([^/]+)/page/?([0-9]{1,})/?$\";s:63:\"index.php?lang=$matches[1]&evento=$matches[2]&paged=$matches[3]\";s:35:\"evento/([^/]+)/page/?([0-9]{1,})/?$\";s:46:\"index.php?evento=$matches[1]&paged=$matches[2]\";s:47:\"(en)/evento/([^/]+)/comment-page-([0-9]{1,})/?$\";s:63:\"index.php?lang=$matches[1]&evento=$matches[2]&cpage=$matches[3]\";s:42:\"evento/([^/]+)/comment-page-([0-9]{1,})/?$\";s:46:\"index.php?evento=$matches[1]&cpage=$matches[2]\";s:36:\"(en)/evento/([^/]+)(?:/([0-9]+))?/?$\";s:62:\"index.php?lang=$matches[1]&evento=$matches[2]&page=$matches[3]\";s:31:\"evento/([^/]+)(?:/([0-9]+))?/?$\";s:45:\"index.php?evento=$matches[1]&page=$matches[2]\";s:28:\"(en)/evento/[^/]+/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:23:\"evento/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:38:\"(en)/evento/[^/]+/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:33:\"evento/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:58:\"(en)/evento/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:53:\"evento/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:53:\"(en)/evento/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:48:\"evento/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:53:\"(en)/evento/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:48:\"evento/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:34:\"(en)/evento/[^/]+/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:29:\"evento/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:61:\"(en)/tipologia-luogo/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:71:\"index.php?lang=$matches[1]&tipologia-luogo=$matches[2]&feed=$matches[3]\";s:56:\"tipologia-luogo/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:54:\"index.php?tipologia-luogo=$matches[1]&feed=$matches[2]\";s:56:\"(en)/tipologia-luogo/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:71:\"index.php?lang=$matches[1]&tipologia-luogo=$matches[2]&feed=$matches[3]\";s:51:\"tipologia-luogo/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:54:\"index.php?tipologia-luogo=$matches[1]&feed=$matches[2]\";s:37:\"(en)/tipologia-luogo/([^/]+)/embed/?$\";s:65:\"index.php?lang=$matches[1]&tipologia-luogo=$matches[2]&embed=true\";s:32:\"tipologia-luogo/([^/]+)/embed/?$\";s:48:\"index.php?tipologia-luogo=$matches[1]&embed=true\";s:49:\"(en)/tipologia-luogo/([^/]+)/page/?([0-9]{1,})/?$\";s:72:\"index.php?lang=$matches[1]&tipologia-luogo=$matches[2]&paged=$matches[3]\";s:44:\"tipologia-luogo/([^/]+)/page/?([0-9]{1,})/?$\";s:55:\"index.php?tipologia-luogo=$matches[1]&paged=$matches[2]\";s:31:\"(en)/tipologia-luogo/([^/]+)/?$\";s:54:\"index.php?lang=$matches[1]&tipologia-luogo=$matches[2]\";s:26:\"tipologia-luogo/([^/]+)/?$\";s:37:\"index.php?tipologia-luogo=$matches[1]\";s:37:\"(en)/luoghi/.+?/attachment/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:32:\"luoghi/.+?/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:47:\"(en)/luoghi/.+?/attachment/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:42:\"luoghi/.+?/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:67:\"(en)/luoghi/.+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:62:\"luoghi/.+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:62:\"(en)/luoghi/.+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:57:\"luoghi/.+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:62:\"(en)/luoghi/.+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:57:\"luoghi/.+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:43:\"(en)/luoghi/.+?/attachment/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:38:\"luoghi/.+?/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:26:\"(en)/luoghi/(.+?)/embed/?$\";s:55:\"index.php?lang=$matches[1]&luogo=$matches[2]&embed=true\";s:21:\"luoghi/(.+?)/embed/?$\";s:38:\"index.php?luogo=$matches[1]&embed=true\";s:30:\"(en)/luoghi/(.+?)/trackback/?$\";s:49:\"index.php?lang=$matches[1]&luogo=$matches[2]&tb=1\";s:25:\"luoghi/(.+?)/trackback/?$\";s:32:\"index.php?luogo=$matches[1]&tb=1\";s:38:\"(en)/luoghi/(.+?)/page/?([0-9]{1,})/?$\";s:62:\"index.php?lang=$matches[1]&luogo=$matches[2]&paged=$matches[3]\";s:33:\"luoghi/(.+?)/page/?([0-9]{1,})/?$\";s:45:\"index.php?luogo=$matches[1]&paged=$matches[2]\";s:45:\"(en)/luoghi/(.+?)/comment-page-([0-9]{1,})/?$\";s:62:\"index.php?lang=$matches[1]&luogo=$matches[2]&cpage=$matches[3]\";s:40:\"luoghi/(.+?)/comment-page-([0-9]{1,})/?$\";s:45:\"index.php?luogo=$matches[1]&cpage=$matches[2]\";s:34:\"(en)/luoghi/(.+?)(?:/([0-9]+))?/?$\";s:61:\"index.php?lang=$matches[1]&luogo=$matches[2]&page=$matches[3]\";s:29:\"luoghi/(.+?)(?:/([0-9]+))?/?$\";s:44:\"index.php?luogo=$matches[1]&page=$matches[2]\";s:39:\"(en)/banner/[^/]+/attachment/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:34:\"banner/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:49:\"(en)/banner/[^/]+/attachment/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:44:\"banner/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:69:\"(en)/banner/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:64:\"banner/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:64:\"(en)/banner/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:59:\"banner/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:64:\"(en)/banner/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:59:\"banner/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:45:\"(en)/banner/[^/]+/attachment/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:40:\"banner/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:28:\"(en)/banner/([^/]+)/embed/?$\";s:56:\"index.php?lang=$matches[1]&banner=$matches[2]&embed=true\";s:23:\"banner/([^/]+)/embed/?$\";s:39:\"index.php?banner=$matches[1]&embed=true\";s:32:\"(en)/banner/([^/]+)/trackback/?$\";s:50:\"index.php?lang=$matches[1]&banner=$matches[2]&tb=1\";s:27:\"banner/([^/]+)/trackback/?$\";s:33:\"index.php?banner=$matches[1]&tb=1\";s:40:\"(en)/banner/([^/]+)/page/?([0-9]{1,})/?$\";s:63:\"index.php?lang=$matches[1]&banner=$matches[2]&paged=$matches[3]\";s:35:\"banner/([^/]+)/page/?([0-9]{1,})/?$\";s:46:\"index.php?banner=$matches[1]&paged=$matches[2]\";s:47:\"(en)/banner/([^/]+)/comment-page-([0-9]{1,})/?$\";s:63:\"index.php?lang=$matches[1]&banner=$matches[2]&cpage=$matches[3]\";s:42:\"banner/([^/]+)/comment-page-([0-9]{1,})/?$\";s:46:\"index.php?banner=$matches[1]&cpage=$matches[2]\";s:36:\"(en)/banner/([^/]+)(?:/([0-9]+))?/?$\";s:62:\"index.php?lang=$matches[1]&banner=$matches[2]&page=$matches[3]\";s:31:\"banner/([^/]+)(?:/([0-9]+))?/?$\";s:45:\"index.php?banner=$matches[1]&page=$matches[2]\";s:28:\"(en)/banner/[^/]+/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:23:\"banner/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:38:\"(en)/banner/[^/]+/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:33:\"banner/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:58:\"(en)/banner/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:53:\"banner/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:53:\"(en)/banner/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:48:\"banner/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:53:\"(en)/banner/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:48:\"banner/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:34:\"(en)/banner/[^/]+/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:29:\"banner/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:57:\"settore-attivita/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:55:\"index.php?settore-attivita=$matches[1]&feed=$matches[2]\";s:52:\"settore-attivita/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:55:\"index.php?settore-attivita=$matches[1]&feed=$matches[2]\";s:33:\"settore-attivita/([^/]+)/embed/?$\";s:49:\"index.php?settore-attivita=$matches[1]&embed=true\";s:45:\"settore-attivita/([^/]+)/page/?([0-9]{1,})/?$\";s:56:\"index.php?settore-attivita=$matches[1]&paged=$matches[2]\";s:27:\"settore-attivita/([^/]+)/?$\";s:38:\"index.php?settore-attivita=$matches[1]\";s:40:\"(en)/spinoff/[^/]+/attachment/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:35:\"spinoff/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:50:\"(en)/spinoff/[^/]+/attachment/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:45:\"spinoff/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:70:\"(en)/spinoff/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:65:\"spinoff/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:65:\"(en)/spinoff/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:60:\"spinoff/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:65:\"(en)/spinoff/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:60:\"spinoff/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:46:\"(en)/spinoff/[^/]+/attachment/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:41:\"spinoff/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:29:\"(en)/spinoff/([^/]+)/embed/?$\";s:57:\"index.php?lang=$matches[1]&spinoff=$matches[2]&embed=true\";s:24:\"spinoff/([^/]+)/embed/?$\";s:40:\"index.php?spinoff=$matches[1]&embed=true\";s:33:\"(en)/spinoff/([^/]+)/trackback/?$\";s:51:\"index.php?lang=$matches[1]&spinoff=$matches[2]&tb=1\";s:28:\"spinoff/([^/]+)/trackback/?$\";s:34:\"index.php?spinoff=$matches[1]&tb=1\";s:41:\"(en)/spinoff/([^/]+)/page/?([0-9]{1,})/?$\";s:64:\"index.php?lang=$matches[1]&spinoff=$matches[2]&paged=$matches[3]\";s:36:\"spinoff/([^/]+)/page/?([0-9]{1,})/?$\";s:47:\"index.php?spinoff=$matches[1]&paged=$matches[2]\";s:48:\"(en)/spinoff/([^/]+)/comment-page-([0-9]{1,})/?$\";s:64:\"index.php?lang=$matches[1]&spinoff=$matches[2]&cpage=$matches[3]\";s:43:\"spinoff/([^/]+)/comment-page-([0-9]{1,})/?$\";s:47:\"index.php?spinoff=$matches[1]&cpage=$matches[2]\";s:37:\"(en)/spinoff/([^/]+)(?:/([0-9]+))?/?$\";s:63:\"index.php?lang=$matches[1]&spinoff=$matches[2]&page=$matches[3]\";s:32:\"spinoff/([^/]+)(?:/([0-9]+))?/?$\";s:46:\"index.php?spinoff=$matches[1]&page=$matches[2]\";s:29:\"(en)/spinoff/[^/]+/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:24:\"spinoff/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:39:\"(en)/spinoff/[^/]+/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:34:\"spinoff/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:59:\"(en)/spinoff/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:54:\"spinoff/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:54:\"(en)/spinoff/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:49:\"spinoff/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:54:\"(en)/spinoff/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:49:\"spinoff/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:35:\"(en)/spinoff/[^/]+/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:30:\"spinoff/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:40:\"(en)/sponsor/[^/]+/attachment/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:35:\"sponsor/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:50:\"(en)/sponsor/[^/]+/attachment/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:45:\"sponsor/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:70:\"(en)/sponsor/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:65:\"sponsor/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:65:\"(en)/sponsor/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:60:\"sponsor/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:65:\"(en)/sponsor/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:60:\"sponsor/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:46:\"(en)/sponsor/[^/]+/attachment/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:41:\"sponsor/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:29:\"(en)/sponsor/([^/]+)/embed/?$\";s:57:\"index.php?lang=$matches[1]&sponsor=$matches[2]&embed=true\";s:24:\"sponsor/([^/]+)/embed/?$\";s:40:\"index.php?sponsor=$matches[1]&embed=true\";s:33:\"(en)/sponsor/([^/]+)/trackback/?$\";s:51:\"index.php?lang=$matches[1]&sponsor=$matches[2]&tb=1\";s:28:\"sponsor/([^/]+)/trackback/?$\";s:34:\"index.php?sponsor=$matches[1]&tb=1\";s:41:\"(en)/sponsor/([^/]+)/page/?([0-9]{1,})/?$\";s:64:\"index.php?lang=$matches[1]&sponsor=$matches[2]&paged=$matches[3]\";s:36:\"sponsor/([^/]+)/page/?([0-9]{1,})/?$\";s:47:\"index.php?sponsor=$matches[1]&paged=$matches[2]\";s:48:\"(en)/sponsor/([^/]+)/comment-page-([0-9]{1,})/?$\";s:64:\"index.php?lang=$matches[1]&sponsor=$matches[2]&cpage=$matches[3]\";s:43:\"sponsor/([^/]+)/comment-page-([0-9]{1,})/?$\";s:47:\"index.php?sponsor=$matches[1]&cpage=$matches[2]\";s:37:\"(en)/sponsor/([^/]+)(?:/([0-9]+))?/?$\";s:63:\"index.php?lang=$matches[1]&sponsor=$matches[2]&page=$matches[3]\";s:32:\"sponsor/([^/]+)(?:/([0-9]+))?/?$\";s:46:\"index.php?sponsor=$matches[1]&page=$matches[2]\";s:29:\"(en)/sponsor/[^/]+/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:24:\"sponsor/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:39:\"(en)/sponsor/[^/]+/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:34:\"sponsor/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:59:\"(en)/sponsor/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:54:\"sponsor/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:54:\"(en)/sponsor/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:49:\"sponsor/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:54:\"(en)/sponsor/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:49:\"sponsor/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:35:\"(en)/sponsor/[^/]+/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:30:\"sponsor/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:66:\"(en)/tipo-risorsa-tecnica/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:76:\"index.php?lang=$matches[1]&tipo-risorsa-tecnica=$matches[2]&feed=$matches[3]\";s:61:\"tipo-risorsa-tecnica/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:59:\"index.php?tipo-risorsa-tecnica=$matches[1]&feed=$matches[2]\";s:61:\"(en)/tipo-risorsa-tecnica/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:76:\"index.php?lang=$matches[1]&tipo-risorsa-tecnica=$matches[2]&feed=$matches[3]\";s:56:\"tipo-risorsa-tecnica/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:59:\"index.php?tipo-risorsa-tecnica=$matches[1]&feed=$matches[2]\";s:42:\"(en)/tipo-risorsa-tecnica/([^/]+)/embed/?$\";s:70:\"index.php?lang=$matches[1]&tipo-risorsa-tecnica=$matches[2]&embed=true\";s:37:\"tipo-risorsa-tecnica/([^/]+)/embed/?$\";s:53:\"index.php?tipo-risorsa-tecnica=$matches[1]&embed=true\";s:54:\"(en)/tipo-risorsa-tecnica/([^/]+)/page/?([0-9]{1,})/?$\";s:77:\"index.php?lang=$matches[1]&tipo-risorsa-tecnica=$matches[2]&paged=$matches[3]\";s:49:\"tipo-risorsa-tecnica/([^/]+)/page/?([0-9]{1,})/?$\";s:60:\"index.php?tipo-risorsa-tecnica=$matches[1]&paged=$matches[2]\";s:36:\"(en)/tipo-risorsa-tecnica/([^/]+)/?$\";s:59:\"index.php?lang=$matches[1]&tipo-risorsa-tecnica=$matches[2]\";s:31:\"tipo-risorsa-tecnica/([^/]+)/?$\";s:42:\"index.php?tipo-risorsa-tecnica=$matches[1]\";s:48:\"(en)/risorsa-tecnica/[^/]+/attachment/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:43:\"risorsa-tecnica/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:58:\"(en)/risorsa-tecnica/[^/]+/attachment/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:53:\"risorsa-tecnica/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:78:\"(en)/risorsa-tecnica/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:73:\"risorsa-tecnica/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:73:\"(en)/risorsa-tecnica/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:68:\"risorsa-tecnica/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:73:\"(en)/risorsa-tecnica/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:68:\"risorsa-tecnica/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:54:\"(en)/risorsa-tecnica/[^/]+/attachment/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:49:\"risorsa-tecnica/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:37:\"(en)/risorsa-tecnica/([^/]+)/embed/?$\";s:65:\"index.php?lang=$matches[1]&risorsa-tecnica=$matches[2]&embed=true\";s:32:\"risorsa-tecnica/([^/]+)/embed/?$\";s:48:\"index.php?risorsa-tecnica=$matches[1]&embed=true\";s:41:\"(en)/risorsa-tecnica/([^/]+)/trackback/?$\";s:59:\"index.php?lang=$matches[1]&risorsa-tecnica=$matches[2]&tb=1\";s:36:\"risorsa-tecnica/([^/]+)/trackback/?$\";s:42:\"index.php?risorsa-tecnica=$matches[1]&tb=1\";s:49:\"(en)/risorsa-tecnica/([^/]+)/page/?([0-9]{1,})/?$\";s:72:\"index.php?lang=$matches[1]&risorsa-tecnica=$matches[2]&paged=$matches[3]\";s:44:\"risorsa-tecnica/([^/]+)/page/?([0-9]{1,})/?$\";s:55:\"index.php?risorsa-tecnica=$matches[1]&paged=$matches[2]\";s:56:\"(en)/risorsa-tecnica/([^/]+)/comment-page-([0-9]{1,})/?$\";s:72:\"index.php?lang=$matches[1]&risorsa-tecnica=$matches[2]&cpage=$matches[3]\";s:51:\"risorsa-tecnica/([^/]+)/comment-page-([0-9]{1,})/?$\";s:55:\"index.php?risorsa-tecnica=$matches[1]&cpage=$matches[2]\";s:45:\"(en)/risorsa-tecnica/([^/]+)(?:/([0-9]+))?/?$\";s:71:\"index.php?lang=$matches[1]&risorsa-tecnica=$matches[2]&page=$matches[3]\";s:40:\"risorsa-tecnica/([^/]+)(?:/([0-9]+))?/?$\";s:54:\"index.php?risorsa-tecnica=$matches[1]&page=$matches[2]\";s:37:\"(en)/risorsa-tecnica/[^/]+/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:32:\"risorsa-tecnica/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:47:\"(en)/risorsa-tecnica/[^/]+/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:42:\"risorsa-tecnica/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:67:\"(en)/risorsa-tecnica/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:62:\"risorsa-tecnica/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:62:\"(en)/risorsa-tecnica/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:57:\"risorsa-tecnica/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:62:\"(en)/risorsa-tecnica/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:57:\"risorsa-tecnica/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:43:\"(en)/risorsa-tecnica/[^/]+/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:38:\"risorsa-tecnica/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:12:\"robots\\.txt$\";s:18:\"index.php?robots=1\";s:13:\"favicon\\.ico$\";s:19:\"index.php?favicon=1\";s:12:\"sitemap\\.xml\";s:23:\"index.php?sitemap=index\";s:48:\".*wp-(atom|rdf|rss|rss2|feed|commentsrss2)\\.php$\";s:18:\"index.php?feed=old\";s:20:\".*wp-app\\.php(/.*)?$\";s:19:\"index.php?error=403\";s:18:\".*wp-register.php$\";s:23:\"index.php?register=true\";s:37:\"(en)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:44:\"index.php?lang=$matches[1]&&feed=$matches[2]\";s:32:\"feed/(feed|rdf|rss|rss2|atom)/?$\";s:35:\"index.php?lang=it&&feed=$matches[1]\";s:32:\"(en)/(feed|rdf|rss|rss2|atom)/?$\";s:44:\"index.php?lang=$matches[1]&&feed=$matches[2]\";s:27:\"(feed|rdf|rss|rss2|atom)/?$\";s:35:\"index.php?lang=it&&feed=$matches[1]\";s:13:\"(en)/embed/?$\";s:38:\"index.php?lang=$matches[1]&&embed=true\";s:8:\"embed/?$\";s:29:\"index.php?lang=it&&embed=true\";s:25:\"(en)/page/?([0-9]{1,})/?$\";s:45:\"index.php?lang=$matches[1]&&paged=$matches[2]\";s:20:\"page/?([0-9]{1,})/?$\";s:36:\"index.php?lang=it&&paged=$matches[1]\";s:7:\"(en)/?$\";s:26:\"index.php?lang=$matches[1]\";s:46:\"(en)/comments/feed/(feed|rdf|rss|rss2|atom)/?$\";s:59:\"index.php?lang=$matches[1]&&feed=$matches[2]&withcomments=1\";s:41:\"comments/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?lang=it&&feed=$matches[1]&withcomments=1\";s:41:\"(en)/comments/(feed|rdf|rss|rss2|atom)/?$\";s:59:\"index.php?lang=$matches[1]&&feed=$matches[2]&withcomments=1\";s:36:\"comments/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?lang=it&&feed=$matches[1]&withcomments=1\";s:22:\"(en)/comments/embed/?$\";s:38:\"index.php?lang=$matches[1]&&embed=true\";s:17:\"comments/embed/?$\";s:29:\"index.php?lang=it&&embed=true\";s:49:\"(en)/search/(.+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:57:\"index.php?lang=$matches[1]&s=$matches[2]&feed=$matches[3]\";s:44:\"search/(.+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:48:\"index.php?lang=it&s=$matches[1]&feed=$matches[2]\";s:44:\"(en)/search/(.+)/(feed|rdf|rss|rss2|atom)/?$\";s:57:\"index.php?lang=$matches[1]&s=$matches[2]&feed=$matches[3]\";s:39:\"search/(.+)/(feed|rdf|rss|rss2|atom)/?$\";s:48:\"index.php?lang=it&s=$matches[1]&feed=$matches[2]\";s:25:\"(en)/search/(.+)/embed/?$\";s:51:\"index.php?lang=$matches[1]&s=$matches[2]&embed=true\";s:20:\"search/(.+)/embed/?$\";s:42:\"index.php?lang=it&s=$matches[1]&embed=true\";s:37:\"(en)/search/(.+)/page/?([0-9]{1,})/?$\";s:58:\"index.php?lang=$matches[1]&s=$matches[2]&paged=$matches[3]\";s:32:\"search/(.+)/page/?([0-9]{1,})/?$\";s:49:\"index.php?lang=it&s=$matches[1]&paged=$matches[2]\";s:19:\"(en)/search/(.+)/?$\";s:40:\"index.php?lang=$matches[1]&s=$matches[2]\";s:14:\"search/(.+)/?$\";s:31:\"index.php?lang=it&s=$matches[1]\";s:52:\"(en)/author/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:67:\"index.php?lang=$matches[1]&author_name=$matches[2]&feed=$matches[3]\";s:47:\"author/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:58:\"index.php?lang=it&author_name=$matches[1]&feed=$matches[2]\";s:47:\"(en)/author/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:67:\"index.php?lang=$matches[1]&author_name=$matches[2]&feed=$matches[3]\";s:42:\"author/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:58:\"index.php?lang=it&author_name=$matches[1]&feed=$matches[2]\";s:28:\"(en)/author/([^/]+)/embed/?$\";s:61:\"index.php?lang=$matches[1]&author_name=$matches[2]&embed=true\";s:23:\"author/([^/]+)/embed/?$\";s:52:\"index.php?lang=it&author_name=$matches[1]&embed=true\";s:40:\"(en)/author/([^/]+)/page/?([0-9]{1,})/?$\";s:68:\"index.php?lang=$matches[1]&author_name=$matches[2]&paged=$matches[3]\";s:35:\"author/([^/]+)/page/?([0-9]{1,})/?$\";s:59:\"index.php?lang=it&author_name=$matches[1]&paged=$matches[2]\";s:22:\"(en)/author/([^/]+)/?$\";s:50:\"index.php?lang=$matches[1]&author_name=$matches[2]\";s:17:\"author/([^/]+)/?$\";s:41:\"index.php?lang=it&author_name=$matches[1]\";s:74:\"(en)/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:97:\"index.php?lang=$matches[1]&year=$matches[2]&monthnum=$matches[3]&day=$matches[4]&feed=$matches[5]\";s:69:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:88:\"index.php?lang=it&year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:69:\"(en)/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:97:\"index.php?lang=$matches[1]&year=$matches[2]&monthnum=$matches[3]&day=$matches[4]&feed=$matches[5]\";s:64:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:88:\"index.php?lang=it&year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:50:\"(en)/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/embed/?$\";s:91:\"index.php?lang=$matches[1]&year=$matches[2]&monthnum=$matches[3]&day=$matches[4]&embed=true\";s:45:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/embed/?$\";s:82:\"index.php?lang=it&year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&embed=true\";s:62:\"(en)/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:98:\"index.php?lang=$matches[1]&year=$matches[2]&monthnum=$matches[3]&day=$matches[4]&paged=$matches[5]\";s:57:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:89:\"index.php?lang=it&year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]\";s:44:\"(en)/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$\";s:80:\"index.php?lang=$matches[1]&year=$matches[2]&monthnum=$matches[3]&day=$matches[4]\";s:39:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$\";s:71:\"index.php?lang=it&year=$matches[1]&monthnum=$matches[2]&day=$matches[3]\";s:61:\"(en)/([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:81:\"index.php?lang=$matches[1]&year=$matches[2]&monthnum=$matches[3]&feed=$matches[4]\";s:56:\"([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:72:\"index.php?lang=it&year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:56:\"(en)/([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:81:\"index.php?lang=$matches[1]&year=$matches[2]&monthnum=$matches[3]&feed=$matches[4]\";s:51:\"([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:72:\"index.php?lang=it&year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:37:\"(en)/([0-9]{4})/([0-9]{1,2})/embed/?$\";s:75:\"index.php?lang=$matches[1]&year=$matches[2]&monthnum=$matches[3]&embed=true\";s:32:\"([0-9]{4})/([0-9]{1,2})/embed/?$\";s:66:\"index.php?lang=it&year=$matches[1]&monthnum=$matches[2]&embed=true\";s:49:\"(en)/([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:82:\"index.php?lang=$matches[1]&year=$matches[2]&monthnum=$matches[3]&paged=$matches[4]\";s:44:\"([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:73:\"index.php?lang=it&year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]\";s:31:\"(en)/([0-9]{4})/([0-9]{1,2})/?$\";s:64:\"index.php?lang=$matches[1]&year=$matches[2]&monthnum=$matches[3]\";s:26:\"([0-9]{4})/([0-9]{1,2})/?$\";s:55:\"index.php?lang=it&year=$matches[1]&monthnum=$matches[2]\";s:48:\"(en)/([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:60:\"index.php?lang=$matches[1]&year=$matches[2]&feed=$matches[3]\";s:43:\"([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:51:\"index.php?lang=it&year=$matches[1]&feed=$matches[2]\";s:43:\"(en)/([0-9]{4})/(feed|rdf|rss|rss2|atom)/?$\";s:60:\"index.php?lang=$matches[1]&year=$matches[2]&feed=$matches[3]\";s:38:\"([0-9]{4})/(feed|rdf|rss|rss2|atom)/?$\";s:51:\"index.php?lang=it&year=$matches[1]&feed=$matches[2]\";s:24:\"(en)/([0-9]{4})/embed/?$\";s:54:\"index.php?lang=$matches[1]&year=$matches[2]&embed=true\";s:19:\"([0-9]{4})/embed/?$\";s:45:\"index.php?lang=it&year=$matches[1]&embed=true\";s:36:\"(en)/([0-9]{4})/page/?([0-9]{1,})/?$\";s:61:\"index.php?lang=$matches[1]&year=$matches[2]&paged=$matches[3]\";s:31:\"([0-9]{4})/page/?([0-9]{1,})/?$\";s:52:\"index.php?lang=it&year=$matches[1]&paged=$matches[2]\";s:18:\"(en)/([0-9]{4})/?$\";s:43:\"index.php?lang=$matches[1]&year=$matches[2]\";s:13:\"([0-9]{4})/?$\";s:34:\"index.php?lang=it&year=$matches[1]\";s:32:\"(en)/.?.+?/attachment/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:27:\".?.+?/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:42:\"(en)/.?.+?/attachment/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:37:\".?.+?/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:62:\"(en)/.?.+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:57:\".?.+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:57:\"(en)/.?.+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:52:\".?.+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:57:\"(en)/.?.+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:52:\".?.+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:38:\"(en)/.?.+?/attachment/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:33:\".?.+?/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:21:\"(en)/(.?.+?)/embed/?$\";s:58:\"index.php?lang=$matches[1]&pagename=$matches[2]&embed=true\";s:16:\"(.?.+?)/embed/?$\";s:41:\"index.php?pagename=$matches[1]&embed=true\";s:25:\"(en)/(.?.+?)/trackback/?$\";s:52:\"index.php?lang=$matches[1]&pagename=$matches[2]&tb=1\";s:20:\"(.?.+?)/trackback/?$\";s:35:\"index.php?pagename=$matches[1]&tb=1\";s:45:\"(en)/(.?.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:64:\"index.php?lang=$matches[1]&pagename=$matches[2]&feed=$matches[3]\";s:40:\"(.?.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:40:\"(en)/(.?.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:64:\"index.php?lang=$matches[1]&pagename=$matches[2]&feed=$matches[3]\";s:35:\"(.?.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:33:\"(en)/(.?.+?)/page/?([0-9]{1,})/?$\";s:65:\"index.php?lang=$matches[1]&pagename=$matches[2]&paged=$matches[3]\";s:28:\"(.?.+?)/page/?([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&paged=$matches[2]\";s:40:\"(en)/(.?.+?)/comment-page-([0-9]{1,})/?$\";s:65:\"index.php?lang=$matches[1]&pagename=$matches[2]&cpage=$matches[3]\";s:35:\"(.?.+?)/comment-page-([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&cpage=$matches[2]\";s:29:\"(en)/(.?.+?)(?:/([0-9]+))?/?$\";s:64:\"index.php?lang=$matches[1]&pagename=$matches[2]&page=$matches[3]\";s:24:\"(.?.+?)(?:/([0-9]+))?/?$\";s:47:\"index.php?pagename=$matches[1]&page=$matches[2]\";s:32:\"(en)/[^/]+/attachment/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:27:\"[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:42:\"(en)/[^/]+/attachment/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:37:\"[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:62:\"(en)/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:57:\"[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:57:\"(en)/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:52:\"[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:57:\"(en)/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:52:\"[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:38:\"(en)/[^/]+/attachment/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:33:\"[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:21:\"(en)/([^/]+)/embed/?$\";s:54:\"index.php?lang=$matches[1]&name=$matches[2]&embed=true\";s:16:\"([^/]+)/embed/?$\";s:37:\"index.php?name=$matches[1]&embed=true\";s:25:\"(en)/([^/]+)/trackback/?$\";s:48:\"index.php?lang=$matches[1]&name=$matches[2]&tb=1\";s:20:\"([^/]+)/trackback/?$\";s:31:\"index.php?name=$matches[1]&tb=1\";s:45:\"(en)/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:60:\"index.php?lang=$matches[1]&name=$matches[2]&feed=$matches[3]\";s:40:\"([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?name=$matches[1]&feed=$matches[2]\";s:40:\"(en)/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:60:\"index.php?lang=$matches[1]&name=$matches[2]&feed=$matches[3]\";s:35:\"([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?name=$matches[1]&feed=$matches[2]\";s:33:\"(en)/([^/]+)/page/?([0-9]{1,})/?$\";s:61:\"index.php?lang=$matches[1]&name=$matches[2]&paged=$matches[3]\";s:28:\"([^/]+)/page/?([0-9]{1,})/?$\";s:44:\"index.php?name=$matches[1]&paged=$matches[2]\";s:40:\"(en)/([^/]+)/comment-page-([0-9]{1,})/?$\";s:61:\"index.php?lang=$matches[1]&name=$matches[2]&cpage=$matches[3]\";s:35:\"([^/]+)/comment-page-([0-9]{1,})/?$\";s:44:\"index.php?name=$matches[1]&cpage=$matches[2]\";s:29:\"(en)/([^/]+)(?:/([0-9]+))?/?$\";s:60:\"index.php?lang=$matches[1]&name=$matches[2]&page=$matches[3]\";s:24:\"([^/]+)(?:/([0-9]+))?/?$\";s:43:\"index.php?name=$matches[1]&page=$matches[2]\";s:21:\"(en)/[^/]+/([^/]+)/?$\";s:49:\"index.php?lang=$matches[1]&attachment=$matches[2]\";s:16:\"[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:31:\"(en)/[^/]+/([^/]+)/trackback/?$\";s:54:\"index.php?lang=$matches[1]&attachment=$matches[2]&tb=1\";s:26:\"[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:51:\"(en)/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:46:\"[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:46:\"(en)/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:66:\"index.php?lang=$matches[1]&attachment=$matches[2]&feed=$matches[3]\";s:41:\"[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:46:\"(en)/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:67:\"index.php?lang=$matches[1]&attachment=$matches[2]&cpage=$matches[3]\";s:41:\"[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:27:\"(en)/[^/]+/([^/]+)/embed/?$\";s:60:\"index.php?lang=$matches[1]&attachment=$matches[2]&embed=true\";s:22:\"[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";}','auto');
INSERT INTO `wp_options` VALUES (183,'category_children','a:0:{}','auto');
INSERT INTO `wp_options` VALUES (185,'pll_dismissed_notices','a:1:{i:0;s:6:\"wizard\";}','auto');
INSERT INTO `wp_options` VALUES (192,'acf_osm_provider_tokens','a:0:{}','off');
INSERT INTO `wp_options` VALUES (193,'acf_osm_providers','a:8:{s:13:\"OpenStreetMap\";a:1:{s:8:\"variants\";a:2:{s:2:\"CH\";s:1:\"0\";s:3:\"BZH\";s:1:\"0\";}}s:9:\"FreeMapSK\";s:1:\"0\";s:9:\"BasemapAT\";s:1:\"0\";s:6:\"nlmaps\";s:1:\"0\";s:8:\"NASAGIBS\";s:1:\"0\";s:10:\"JusticeMap\";s:1:\"0\";s:16:\"GeoportailFrance\";s:1:\"0\";s:21:\"SwissFederalGeoportal\";s:1:\"0\";}','off');
INSERT INTO `wp_options` VALUES (194,'acf_osm_proxy','a:0:{}','off');
INSERT INTO `wp_options` VALUES (195,'acf-openstreetmap-field_version','1.6.1','off');
INSERT INTO `wp_options` VALUES (271,'tipologia-luogo_children','a:0:{}','auto');
INSERT INTO `wp_options` VALUES (273,'struttura_children','a:0:{}','auto');
INSERT INTO `wp_options` VALUES (277,'tipo-pubblicazione_children','a:0:{}','auto');
INSERT INTO `wp_options` VALUES (278,'menu_check','1','auto');
INSERT INTO `wp_options` VALUES (279,'dli_has_installed','1','auto');
/*!40000 ALTER TABLE `wp_options` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `wp_postmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_postmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `post_id` (`post_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB AUTO_INCREMENT=380 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `wp_postmeta` WRITE;
/*!40000 ALTER TABLE `wp_postmeta` DISABLE KEYS */;
INSERT INTO `wp_postmeta` VALUES (1,2,'_wp_page_template','default');
INSERT INTO `wp_postmeta` VALUES (2,3,'_wp_page_template','default');
INSERT INTO `wp_postmeta` VALUES (3,5,'_wp_page_template','');
INSERT INTO `wp_postmeta` VALUES (4,6,'_wp_page_template','');
INSERT INTO `wp_postmeta` VALUES (5,7,'_wp_page_template','page-templates/privacy.php');
INSERT INTO `wp_postmeta` VALUES (6,8,'_wp_page_template','page-templates/contatti.php');
INSERT INTO `wp_postmeta` VALUES (7,9,'_wp_page_template','page-templates/contatti.php');
INSERT INTO `wp_postmeta` VALUES (8,10,'_wp_page_template','page-templates/newsletter.php');
INSERT INTO `wp_postmeta` VALUES (9,11,'_wp_page_template','page-templates/newsletter.php');
INSERT INTO `wp_postmeta` VALUES (10,12,'_wp_page_template','');
INSERT INTO `wp_postmeta` VALUES (11,13,'_wp_page_template','');
INSERT INTO `wp_postmeta` VALUES (12,14,'_wp_page_template','');
INSERT INTO `wp_postmeta` VALUES (13,15,'_wp_page_template','');
INSERT INTO `wp_postmeta` VALUES (14,16,'_wp_page_template','page-templates/mappasito.php');
INSERT INTO `wp_postmeta` VALUES (15,17,'_wp_page_template','page-templates/mappasito.php');
INSERT INTO `wp_postmeta` VALUES (16,18,'_wp_page_template','page-templates/il-laboratorio.php');
INSERT INTO `wp_postmeta` VALUES (17,19,'_wp_page_template','page-templates/il-laboratorio.php');
INSERT INTO `wp_postmeta` VALUES (18,20,'_wp_page_template','page-templates/persone.php');
INSERT INTO `wp_postmeta` VALUES (19,21,'_wp_page_template','page-templates/persone.php');
INSERT INTO `wp_postmeta` VALUES (20,22,'_wp_page_template','page-templates/progetti.php');
INSERT INTO `wp_postmeta` VALUES (21,23,'_wp_page_template','page-templates/progetti.php');
INSERT INTO `wp_postmeta` VALUES (22,24,'_wp_page_template','page-templates/archive-progetti.php');
INSERT INTO `wp_postmeta` VALUES (23,25,'_wp_page_template','page-templates/archive-progetti.php');
INSERT INTO `wp_postmeta` VALUES (24,26,'_wp_page_template','page-templates/ricerca.php');
INSERT INTO `wp_postmeta` VALUES (25,27,'_wp_page_template','page-templates/ricerca.php');
INSERT INTO `wp_postmeta` VALUES (26,28,'_wp_page_template','page-templates/pubblicazioni.php');
INSERT INTO `wp_postmeta` VALUES (27,29,'_wp_page_template','page-templates/pubblicazioni.php');
INSERT INTO `wp_postmeta` VALUES (28,30,'_wp_page_template','page-templates/brevetti.php');
INSERT INTO `wp_postmeta` VALUES (29,31,'_wp_page_template','page-templates/brevetti.php');
INSERT INTO `wp_postmeta` VALUES (30,32,'_wp_page_template','page-templates/spinoff.php');
INSERT INTO `wp_postmeta` VALUES (31,33,'_wp_page_template','page-templates/spinoff.php');
INSERT INTO `wp_postmeta` VALUES (32,34,'_wp_page_template','page-templates/notizie.php');
INSERT INTO `wp_postmeta` VALUES (33,35,'_wp_page_template','page-templates/notizie.php');
INSERT INTO `wp_postmeta` VALUES (34,36,'_wp_page_template','page-templates/eventi.php');
INSERT INTO `wp_postmeta` VALUES (35,37,'_wp_page_template','page-templates/eventi.php');
INSERT INTO `wp_postmeta` VALUES (36,38,'_wp_page_template','page-templates/luoghi.php');
INSERT INTO `wp_postmeta` VALUES (37,39,'_wp_page_template','page-templates/luoghi.php');
INSERT INTO `wp_postmeta` VALUES (38,40,'_wp_page_template','page-templates/cerca.php');
INSERT INTO `wp_postmeta` VALUES (39,41,'_wp_page_template','page-templates/cerca.php');
INSERT INTO `wp_postmeta` VALUES (40,42,'_wp_page_template','page-templates/blog.php');
INSERT INTO `wp_postmeta` VALUES (41,43,'_wp_page_template','page-templates/blog.php');
INSERT INTO `wp_postmeta` VALUES (42,44,'_wp_page_template','page-templates/risorse-tecniche.php');
INSERT INTO `wp_postmeta` VALUES (43,45,'_wp_page_template','page-templates/risorse-tecniche.php');
INSERT INTO `wp_postmeta` VALUES (44,46,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (45,46,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (46,46,'_menu_item_object_id','20');
INSERT INTO `wp_postmeta` VALUES (47,46,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (48,46,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (49,46,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (50,46,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (51,46,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (52,47,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (53,47,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (54,47,'_menu_item_object_id','22');
INSERT INTO `wp_postmeta` VALUES (55,47,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (56,47,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (57,47,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (58,47,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (59,47,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (60,48,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (61,48,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (62,48,'_menu_item_object_id','26');
INSERT INTO `wp_postmeta` VALUES (63,48,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (64,48,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (65,48,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (66,48,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (67,48,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (68,49,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (69,49,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (70,49,'_menu_item_object_id','28');
INSERT INTO `wp_postmeta` VALUES (71,49,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (72,49,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (73,49,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (74,49,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (75,49,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (76,50,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (77,50,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (78,50,'_menu_item_object_id','30');
INSERT INTO `wp_postmeta` VALUES (79,50,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (80,50,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (81,50,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (82,50,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (83,50,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (84,51,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (85,51,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (86,51,'_menu_item_object_id','32');
INSERT INTO `wp_postmeta` VALUES (87,51,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (88,51,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (89,51,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (90,51,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (91,51,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (92,52,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (93,52,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (94,52,'_menu_item_object_id','5');
INSERT INTO `wp_postmeta` VALUES (95,52,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (96,52,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (97,52,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (98,52,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (99,52,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (100,53,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (101,53,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (102,53,'_menu_item_object_id','34');
INSERT INTO `wp_postmeta` VALUES (103,53,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (104,53,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (105,53,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (106,53,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (107,53,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (108,54,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (109,54,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (110,54,'_menu_item_object_id','36');
INSERT INTO `wp_postmeta` VALUES (111,54,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (112,54,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (113,54,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (114,54,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (115,54,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (116,55,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (117,55,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (118,55,'_menu_item_object_id','8');
INSERT INTO `wp_postmeta` VALUES (119,55,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (120,55,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (121,55,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (122,55,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (123,55,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (124,56,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (125,56,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (126,56,'_menu_item_object_id','38');
INSERT INTO `wp_postmeta` VALUES (127,56,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (128,56,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (129,56,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (130,56,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (131,56,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (132,57,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (133,57,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (134,57,'_menu_item_object_id','3');
INSERT INTO `wp_postmeta` VALUES (135,57,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (136,57,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (137,57,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (138,57,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (139,57,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (140,58,'_menu_item_type','custom');
INSERT INTO `wp_postmeta` VALUES (141,58,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (142,58,'_menu_item_object_id','58');
INSERT INTO `wp_postmeta` VALUES (143,58,'_menu_item_object','custom');
INSERT INTO `wp_postmeta` VALUES (144,58,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (145,58,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (146,58,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (147,58,'_menu_item_url','http://dli-wp-e2e.local/privacy-policy#cookies');
INSERT INTO `wp_postmeta` VALUES (148,59,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (149,59,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (150,59,'_menu_item_object_id','14');
INSERT INTO `wp_postmeta` VALUES (151,59,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (152,59,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (153,59,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (154,59,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (155,59,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (156,60,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (157,60,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (158,60,'_menu_item_object_id','16');
INSERT INTO `wp_postmeta` VALUES (159,60,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (160,60,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (161,60,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (162,60,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (163,60,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (164,61,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (165,61,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (166,61,'_menu_item_object_id','40');
INSERT INTO `wp_postmeta` VALUES (167,61,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (168,61,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (169,61,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (170,61,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (171,61,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (172,62,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (173,62,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (174,62,'_menu_item_object_id','42');
INSERT INTO `wp_postmeta` VALUES (175,62,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (176,62,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (177,62,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (178,62,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (179,62,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (180,63,'_menu_item_type','custom');
INSERT INTO `wp_postmeta` VALUES (181,63,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (182,63,'_menu_item_object_id','63');
INSERT INTO `wp_postmeta` VALUES (183,63,'_menu_item_object','custom');
INSERT INTO `wp_postmeta` VALUES (184,63,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (185,63,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (186,63,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (187,63,'_menu_item_url','https://catalogo-software.developers.italia.it/software/9f2ec6c8-9eb7-4d73-8d79-577e62308e9a');
INSERT INTO `wp_postmeta` VALUES (188,64,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (189,64,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (190,64,'_menu_item_object_id','12');
INSERT INTO `wp_postmeta` VALUES (191,64,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (192,64,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (193,64,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (194,64,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (195,64,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (196,65,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (197,65,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (198,65,'_menu_item_object_id','24');
INSERT INTO `wp_postmeta` VALUES (199,65,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (200,65,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (201,65,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (202,65,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (203,65,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (204,66,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (205,66,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (206,66,'_menu_item_object_id','44');
INSERT INTO `wp_postmeta` VALUES (207,66,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (208,66,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (209,66,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (210,66,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (211,66,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (212,67,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (213,67,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (214,67,'_menu_item_object_id','21');
INSERT INTO `wp_postmeta` VALUES (215,67,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (216,67,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (217,67,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (218,67,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (219,67,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (220,68,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (221,68,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (222,68,'_menu_item_object_id','23');
INSERT INTO `wp_postmeta` VALUES (223,68,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (224,68,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (225,68,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (226,68,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (227,68,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (228,69,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (229,69,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (230,69,'_menu_item_object_id','27');
INSERT INTO `wp_postmeta` VALUES (231,69,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (232,69,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (233,69,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (234,69,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (235,69,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (236,70,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (237,70,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (238,70,'_menu_item_object_id','29');
INSERT INTO `wp_postmeta` VALUES (239,70,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (240,70,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (241,70,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (242,70,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (243,70,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (244,71,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (245,71,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (246,71,'_menu_item_object_id','31');
INSERT INTO `wp_postmeta` VALUES (247,71,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (248,71,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (249,71,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (250,71,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (251,71,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (252,72,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (253,72,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (254,72,'_menu_item_object_id','33');
INSERT INTO `wp_postmeta` VALUES (255,72,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (256,72,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (257,72,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (258,72,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (259,72,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (260,73,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (261,73,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (262,73,'_menu_item_object_id','6');
INSERT INTO `wp_postmeta` VALUES (263,73,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (264,73,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (265,73,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (266,73,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (267,73,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (268,74,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (269,74,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (270,74,'_menu_item_object_id','35');
INSERT INTO `wp_postmeta` VALUES (271,74,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (272,74,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (273,74,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (274,74,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (275,74,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (276,75,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (277,75,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (278,75,'_menu_item_object_id','37');
INSERT INTO `wp_postmeta` VALUES (279,75,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (280,75,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (281,75,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (282,75,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (283,75,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (284,76,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (285,76,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (286,76,'_menu_item_object_id','9');
INSERT INTO `wp_postmeta` VALUES (287,76,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (288,76,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (289,76,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (290,76,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (291,76,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (292,77,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (293,77,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (294,77,'_menu_item_object_id','39');
INSERT INTO `wp_postmeta` VALUES (295,77,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (296,77,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (297,77,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (298,77,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (299,77,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (300,78,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (301,78,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (302,78,'_menu_item_object_id','7');
INSERT INTO `wp_postmeta` VALUES (303,78,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (304,78,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (305,78,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (306,78,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (307,78,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (308,79,'_menu_item_type','custom');
INSERT INTO `wp_postmeta` VALUES (309,79,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (310,79,'_menu_item_object_id','79');
INSERT INTO `wp_postmeta` VALUES (311,79,'_menu_item_object','custom');
INSERT INTO `wp_postmeta` VALUES (312,79,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (313,79,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (314,79,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (315,79,'_menu_item_url','http://dli-wp-e2e.local/en/privacy-policy-en#cookies');
INSERT INTO `wp_postmeta` VALUES (316,80,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (317,80,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (318,80,'_menu_item_object_id','15');
INSERT INTO `wp_postmeta` VALUES (319,80,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (320,80,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (321,80,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (322,80,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (323,80,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (324,81,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (325,81,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (326,81,'_menu_item_object_id','17');
INSERT INTO `wp_postmeta` VALUES (327,81,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (328,81,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (329,81,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (330,81,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (331,81,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (332,82,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (333,82,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (334,82,'_menu_item_object_id','41');
INSERT INTO `wp_postmeta` VALUES (335,82,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (336,82,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (337,82,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (338,82,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (339,82,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (340,83,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (341,83,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (342,83,'_menu_item_object_id','43');
INSERT INTO `wp_postmeta` VALUES (343,83,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (344,83,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (345,83,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (346,83,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (347,83,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (348,84,'_menu_item_type','custom');
INSERT INTO `wp_postmeta` VALUES (349,84,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (350,84,'_menu_item_object_id','84');
INSERT INTO `wp_postmeta` VALUES (351,84,'_menu_item_object','custom');
INSERT INTO `wp_postmeta` VALUES (352,84,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (353,84,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (354,84,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (355,84,'_menu_item_url','https://catalogo-software.developers.italia.it/software/9f2ec6c8-9eb7-4d73-8d79-577e62308e9a');
INSERT INTO `wp_postmeta` VALUES (356,85,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (357,85,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (358,85,'_menu_item_object_id','13');
INSERT INTO `wp_postmeta` VALUES (359,85,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (360,85,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (361,85,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (362,85,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (363,85,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (364,86,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (365,86,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (366,86,'_menu_item_object_id','25');
INSERT INTO `wp_postmeta` VALUES (367,86,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (368,86,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (369,86,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (370,86,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (371,86,'_menu_item_url','');
INSERT INTO `wp_postmeta` VALUES (372,87,'_menu_item_type','post_type');
INSERT INTO `wp_postmeta` VALUES (373,87,'_menu_item_menu_item_parent','0');
INSERT INTO `wp_postmeta` VALUES (374,87,'_menu_item_object_id','45');
INSERT INTO `wp_postmeta` VALUES (375,87,'_menu_item_object','page');
INSERT INTO `wp_postmeta` VALUES (376,87,'_menu_item_target','');
INSERT INTO `wp_postmeta` VALUES (377,87,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
INSERT INTO `wp_postmeta` VALUES (378,87,'_menu_item_xfn','');
INSERT INTO `wp_postmeta` VALUES (379,87,'_menu_item_url','');
/*!40000 ALTER TABLE `wp_postmeta` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `wp_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_posts` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_author` bigint(20) unsigned NOT NULL DEFAULT 0,
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content` longtext NOT NULL,
  `post_title` text NOT NULL,
  `post_excerpt` text NOT NULL,
  `post_status` varchar(20) NOT NULL DEFAULT 'publish',
  `comment_status` varchar(20) NOT NULL DEFAULT 'open',
  `ping_status` varchar(20) NOT NULL DEFAULT 'open',
  `post_password` varchar(255) NOT NULL DEFAULT '',
  `post_name` varchar(200) NOT NULL DEFAULT '',
  `to_ping` text NOT NULL,
  `pinged` text NOT NULL,
  `post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content_filtered` longtext NOT NULL,
  `post_parent` bigint(20) unsigned NOT NULL DEFAULT 0,
  `guid` varchar(255) NOT NULL DEFAULT '',
  `menu_order` int(11) NOT NULL DEFAULT 0,
  `post_type` varchar(20) NOT NULL DEFAULT 'post',
  `post_mime_type` varchar(100) NOT NULL DEFAULT '',
  `comment_count` bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`),
  KEY `post_name` (`post_name`(191)),
  KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`ID`),
  KEY `post_parent` (`post_parent`),
  KEY `post_author` (`post_author`),
  KEY `type_status_author` (`post_type`,`post_status`,`post_author`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `wp_posts` WRITE;
/*!40000 ALTER TABLE `wp_posts` DISABLE KEYS */;
INSERT INTO `wp_posts` VALUES (1,3,'2026-10-21 09:58:58','2026-10-21 09:58:58','<!-- wp:paragraph -->\n<p>Welcome to WordPress. This is your first post. Edit or delete it, then start writing!</p>\n<!-- /wp:paragraph -->','Hello world!','','publish','open','open','','hello-world','','','2026-10-21 09:58:58','2026-10-21 09:58:58','',0,'http://dli-wp-e2e.local/?p=1',0,'post','',1);
INSERT INTO `wp_posts` VALUES (2,3,'2026-10-21 09:58:58','2026-10-21 09:58:58','<!-- wp:paragraph -->\n<p>This is an example page. It\'s different from a blog post because it will stay in one place and will show up in your site navigation (in most themes). Most people start with an About page that introduces them to potential site visitors. It might say something like this:</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\">\n<!-- wp:paragraph -->\n<p>Hi there! I\'m a bike messenger by day, aspiring actor by night, and this is my website. I live in Los Angeles, have a great dog named Jack, and I like pi&#241;a coladas. (And gettin\' caught in the rain.)</p>\n<!-- /wp:paragraph -->\n</blockquote>\n<!-- /wp:quote -->\n\n<!-- wp:paragraph -->\n<p>...or something like this:</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\">\n<!-- wp:paragraph -->\n<p>The XYZ Doohickey Company was founded in 1971, and has been providing quality doohickeys to the public ever since. Located in Gotham City, XYZ employs over 2,000 people and does all kinds of awesome things for the Gotham community.</p>\n<!-- /wp:paragraph -->\n</blockquote>\n<!-- /wp:quote -->\n\n<!-- wp:paragraph -->\n<p>As a new WordPress user, you should go to <a href=\"http://dli-wp-e2e.local/wp-admin/\">your dashboard</a> to delete this page and create new pages for your content. Have fun!</p>\n<!-- /wp:paragraph -->','Sample Page','','publish','closed','open','','sample-page','','','2026-10-21 09:58:58','2026-10-21 09:58:58','',0,'http://dli-wp-e2e.local/?page_id=2',0,'page','',0);
INSERT INTO `wp_posts` VALUES (3,3,'2026-10-21 09:58:58','2026-10-21 09:58:58','<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Who we are</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>Our website address is: http://dli-wp-e2e.local.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Comments</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>When visitors leave comments on the site we collect the data shown in the comments form, and also the visitor&#8217;s IP address and browser user agent string to help spam detection.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>An anonymized string created from your email address (also called a hash) may be provided to the Gravatar service to see if you are using it. The Gravatar service privacy policy is available here: https://automattic.com/privacy/. After approval of your comment, your profile picture is visible to the public in the context of your comment.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Media</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you upload images to the website, you should avoid uploading images with embedded location data (EXIF GPS) included. Visitors to the website can download and extract any location data from images on the website.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Cookies</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you leave a comment on our site you may opt-in to saving your name, email address and website in cookies. These are for your convenience so that you do not have to fill in your details again when you leave another comment. These cookies will last for one year.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>If you visit our login page, we will set a temporary cookie to determine if your browser accepts cookies. This cookie contains no personal data and is discarded when you close your browser.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>When you log in, we will also set up several cookies to save your login information and your screen display choices. Login cookies last for two days, and screen options cookies last for a year. If you select &quot;Remember Me&quot;, your login will persist for two weeks. If you log out of your account, the login cookies will be removed.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>If you edit or publish an article, an additional cookie will be saved in your browser. This cookie includes no personal data and simply indicates the post ID of the article you just edited. It expires after 1 day.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Embedded content from other websites</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>Articles on this site may include embedded content (e.g. videos, images, articles, etc.). Embedded content from other websites behaves in the exact same way as if the visitor has visited the other website.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>These websites may collect data about you, use cookies, embed additional third-party tracking, and monitor your interaction with that embedded content, including tracking your interaction with the embedded content if you have an account and are logged in to that website.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Who we share your data with</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you request a password reset, your IP address will be included in the reset email.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">How long we retain your data</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you leave a comment, the comment and its metadata are retained indefinitely. This is so we can recognize and approve any follow-up comments automatically instead of holding them in a moderation queue.</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>For users that register on our website (if any), we also store the personal information they provide in their user profile. All users can see, edit, or delete their personal information at any time (except they cannot change their username). Website administrators can also see and edit that information.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">What rights you have over your data</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>If you have an account on this site, or have left comments, you can request to receive an exported file of the personal data we hold about you, including any data you have provided to us. You can also request that we erase any personal data we hold about you. This does not include any data we are obliged to keep for administrative, legal, or security purposes.</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Where your data is sent</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">Suggested text: </strong>Visitor comments may be checked through an automated spam detection service.</p>\n<!-- /wp:paragraph -->\n','Privacy Policy','','draft','closed','open','','privacy-policy','','','2026-10-21 09:58:58','2026-10-21 09:58:58','',0,'http://dli-wp-e2e.local/?page_id=3',0,'page','',0);
INSERT INTO `wp_posts` VALUES (4,3,'2026-10-21 10:01:25','0000-00-00 00:00:00','','Auto Draft','','auto-draft','open','open','','','','','2026-10-21 10:01:25','0000-00-00 00:00:00','',0,'http://dli-wp-e2e.local/?p=4',0,'post','',0);
INSERT INTO `wp_posts` VALUES (5,3,'2026-10-21 10:06:27','2026-10-21 10:06:27','La nostra storia...','Presentazione','','publish','closed','closed','','presentazione','','','2026-10-21 10:06:27','2026-10-21 10:06:27','',0,'http://dli-wp-e2e.local/presentazione/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (6,3,'2026-10-21 10:06:27','2026-10-21 10:06:27','Our history...','Presentation','','publish','closed','closed','','presentation','','','2026-10-21 10:06:27','2026-10-21 10:06:27','',0,'http://dli-wp-e2e.local/presentation/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (7,3,'2026-10-21 10:06:27','2026-10-21 10:06:27','<span style=\"font-weight: 400;\">This information notice is provided, pursuant to EU Regulation 2016/679 of the European Parliament and Council of April 27, 2016, exclusively for the institutional websites of the Scuola Normale Superiore.</span> \n<h5><strong>Data Controller</strong></h5>\n<span style=\"font-weight: 400;\">The <strong>Scuola Normale Superiore</strong>, represented by its acting Director, located in Pisa, P.za dei Cavalieri 7, phone +39.050.509111, certified email: </span><span style=\"font-weight: 400;\">protocollo@pec.sns.it</span><span style=\"font-weight: 400;\">, is the data controller for personal data voluntarily provided or automatically collected from the institutional websites.</span>\n<h5><strong>Data Protection Officer</strong></h5>\n<span style=\"font-weight: 400;\">Contact details of the Data Protection Officer</span><span style=\"font-weight: 400;\">\n</span><span style=\"font-weight: 400;\">Marco Marchetti: </span><a href=\"mailto:dpo@sns.it\"><span style=\"font-weight: 400;\">dpo@sns.it</span><span style=\"font-weight: 400;\">\n</span></a><span style=\"font-weight: 400;\">phone: 050500082</span>\n<h5><strong>Purpose and Legal Basis of Data Processing</strong></h5>\n<span style=\"font-weight: 400;\">The personal data described in this notice are processed by the Controller in the performance of its public interest tasks or otherwise connected to the exercise of its public powers, including the task of promoting awareness of the initiatives of the Department for Digital Transformation (Art. 6, Para. 1, Letter e of the GDPR).</span>\n<h5><strong>Types of Data Processed, Processing Purposes, and Retention Period</strong></h5>\n<span style=\"font-weight: 400;\">In compliance with the principles of lawfulness, fairness, transparency, adequacy, relevance, and necessity under Art. 5, Para. 1, of EU Reg. 679/2016, the Data Controller will process personal data automatically collected while browsing the Scuola\'s websites or voluntarily provided for the following purposes:</span>\n<ul>\n    <li><span style=\"font-weight: 400;\">Browsing data</span><b>,</b><span style=\"font-weight: 400;\"> such as IP addresses, domain names of users’ computers connecting to the site, URI (Uniform Resource Identifier) addresses of requested resources, request time, method used to submit the request to the server, file size received in response, server response status code (success, error), and other parameters related to the user\'s operating system and IT environment, are collected solely to obtain anonymous statistical information on website use and ensure its proper functioning. Although not collected for the purpose of identifying users, these data could allow user identification through processing and association with data held by third parties. Finally, these data may be used to ascertain liability in the event of potential cybercrimes against the websites.</span></li>\n    <li><span style=\"font-weight: 400;\">Data voluntarily provided by users</span><b>,</b><span style=\"font-weight: 400;\"> including information, personal, and identifying data collected to enable user access to specific requested services, such as non-institutional email addresses, personal phone numbers, and any special or judicial data voluntarily provided by users during online service usage or by sending requests to published email addresses, are processed solely for institutional purposes and in the interest of the requesting user.</span></li>\n</ul>\n<span style=\"font-weight: 400;\">The processing activities and retention periods for the data vary depending on each online service and are detailed in specific notices available on subscription pages.</span>\n<h5><strong>Categories of Data Recipients and Possible Data Transfers</strong></h5>\n<span style=\"font-weight: 400;\">The data processed for the above purposes will be communicated or made accessible to employees and collaborators assigned to the relevant offices of the Data Controller, who, as data protection contacts and/or system administrators and/or processing delegates, will be adequately trained by the Data Controller.</span>\n\n<span style=\"font-weight: 400;\">The Scuola Normale Superiore may also communicate personal data to other public administrations if they need to process the data for procedures within their institutional competence, as well as to any public entities to whom communication is mandatory under EU regulations, laws, or regulations.</span>\n\n<span style=\"font-weight: 400;\">The management and storage of personal data collected by the Scuola Normale Superiore occur on servers located within its premises.</span>\n<h5><strong>Data Retention Period</strong></h5>\n<span style=\"font-weight: 400;\">Browsing data automatically collected are retained only for the time necessary (24 hours) to derive anonymous statistical information on website usage and ensure its proper functioning, after which they are immediately deleted. </span><span style=\"font-weight: 400;\">\n</span><span style=\"font-weight: 400;\">For the retention periods of other data voluntarily provided by users, please refer to the specific notices prepared by the relevant Offices.</span>\n\n<span style=\"font-weight: 400;\">Rights of the Data Subject</span>\n\n<span style=\"font-weight: 400;\">The data subject has the right to request from the Data Controller, pursuant to Articles 15, 16, 17, 18, 19, and 21 of EU Reg. 679/2016: access to their personal data and all related information; correction of inaccurate personal data and completion of incomplete data; deletion of personal data, except for those contained in records that must be retained by the Scuola Normale Superiore, and unless there is an overriding legitimate reason to process them; restriction of processing in cases specified under Art. 18 of the Regulation.</span><span style=\"font-weight: 400;\">\n</span><span style=\"font-weight: 400;\">The data subject also has the right to object to the processing of their personal data, subject to the necessity and obligation of processing for the establishment of a relationship; to withdraw consent, if provided, for non-mandatory data processing, without affecting the lawfulness of processing based on consent before its withdrawal.</span><span style=\"font-weight: 400;\">\n</span><span style=\"font-weight: 400;\">The data subject may exercise all the above rights by sending an email to the Data Controller at </span><span style=\"font-weight: 400;\">privacy@sns.it</span><span style=\"font-weight: 400;\">\n</span><span style=\"font-weight: 400;\">The data subject also has the right to lodge a complaint with the Data Protection Authority pursuant to Art. 77 of EU Reg. 679/2016.</span>\n\n<span style=\"font-weight: 400;\">For any clarification, the Data Protection Officer can be contacted at: </span><span style=\"font-weight: 400;\">dpo@sns.it</span><span style=\"font-weight: 400;\"> </span>\n<h5><strong>Mandatory or Optional Data Provision</strong></h5>\n<span style=\"font-weight: 400;\">Except as specified for browsing data, users are free to provide their personal data or not. Providing such data may be necessary to use certain online services.</span>\n<h5 id=\"cookies\"><strong>Cookies</strong></h5>\n<span style=\"font-weight: 400;\">Cookies are text files stored on users\' computers to enable secure and efficient browsing of the site and monitor its usage.</span>\n\n<span style=\"font-weight: 400;\">No personal data is intentionally acquired by the site. </span>\n\n<span style=\"font-weight: 400;\">This site uses technical cookies necessary for its operation and to enhance the user experience.</span>\n\n<span style=\"font-weight: 400;\">This site uses profiling cookies managed by the centralized platform </span><a href=\"https://webanalytics.italia.it/\"><span style=\"font-weight: 400;\">Web Analytics Italia (WAI)</span></a><span style=\"font-weight: 400;\"> by AgID. WAI stores statistical data of Italian public administration websites in an anonymous form (through IP address masking), aggregated statistical information on user navigation (e.g., number of pages visited and accesses, time spent on the site). </span>\n\n<span style=\"font-weight: 400;\">These cookies are assimilated to technical cookies and do not require your consent. Furthermore, these analytical cookies, once stored on your browser, may remain for up to 13 months, subject to the possibility of disabling them at any time via your browser settings as described in the following section.</span>\n\n<span style=\"font-weight: 400;\">To access video content hosted directly on the site, before viewing, you will be asked to approve third-party profiling cookies from YouTube (</span><a href=\"https://policies.google.com/privacy?hl=en\"><span style=\"font-weight: 400;\">https://policies.google.com/privacy?hl=en</span></a><span style=\"font-weight: 400;\">) which also activate other technical identifiers used by YouTube to enable video streaming from the site. These cookies will only be stored in your browser after obtaining your consent, which can be limited to a single video or extended to all videos on the site by selecting “</span><b>Remember for all videos</b><span style=\"font-weight: 400;\">.” You can always change your choice using the “</span><b>Revoke</b><span style=\"font-weight: 400;\">” button in the “</span><b>Cookie Management</b><span style=\"font-weight: 400;\">” section at the bottom of this notice. The cookie management functionality is also accessible from the site footer.</span>\n\n<span style=\"font-weight: 400;\">How to disable cookies (opt-out) on your device</span>\n\n<span style=\"font-weight: 400;\">Most browsers automatically accept cookies, but they can be declined. If you do not wish to receive or store cookies, you can modify your browser\'s security settings according to the instructions provided by the respective providers at the links below. Disabling all cookies may prevent the site from functioning properly.</span>\n<ul>\n    <li style=\"font-weight: 400;\" aria-level=\"1\"><a href=\"https://support.google.com/chrome/answer/95647\"><span style=\"font-weight: 400;\">Chrome</span></a></li>\n    <li style=\"font-weight: 400;\" aria-level=\"1\"><a href=\"https://support.mozilla.org/en/kb/enhanced-tracking-protection-firefox-desktop\"><span style=\"font-weight: 400;\">Firefox</span></a></li>\n    <li style=\"font-weight: 400;\" aria-level=\"1\"><a href=\"https://support.apple.com/en-us/guide/safari/sfri11471/mac\"><span style=\"font-weight: 400;\">Safari</span></a></li>\n    <li style=\"font-weight: 400;\" aria-level=\"1\"><a href=\"https://support.microsoft.com/en-us/microsoft-edge/delete-cookies-in-microsoft-edge-63947406-40ac-c3b8-57b9-2a946a29ae09\"><span style=\"font-weight: 400;\">Edge</span></a></li>\n    <li style=\"font-weight: 400;\" aria-level=\"1\"><a href=\"https://help.opera.com/en/latest/web-preferences/#cookies\"><span style=\"font-weight: 400;\">Opera</span></a></li>\n</ul>\n&nbsp;\n','Privacy policy','','publish','closed','closed','','privacy-policy-en','','','2026-10-21 10:06:27','2026-10-21 10:06:27','',0,'http://dli-wp-e2e.local/privacy-policy-en/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (8,3,'2026-10-21 10:06:27','2026-10-21 10:06:27','I nostri contatti... [template attivo]','Contatti','','publish','closed','closed','','contatti','','','2026-10-21 10:06:27','2026-10-21 10:06:27','',0,'http://dli-wp-e2e.local/contatti/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (9,3,'2026-10-21 10:06:27','2026-10-21 10:06:27','Our contacts... [template active]','Contacts','','publish','closed','closed','','contacts','','','2026-10-21 10:06:27','2026-10-21 10:06:27','',0,'http://dli-wp-e2e.local/contacts/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (10,3,'2026-10-21 10:06:27','2026-10-21 10:06:27','Registrati alla newsletter... [template attivo]','Newsletter','','publish','closed','closed','','newsletter','','','2026-10-21 10:06:27','2026-10-21 10:06:27','',0,'http://dli-wp-e2e.local/newsletter/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (11,3,'2026-10-21 10:06:27','2026-10-21 10:06:27','Register to the newsletter... [template active]','Newsletter','','publish','closed','closed','','newsletter-en','','','2026-10-21 10:06:27','2026-10-21 10:06:27','',0,'http://dli-wp-e2e.local/newsletter-en/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (12,3,'2026-10-21 10:06:27','2026-10-21 10:06:27','<p>Un sito web è accessibile quando, indipendentemente dalle disabilità psicofisiche, dalle dotazione tecniche di cui dispone l\'utente, permette di accedere con facilità ai contenuti e ai servizi che offre.</p>\n<p>Questo sito è stato realizzato nel rispetto delle <a href=\"https://www.agid.gov.it/it/design-servizi/accessibilita/normativa\">principali normative vigenti in tema di accessibilità dei siti web delle PA</a> e con estrema attenzione alla fruibilità da dispositivi di tipo diverso, con l\'approccio di design <em>mobile-first.</em></p>\n<p>Un sito è accessibile quando permette di accedere facilmente ai contenuti e ai servizi che offre, indipendentemente dalle disabilità psicofisiche, dalle dotazioni tecniche di cui dispone l\'utente e dal contesto in cui opera. Questo sito web è stato realizzato secondo le disposizioni normative vigenti in tema di accessibilità dei siti web e con la tecnica del Responsive Web Design per la consultazione anche da dispositivi mobili.</p>\n<p><strong><a href=\"https://form.agid.gov.it/view/8459f750-988b-11f0-a8e8-5d8f0c93c5ba\">Dichiarazione di accessibilità</a></strong></p>\n<p><strong>Segnalazioni sull\'accessibilità</strong></p>\n<p>Nonostante le verifiche, non è sempre possibile garantire la completa accessibilità del sito. Vi preghiamo di segnalarci difficoltà riscontrate nell\'accesso alle informazioni e ai servizi.</p>\n','Dichiarazione','','publish','closed','closed','','accessibilita','','','2026-10-21 10:06:27','2026-10-21 10:06:27','',0,'http://dli-wp-e2e.local/accessibilita/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (13,3,'2026-10-21 10:06:27','2026-10-21 10:06:27','<p>A website is accessible when, regardless of psychophysical disabilities or the technical equipment available to the user, it allows easy access to its content and services.</p>  \n<p>This website has been created in compliance with the <a href=\"https://www.agid.gov.it/it/design-servizi/accessibilita/normativa\">main regulations in force regarding the accessibility of public administration websites</a> and with great attention to usability across different types of devices, following a <em>mobile-first</em> design approach.</p>  \n<p>A website is accessible when it allows easy access to its content and services, regardless of psychophysical disabilities, the technical equipment available to the user, and the context in which they operate. This web site has been developed following the current regulations on website accessibility and using Responsive Web Design techniques to ensure usability from mobile devices as well.</p>  \n<p><strong><a href=\"https://form.agid.gov.it/view/8459f750-988b-11f0-a8e8-5d8f0c93c5ba\">Accessibility Statement</a></strong></p>  \n<p><strong>Accessibility Reports</strong></p>  \n<p>Despite thorough checks, it is not always possible to guarantee the complete accessibility of the website. Please report any difficulties encountered in accessing information and services.</p>\n','Accessibility','','publish','closed','closed','','accessibility','','','2026-10-21 10:06:27','2026-10-21 10:06:27','',0,'http://dli-wp-e2e.local/accessibility/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (14,3,'2026-10-21 10:06:27','2026-10-21 10:06:27','<h5><b>Licenza dei contenuti</b></h5>\n<span style=\"font-weight: 400;\">In applicazione del principio open by default ai sensi dell’articolo 52 del decreto legislativo 7 marzo 2005, n. 82 (CAD) e salvo dove diversamente specificato (compresi i contenuti incorporati di terzi), i dati, i documenti e le informazioni pubblicati sul sito sono rilasciati con </span><a href=\"https://creativecommons.org/licenses/by/4.0/legalcode.it\"><span style=\"font-weight: 400;\">licenza CC-BY 4.0</span></a><span style=\"font-weight: 400;\">.</span>\n\n<span style=\"font-weight: 400;\">Gli utenti sono quindi liberi di condividere (riprodurre, distribuire, comunicare al pubblico, esporre in pubblico, rappresentare, eseguire e recitare questo materiale con qualsiasi mezzo e formato) e modificare (trasformare il materiale e utilizzarlo per opere derivate) per qualsiasi fine, anche commerciale con il solo onere di attribuzione, senza apporre restrizioni aggiuntive.</span>\n<h5><b>Collegamenti a siti esterni e contenuti incorporati</b></h5>\n<span style=\"font-weight: 400;\">I collegamenti a siti esterni di terzi, indicati nel presente sito, nonché i contenuti incorporati di terzi sono forniti come semplice servizio agli utenti. L’indicazione dei collegamenti a siti esterni di terzi o l’inserimento di contenuti incorporati di terzi non implica da parte della </span><b>Scuola Normale</b><span style=\"font-weight: 400;\"> alcun tipo di approvazione o condivisione di responsabilità in relazione alla legittimità, alla completezza e alla correttezza delle informazioni contenute nei siti indicati.</span>\n<h5><b>Social media policy</b></h5>\n<span style=\"font-weight: 400;\">La </span><b>Scuola Normale Superiore</b><span style=\"font-weight: 400;\"> utilizza i social media per finalità istituzionali, per accrescere la trasparenza e la conoscenza delle proprie attività: informa, comunica, ascolta e pubblica contenuti di carattere istituzionale.</span>\n\n<span style=\"font-weight: 400;\">I contenuti pubblicati comprendono immagini, video, comunicati stampa, informazioni di servizio, notizie relative all’attività della </span><b>Scuola Normale</b><span style=\"font-weight: 400;\"> nonché opportunità di coinvolgimento e partecipazione dei cittadini.</span>\n\n<span style=\"font-weight: 400;\">Attraverso i propri canali social, la </span><b>Scuola Normale</b><span style=\"font-weight: 400;\"> può condividere e rilanciare contenuti e messaggi di pubblico interesse e utilità pubblicate da altri utenti. Laddove pubblicati direttamente dalla </span><b>Scuola Normale</b><span style=\"font-weight: 400;\">, tutti i contenuti sono rilasciati secondo il principio di open by default con </span><a href=\"https://creativecommons.org/licenses/by/4.0/legalcode.it\"><span style=\"font-weight: 400;\">licenza CC-BY 4.0</span></a><span style=\"font-weight: 400;\">.</span>\n\n<span style=\"font-weight: 400;\">Trattiamo i tuoi dati personali come previsto nella privacy policy di questo sito.</span>\n\n<span style=\"font-weight: 400;\">La </span><b>Scuola Normale</b><span style=\"font-weight: 400;\"> si impegna a gestire spazi di comunicazione e dialogo all’interno dei propri profili, costantemente monitorati, nei diversi social media, chiedendo ai propri utenti il rispetto di alcune semplici regole:</span>\n<ul>\n 	<li style=\"font-weight: 400;\" aria-level=\"1\"><span style=\"font-weight: 400;\">A tutti si chiede di esporre la propria opinione con correttezza, misura e rispetto.</span></li>\n 	<li style=\"font-weight: 400;\" aria-level=\"1\"><span style=\"font-weight: 400;\">Nei social media ognuno è responsabile dei contenuti che pubblica e delle opinioni che esprime. Non saranno comunque tollerati insulti, volgarità, offese, minacce e, in generale, atteggiamenti discriminatori, violenti o diffamatori.</span></li>\n 	<li style=\"font-weight: 400;\" aria-level=\"1\"><span style=\"font-weight: 400;\">I contenuti pubblicati devono rispettare sempre la privacy delle persone. Vanno evitati riferimenti a fatti o a dettagli privi di rilevanza pubblica e che ledano la sfera personale di terzi.</span></li>\n 	<li style=\"font-weight: 400;\" aria-level=\"1\"><span style=\"font-weight: 400;\">L’interesse degli argomenti rispetto alle attività della </span><b>Scuola Normale</b><span style=\"font-weight: 400;\"> è un requisito essenziale: non è possibile in alcun modo utilizzare questi spazi per scopi diversi da quelli istituzionali.</span></li>\n 	<li style=\"font-weight: 400;\" aria-level=\"1\"><span style=\"font-weight: 400;\">Ogni discussione è legata a un tema specifico: a tutti i partecipanti è richiesto di rispettarlo.</span></li>\n 	<li style=\"font-weight: 400;\" aria-level=\"1\"><span style=\"font-weight: 400;\">Non sarà tollerata alcuna forma di pubblicità, spam o promozione di interessi privati o di attività illegali.</span></li>\n 	<li style=\"font-weight: 400;\" aria-level=\"1\"><span style=\"font-weight: 400;\">Non sono ammessi contenuti che violino il diritto d’autore né l’utilizzo non autorizzato di marchi registrati.</span></li>\n</ul>\n<span style=\"font-weight: 400;\">In ogni caso, ove il social media lo permetta, saranno rimossi dallo staff tutti i post, i commenti o i materiali audio/video che:</span>\n<ul>\n 	<li style=\"font-weight: 400;\" aria-level=\"1\"><span style=\"font-weight: 400;\">presentano un linguaggio inappropriato e/o un tono minaccioso, violento, volgare o irrispettoso;</span></li>\n 	<li style=\"font-weight: 400;\" aria-level=\"1\"><span style=\"font-weight: 400;\">presentano contenuti illeciti o di incitamento a compiere attività illecite; hanno contenuti offensivi, ingannevoli, allarmistici, o in violazione di diritti di terzi;</span></li>\n 	<li style=\"font-weight: 400;\" aria-level=\"1\"><span style=\"font-weight: 400;\">divulgano dati e informazioni personali o che possono cagionare danni o ledere la reputazione di terzi;</span></li>\n 	<li style=\"font-weight: 400;\" aria-level=\"1\"><span style=\"font-weight: 400;\">presentano contenuti a contenuto osceno, pornografico o pedopornografico, o tale da offendere la sensibilità degli utenti;</span></li>\n 	<li style=\"font-weight: 400;\" aria-level=\"1\"><span style=\"font-weight: 400;\">hanno un contenuto discriminatorio per genere, razza, etnia, lingua, credo religioso, opinioni politiche, orientamento sessuale, età, condizioni personali e sociali;</span></li>\n 	<li style=\"font-weight: 400;\" aria-level=\"1\"><span style=\"font-weight: 400;\">promuovono o sostengono attività illegali, che violano il copyright o diritti di terzi.</span></li>\n</ul>\n<span style=\"font-weight: 400;\">Per chi dovesse violare ripetutamente queste condizioni, quelle contenute nelle policy degli strumenti adottati o comunque mette a rischio la sicurezza dei sistemi, la </span><b>Scuola Normale</b><span style=\"font-weight: 400;\"> si riserva il diritto di usare il ban o il blocco (quando possibile dopo un primo avvertimento) per impedire ulteriori interventi, e di segnalare l’utente ai responsabili della piattaforma ed eventualmente alle forze dell’ordine.</span>','Note legali','','publish','closed','closed','','note-legali','','','2026-10-21 10:06:27','2026-10-21 10:06:27','',0,'http://dli-wp-e2e.local/note-legali/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (15,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','<h5><b>Content License</b></h5>\n<span style=\"font-weight: 400;\">In accordance with the open by default principle as per Article 52 of Legislative Decree 7 March 2005, No. 82 (CAD), and unless otherwise specified (including third-party embedded content), the data, documents, and information published on the site are released under the </span><a href=\"https://creativecommons.org/licenses/by/4.0/legalcode.it\"><span style=\"font-weight: 400;\">CC-BY 4.0 license</span></a><span style=\"font-weight: 400;\">.</span>\n\n<span style=\"font-weight: 400;\">Users are therefore free to share (reproduce, distribute, communicate to the public, display publicly, perform, and recite this material by any means and format) and modify (transform the material and use it for derivative works) for any purpose, including commercial purposes, with the only obligation of attribution, without imposing additional restrictions.</span>\n<h5><b>Links to External Websites and Embedded Content</b></h5>\n<span style=\"font-weight: 400;\">Links to third-party external websites provided on this site, as well as third-party embedded content, are offered as a simple service to users. The inclusion of links to third-party external websites or embedded content from third parties does not imply any approval or assumption of responsibility by </span><b>Scuola Normale</b><span style=\"font-weight: 400;\"> regarding the legality, completeness, and accuracy of the information contained in the linked sites.</span>\n<h5><b>Social Media Policy</b></h5>\n<span style=\"font-weight: 400;\">The </span><b>Scuola Normale Superiore</b><span style=\"font-weight: 400;\"> uses social media for institutional purposes, to increase transparency and awareness of its activities: it informs, communicates, listens, and publishes institutional content.</span>\n\n<span style=\"font-weight: 400;\">The published content includes images, videos, press releases, service information, news related to the activities of </span><b>Scuola Normale</b><span style=\"font-weight: 400;\">, as well as opportunities for citizen engagement and participation.</span>\n\n<span style=\"font-weight: 400;\">Through its social media channels, </span><b>Scuola Normale</b><span style=\"font-weight: 400;\"> may share and amplify content and messages of public interest and utility published by other users. Where directly published by </span><b>Scuola Normale</b><span style=\"font-weight: 400;\">, all content is released according to the open by default principle under the </span><a href=\"https://creativecommons.org/licenses/by/4.0/legalcode.it\"><span style=\"font-weight: 400;\">CC-BY 4.0 license</span></a><span style=\"font-weight: 400;\">.</span>\n\n<span style=\"font-weight: 400;\">We process your personal data as outlined in the privacy policy of this site.</span>\n\n<span style=\"font-weight: 400;\">The </span><b>Scuola Normale</b><span style=\"font-weight: 400;\"> is committed to managing communication and dialogue spaces within its profiles, constantly monitored, on various social media platforms, asking its users to respect some simple rules:</span>\n<ul>\n 	<li style=\"font-weight: 400;\" aria-level=\"1\"><span style=\"font-weight: 400;\">Everyone is asked to express their opinion with fairness, moderation, and respect.</span></li>\n 	<li style=\"font-weight: 400;\" aria-level=\"1\"><span style=\"font-weight: 400;\">On social media, each individual is responsible for the content they post and the opinions they express. Insults, vulgarities, offenses, threats, and, in general, discriminatory, violent, or defamatory behaviors will not be tolerated.</span></li>\n 	<li style=\"font-weight: 400;\" aria-level=\"1\"><span style=\"font-weight: 400;\">Published content must always respect the privacy of individuals. References to facts or details that are irrelevant to the public interest and infringe on the personal sphere of third parties should be avoided.</span></li>\n 	<li style=\"font-weight: 400;\" aria-level=\"1\"><span style=\"font-weight: 400;\">The relevance of topics to the activities of </span><b>Scuola Normale</b><span style=\"font-weight: 400;\"> is an essential requirement: these spaces cannot be used for purposes other than institutional ones.</span></li>\n 	<li style=\"font-weight: 400;\" aria-level=\"1\"><span style=\"font-weight: 400;\">Every discussion is tied to a specific topic: all participants are required to respect it.</span></li>\n 	<li style=\"font-weight: 400;\" aria-level=\"1\"><span style=\"font-weight: 400;\">No form of advertising, spam, or promotion of private interests or illegal activities will be tolerated.</span></li>\n 	<li style=\"font-weight: 400;\" aria-level=\"1\"><span style=\"font-weight: 400;\">Content that infringes copyright or the unauthorized use of registered trademarks is not allowed.</span></li>\n</ul>\n<span style=\"font-weight: 400;\">In any case, where the social media platform allows, the staff will remove all posts, comments, or audio/video materials that:</span>\n<ul>\n 	<li style=\"font-weight: 400;\" aria-level=\"1\"><span style=\"font-weight: 400;\">present inappropriate language and/or a threatening, violent, vulgar, or disrespectful tone;</span></li>\n 	<li style=\"font-weight: 400;\" aria-level=\"1\"><span style=\"font-weight: 400;\">present illegal content or incite illegal activities; contain offensive, misleading, alarmist content, or violate the rights of t\n</span></li>\n</ul>','Legal notes','','publish','closed','closed','','legal-notes','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/legal-notes/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (16,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','La mappa del sito... [template attivo]','Mappa del sito','','publish','closed','closed','','mappa-sito','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/mappa-sito/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (17,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','The map of the site... [template active]','Site map','','publish','closed','closed','','site-map','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/site-map/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (18,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','Descrizione del sito... [template attivo]','Il Laboratorio','','publish','closed','closed','','il-laboratorio','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/il-laboratorio/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (19,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','Site description... [template active]','The Lab','','publish','closed','closed','','the-lab','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/the-lab/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (20,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','Descrizione dello staff ... [template attivo]','Persone','','publish','closed','closed','','persone','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/persone/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (21,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','Description of the staff... [template active]','People','','publish','closed','closed','','people','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/people/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (22,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','Descrizione dei progetti... [template attivo]','Progetti','','publish','closed','closed','','progetti','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/progetti/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (23,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','Description of the projects... [template active]','Projects','','publish','closed','closed','','projects','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/projects/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (24,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','Archivio dei progetti...','Archivio progetti','','publish','closed','closed','','archivio-progetti','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/archivio-progetti/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (25,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','Projects archive of the projects... [template active]','Projects archive','','publish','closed','closed','','projects-archive','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/projects-archive/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (26,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','Descrizione delle attività di ricerca... [template attivo]','Attività di ricerca','','publish','closed','closed','','ricerca','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/ricerca/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (27,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','Description of the research activities... [template active]','Research activities','','publish','closed','closed','','research','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/research/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (28,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','Le nostre pubblicazioni... [template attivo]','Pubblicazioni','','publish','closed','closed','','pubblicazioni','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/pubblicazioni/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (29,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','Our publications... [template active]','Publications','','publish','closed','closed','','publications','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/publications/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (30,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','I nostri brevetti... [template attivo]','Brevetti','','publish','closed','closed','','brevetti','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/brevetti/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (31,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','Our patents... [template active]','Patents','','publish','closed','closed','','patents','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/patents/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (32,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','Le nostre spin-off ... [template attivo]','Spin-off','','publish','closed','closed','','spinoff','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/spinoff/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (33,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','Our spin-off... [template active]','Spin-off','','publish','closed','closed','','spinoff-en','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/spinoff-en/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (34,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','Le notizie... [template attivo]','Le notizie','','publish','closed','closed','','notizie','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/notizie/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (35,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','Publications... [template active]','News','','publish','closed','closed','','news','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/news/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (36,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','Gli eventi... [template attivo]','Gli eventi','','publish','closed','closed','','eventi','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/eventi/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (37,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','The events... [template active]','Events','','publish','closed','closed','','events','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/events/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (38,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','I luoghi... [template attivo]','I luoghi','','publish','closed','closed','','luoghi','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/luoghi/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (39,3,'2026-10-21 10:06:28','2026-10-21 10:06:28','The places... [template active]','Places','','publish','closed','closed','','places','','','2026-10-21 10:06:28','2026-10-21 10:06:28','',0,'http://dli-wp-e2e.local/places/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (40,3,'2026-10-21 10:06:29','2026-10-21 10:06:29','Ricerca cose nel sito ... [template attivo]','Ricerca','','publish','closed','closed','','ricerca-sito','','','2026-10-21 10:06:29','2026-10-21 10:06:29','',0,'http://dli-wp-e2e.local/ricerca-sito/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (41,3,'2026-10-21 10:06:29','2026-10-21 10:06:29','Search things in the site... [template active]','Search','','publish','closed','closed','','search-site','','','2026-10-21 10:06:29','2026-10-21 10:06:29','',0,'http://dli-wp-e2e.local/search-site/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (42,3,'2026-10-21 10:06:29','2026-10-21 10:06:29','Il blog ... [template attivo]','Blog','','publish','closed','closed','','il-blog','','','2026-10-21 10:06:29','2026-10-21 10:06:29','',0,'http://dli-wp-e2e.local/il-blog/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (43,3,'2026-10-21 10:06:29','2026-10-21 10:06:29','The blog... [template active]','Blog','','publish','closed','closed','','the-blog','','','2026-10-21 10:06:29','2026-10-21 10:06:29','',0,'http://dli-wp-e2e.local/the-blog/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (44,3,'2026-10-21 10:06:29','2026-10-21 10:06:29','Le nostre risorse tecniche ... [template attivo]','Risorse tecniche','','publish','closed','closed','','risorse-tecniche','','','2026-10-21 10:06:29','2026-10-21 10:06:29','',0,'http://dli-wp-e2e.local/risorse-tecniche/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (45,3,'2026-10-21 10:06:29','2026-10-21 10:06:29','Our technical resources... [template active]','Technical resources','','publish','closed','closed','','technical-resources','','','2026-10-21 10:06:29','2026-10-21 10:06:29','',0,'http://dli-wp-e2e.local/technical-resources/',0,'page','',0);
INSERT INTO `wp_posts` VALUES (46,3,'2026-10-21 10:06:30','2026-10-21 10:06:30',' ','','','publish','closed','closed','','46','','','2026-10-21 10:06:30','2026-10-21 10:06:30','',0,'http://dli-wp-e2e.local/46/',0,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (47,3,'2026-10-21 10:06:30','2026-10-21 10:06:30',' ','','','publish','closed','closed','','47','','','2026-10-21 10:06:30','2026-10-21 10:06:30','',0,'http://dli-wp-e2e.local/47/',2,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (48,3,'2026-10-21 10:06:30','2026-10-21 10:06:30',' ','','','publish','closed','closed','','48','','','2026-10-21 10:06:30','2026-10-21 10:06:30','',0,'http://dli-wp-e2e.local/48/',3,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (49,3,'2026-10-21 10:06:30','2026-10-21 10:06:30',' ','','','publish','closed','closed','','49','','','2026-10-21 10:06:30','2026-10-21 10:06:30','',0,'http://dli-wp-e2e.local/49/',4,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (50,3,'2026-10-21 10:06:30','2026-10-21 10:06:30',' ','','','publish','closed','closed','','50','','','2026-10-21 10:06:30','2026-10-21 10:06:30','',0,'http://dli-wp-e2e.local/50/',5,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (51,3,'2026-10-21 10:06:30','2026-10-21 10:06:30',' ','','','publish','closed','closed','','51','','','2026-10-21 10:06:30','2026-10-21 10:06:30','',0,'http://dli-wp-e2e.local/51/',6,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (52,3,'2026-10-21 10:06:30','2026-10-21 10:06:30',' ','','','publish','closed','closed','','52','','','2026-10-21 10:06:30','2026-10-21 10:06:30','',0,'http://dli-wp-e2e.local/52/',0,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (53,3,'2026-10-21 10:06:30','2026-10-21 10:06:30','','Notizie','','publish','closed','closed','','notizie','','','2026-10-21 10:06:30','2026-10-21 10:06:30','',0,'http://dli-wp-e2e.local/notizie/',0,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (54,3,'2026-10-21 10:06:30','2026-10-21 10:06:30','','Eventi','','publish','closed','closed','','eventi','','','2026-10-21 10:06:30','2026-10-21 10:06:30','',0,'http://dli-wp-e2e.local/eventi/',2,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (55,3,'2026-10-21 10:06:30','2026-10-21 10:06:30',' ','','','publish','closed','closed','','55','','','2026-10-21 10:06:30','2026-10-21 10:06:30','',0,'http://dli-wp-e2e.local/55/',3,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (56,3,'2026-10-21 10:06:30','2026-10-21 10:06:30','','Dove siamo','','publish','closed','closed','','dove-siamo','','','2026-10-21 10:06:30','2026-10-21 10:06:30','',0,'http://dli-wp-e2e.local/dove-siamo/',4,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (57,3,'2026-10-21 10:06:30','2026-10-21 10:06:30','','Privacy policy','','publish','closed','closed','','privacy-policy','','','2026-10-21 10:06:30','2026-10-21 10:06:30','',0,'http://dli-wp-e2e.local/privacy-policy/',0,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (58,3,'2026-10-21 10:06:30','2026-10-21 10:06:30','','Cookies policy','','publish','closed','closed','','cookies-policy','','','2026-10-21 10:06:30','2026-10-21 10:06:30','',0,'http://dli-wp-e2e.local/cookies-policy/',2,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (59,3,'2026-10-21 10:06:30','2026-10-21 10:06:30',' ','','','publish','closed','closed','','59','','','2026-10-21 10:06:30','2026-10-21 10:06:30','',0,'http://dli-wp-e2e.local/59/',3,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (60,3,'2026-10-21 10:06:30','2026-10-21 10:06:30',' ','','','publish','closed','closed','','60','','','2026-10-21 10:06:30','2026-10-21 10:06:30','',0,'http://dli-wp-e2e.local/60/',4,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (61,3,'2026-10-21 10:06:30','2026-10-21 10:06:30','','Cerca','','publish','closed','closed','','cerca','','','2026-10-21 10:06:30','2026-10-21 10:06:30','',0,'http://dli-wp-e2e.local/cerca/',5,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (62,3,'2026-10-21 10:06:30','2026-10-21 10:06:30',' ','','','publish','closed','closed','','62','','','2026-10-21 10:06:30','2026-10-21 10:06:30','',0,'http://dli-wp-e2e.local/62/',6,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (63,3,'2026-10-21 10:06:30','2026-10-21 10:06:30','','Riuso','','publish','closed','closed','','riuso','','','2026-10-21 10:06:30','2026-10-21 10:06:30','',0,'http://dli-wp-e2e.local/riuso/',7,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (64,3,'2026-10-21 10:06:31','2026-10-21 10:06:31','','Dichiarazione di accessibilità','','publish','closed','closed','','dichiarazione-di-accessibilita','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/dichiarazione-di-accessibilita/',0,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (65,3,'2026-10-21 10:06:31','2026-10-21 10:06:31',' ','','','publish','closed','closed','','65','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/65/',2,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (66,3,'2026-10-21 10:06:31','2026-10-21 10:06:31',' ','','','publish','closed','closed','','66','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/66/',3,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (67,3,'2026-10-21 10:06:31','2026-10-21 10:06:31',' ','','','publish','closed','closed','','67','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/67/',0,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (68,3,'2026-10-21 10:06:31','2026-10-21 10:06:31',' ','','','publish','closed','closed','','68','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/68/',2,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (69,3,'2026-10-21 10:06:31','2026-10-21 10:06:31',' ','','','publish','closed','closed','','69','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/69/',3,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (70,3,'2026-10-21 10:06:31','2026-10-21 10:06:31',' ','','','publish','closed','closed','','70','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/70/',4,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (71,3,'2026-10-21 10:06:31','2026-10-21 10:06:31',' ','','','publish','closed','closed','','71','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/71/',5,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (72,3,'2026-10-21 10:06:31','2026-10-21 10:06:31',' ','','','publish','closed','closed','','72','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/72/',6,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (73,3,'2026-10-21 10:06:31','2026-10-21 10:06:31',' ','','','publish','closed','closed','','73','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/73/',0,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (74,3,'2026-10-21 10:06:31','2026-10-21 10:06:31',' ','','','publish','closed','closed','','74','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/74/',0,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (75,3,'2026-10-21 10:06:31','2026-10-21 10:06:31',' ','','','publish','closed','closed','','75','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/75/',2,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (76,3,'2026-10-21 10:06:31','2026-10-21 10:06:31',' ','','','publish','closed','closed','','76','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/76/',3,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (77,3,'2026-10-21 10:06:31','2026-10-21 10:06:31','','Where we are','','publish','closed','closed','','where-we-are','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/where-we-are/',4,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (78,3,'2026-10-21 10:06:31','2026-10-21 10:06:31',' ','','','publish','closed','closed','','78','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/78/',0,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (79,3,'2026-10-21 10:06:31','2026-10-21 10:06:31','','Cookies policy','','publish','closed','closed','','cookies-policy-2','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/cookies-policy-2/',2,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (80,3,'2026-10-21 10:06:31','2026-10-21 10:06:31',' ','','','publish','closed','closed','','80','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/80/',3,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (81,3,'2026-10-21 10:06:31','2026-10-21 10:06:31',' ','','','publish','closed','closed','','81','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/81/',4,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (82,3,'2026-10-21 10:06:31','2026-10-21 10:06:31',' ','','','publish','closed','closed','','82','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/82/',5,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (83,3,'2026-10-21 10:06:31','2026-10-21 10:06:31',' ','','','publish','closed','closed','','83','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/83/',6,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (84,3,'2026-10-21 10:06:31','2026-10-21 10:06:31','','Riuso','','publish','closed','closed','','riuso-2','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/riuso-2/',7,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (85,3,'2026-10-21 10:06:31','2026-10-21 10:06:31','','Accessibility declaration','','publish','closed','closed','','accessibility-declaration','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/accessibility-declaration/',0,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (86,3,'2026-10-21 10:06:31','2026-10-21 10:06:31',' ','','','publish','closed','closed','','86','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/86/',2,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (87,3,'2026-10-21 10:06:31','2026-10-21 10:06:31',' ','','','publish','closed','closed','','87','','','2026-10-21 10:06:31','2026-10-21 10:06:31','',0,'http://dli-wp-e2e.local/87/',3,'nav_menu_item','',0);
INSERT INTO `wp_posts` VALUES (88,3,'2026-10-21 12:03:45','0000-00-00 00:00:00','','Auto Draft','','auto-draft','closed','open','','','','','2026-10-21 12:03:45','0000-00-00 00:00:00','',0,'http://dli-wp-e2e.local/?p=88',0,'post','',0);
/*!40000 ALTER TABLE `wp_posts` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `wp_term_relationships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_term_relationships` (
  `object_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `term_taxonomy_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `term_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`object_id`,`term_taxonomy_id`),
  KEY `term_taxonomy_id` (`term_taxonomy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `wp_term_relationships` WRITE;
/*!40000 ALTER TABLE `wp_term_relationships` DISABLE KEYS */;
INSERT INTO `wp_term_relationships` VALUES (1,1,0);
INSERT INTO `wp_term_relationships` VALUES (1,2,0);
INSERT INTO `wp_term_relationships` VALUES (1,3,0);
INSERT INTO `wp_term_relationships` VALUES (1,8,0);
INSERT INTO `wp_term_relationships` VALUES (2,2,0);
INSERT INTO `wp_term_relationships` VALUES (3,2,0);
INSERT INTO `wp_term_relationships` VALUES (3,10,0);
INSERT INTO `wp_term_relationships` VALUES (4,2,0);
INSERT INTO `wp_term_relationships` VALUES (5,2,0);
INSERT INTO `wp_term_relationships` VALUES (5,9,0);
INSERT INTO `wp_term_relationships` VALUES (6,5,0);
INSERT INTO `wp_term_relationships` VALUES (6,9,0);
INSERT INTO `wp_term_relationships` VALUES (7,5,0);
INSERT INTO `wp_term_relationships` VALUES (7,6,0);
INSERT INTO `wp_term_relationships` VALUES (7,8,0);
INSERT INTO `wp_term_relationships` VALUES (7,10,0);
INSERT INTO `wp_term_relationships` VALUES (8,2,0);
INSERT INTO `wp_term_relationships` VALUES (8,11,0);
INSERT INTO `wp_term_relationships` VALUES (9,5,0);
INSERT INTO `wp_term_relationships` VALUES (9,11,0);
INSERT INTO `wp_term_relationships` VALUES (10,2,0);
INSERT INTO `wp_term_relationships` VALUES (10,12,0);
INSERT INTO `wp_term_relationships` VALUES (11,5,0);
INSERT INTO `wp_term_relationships` VALUES (11,12,0);
INSERT INTO `wp_term_relationships` VALUES (12,2,0);
INSERT INTO `wp_term_relationships` VALUES (12,13,0);
INSERT INTO `wp_term_relationships` VALUES (13,5,0);
INSERT INTO `wp_term_relationships` VALUES (13,13,0);
INSERT INTO `wp_term_relationships` VALUES (14,2,0);
INSERT INTO `wp_term_relationships` VALUES (14,14,0);
INSERT INTO `wp_term_relationships` VALUES (15,5,0);
INSERT INTO `wp_term_relationships` VALUES (15,14,0);
INSERT INTO `wp_term_relationships` VALUES (16,2,0);
INSERT INTO `wp_term_relationships` VALUES (16,15,0);
INSERT INTO `wp_term_relationships` VALUES (17,5,0);
INSERT INTO `wp_term_relationships` VALUES (17,15,0);
INSERT INTO `wp_term_relationships` VALUES (18,2,0);
INSERT INTO `wp_term_relationships` VALUES (18,16,0);
INSERT INTO `wp_term_relationships` VALUES (19,5,0);
INSERT INTO `wp_term_relationships` VALUES (19,16,0);
INSERT INTO `wp_term_relationships` VALUES (20,2,0);
INSERT INTO `wp_term_relationships` VALUES (20,17,0);
INSERT INTO `wp_term_relationships` VALUES (21,5,0);
INSERT INTO `wp_term_relationships` VALUES (21,17,0);
INSERT INTO `wp_term_relationships` VALUES (22,2,0);
INSERT INTO `wp_term_relationships` VALUES (22,18,0);
INSERT INTO `wp_term_relationships` VALUES (23,5,0);
INSERT INTO `wp_term_relationships` VALUES (23,18,0);
INSERT INTO `wp_term_relationships` VALUES (24,2,0);
INSERT INTO `wp_term_relationships` VALUES (24,19,0);
INSERT INTO `wp_term_relationships` VALUES (25,5,0);
INSERT INTO `wp_term_relationships` VALUES (25,19,0);
INSERT INTO `wp_term_relationships` VALUES (26,2,0);
INSERT INTO `wp_term_relationships` VALUES (26,20,0);
INSERT INTO `wp_term_relationships` VALUES (27,5,0);
INSERT INTO `wp_term_relationships` VALUES (27,20,0);
INSERT INTO `wp_term_relationships` VALUES (28,2,0);
INSERT INTO `wp_term_relationships` VALUES (28,21,0);
INSERT INTO `wp_term_relationships` VALUES (29,5,0);
INSERT INTO `wp_term_relationships` VALUES (29,21,0);
INSERT INTO `wp_term_relationships` VALUES (30,2,0);
INSERT INTO `wp_term_relationships` VALUES (30,3,0);
INSERT INTO `wp_term_relationships` VALUES (30,22,0);
INSERT INTO `wp_term_relationships` VALUES (30,31,0);
INSERT INTO `wp_term_relationships` VALUES (31,5,0);
INSERT INTO `wp_term_relationships` VALUES (31,22,0);
INSERT INTO `wp_term_relationships` VALUES (32,2,0);
INSERT INTO `wp_term_relationships` VALUES (32,6,0);
INSERT INTO `wp_term_relationships` VALUES (32,23,0);
INSERT INTO `wp_term_relationships` VALUES (32,31,0);
INSERT INTO `wp_term_relationships` VALUES (33,5,0);
INSERT INTO `wp_term_relationships` VALUES (33,23,0);
INSERT INTO `wp_term_relationships` VALUES (34,2,0);
INSERT INTO `wp_term_relationships` VALUES (34,24,0);
INSERT INTO `wp_term_relationships` VALUES (35,3,0);
INSERT INTO `wp_term_relationships` VALUES (35,5,0);
INSERT INTO `wp_term_relationships` VALUES (35,24,0);
INSERT INTO `wp_term_relationships` VALUES (35,36,0);
INSERT INTO `wp_term_relationships` VALUES (36,2,0);
INSERT INTO `wp_term_relationships` VALUES (36,25,0);
INSERT INTO `wp_term_relationships` VALUES (37,5,0);
INSERT INTO `wp_term_relationships` VALUES (37,6,0);
INSERT INTO `wp_term_relationships` VALUES (37,25,0);
INSERT INTO `wp_term_relationships` VALUES (37,36,0);
INSERT INTO `wp_term_relationships` VALUES (38,2,0);
INSERT INTO `wp_term_relationships` VALUES (38,26,0);
INSERT INTO `wp_term_relationships` VALUES (39,5,0);
INSERT INTO `wp_term_relationships` VALUES (39,26,0);
INSERT INTO `wp_term_relationships` VALUES (40,2,0);
INSERT INTO `wp_term_relationships` VALUES (40,3,0);
INSERT INTO `wp_term_relationships` VALUES (40,27,0);
INSERT INTO `wp_term_relationships` VALUES (40,41,0);
INSERT INTO `wp_term_relationships` VALUES (41,5,0);
INSERT INTO `wp_term_relationships` VALUES (41,27,0);
INSERT INTO `wp_term_relationships` VALUES (42,2,0);
INSERT INTO `wp_term_relationships` VALUES (42,6,0);
INSERT INTO `wp_term_relationships` VALUES (42,28,0);
INSERT INTO `wp_term_relationships` VALUES (42,41,0);
INSERT INTO `wp_term_relationships` VALUES (43,5,0);
INSERT INTO `wp_term_relationships` VALUES (43,28,0);
INSERT INTO `wp_term_relationships` VALUES (44,2,0);
INSERT INTO `wp_term_relationships` VALUES (44,29,0);
INSERT INTO `wp_term_relationships` VALUES (45,3,0);
INSERT INTO `wp_term_relationships` VALUES (45,5,0);
INSERT INTO `wp_term_relationships` VALUES (45,29,0);
INSERT INTO `wp_term_relationships` VALUES (45,46,0);
INSERT INTO `wp_term_relationships` VALUES (46,80,0);
INSERT INTO `wp_term_relationships` VALUES (47,6,0);
INSERT INTO `wp_term_relationships` VALUES (47,46,0);
INSERT INTO `wp_term_relationships` VALUES (47,80,0);
INSERT INTO `wp_term_relationships` VALUES (48,80,0);
INSERT INTO `wp_term_relationships` VALUES (49,80,0);
INSERT INTO `wp_term_relationships` VALUES (50,3,0);
INSERT INTO `wp_term_relationships` VALUES (50,51,0);
INSERT INTO `wp_term_relationships` VALUES (50,80,0);
INSERT INTO `wp_term_relationships` VALUES (51,80,0);
INSERT INTO `wp_term_relationships` VALUES (52,6,0);
INSERT INTO `wp_term_relationships` VALUES (52,51,0);
INSERT INTO `wp_term_relationships` VALUES (52,81,0);
INSERT INTO `wp_term_relationships` VALUES (53,82,0);
INSERT INTO `wp_term_relationships` VALUES (54,82,0);
INSERT INTO `wp_term_relationships` VALUES (55,3,0);
INSERT INTO `wp_term_relationships` VALUES (55,56,0);
INSERT INTO `wp_term_relationships` VALUES (55,82,0);
INSERT INTO `wp_term_relationships` VALUES (56,82,0);
INSERT INTO `wp_term_relationships` VALUES (57,6,0);
INSERT INTO `wp_term_relationships` VALUES (57,56,0);
INSERT INTO `wp_term_relationships` VALUES (57,83,0);
INSERT INTO `wp_term_relationships` VALUES (58,83,0);
INSERT INTO `wp_term_relationships` VALUES (59,83,0);
INSERT INTO `wp_term_relationships` VALUES (60,3,0);
INSERT INTO `wp_term_relationships` VALUES (60,61,0);
INSERT INTO `wp_term_relationships` VALUES (60,83,0);
INSERT INTO `wp_term_relationships` VALUES (61,83,0);
INSERT INTO `wp_term_relationships` VALUES (62,6,0);
INSERT INTO `wp_term_relationships` VALUES (62,61,0);
INSERT INTO `wp_term_relationships` VALUES (62,83,0);
INSERT INTO `wp_term_relationships` VALUES (63,83,0);
INSERT INTO `wp_term_relationships` VALUES (64,84,0);
INSERT INTO `wp_term_relationships` VALUES (65,3,0);
INSERT INTO `wp_term_relationships` VALUES (65,66,0);
INSERT INTO `wp_term_relationships` VALUES (65,84,0);
INSERT INTO `wp_term_relationships` VALUES (66,84,0);
INSERT INTO `wp_term_relationships` VALUES (67,6,0);
INSERT INTO `wp_term_relationships` VALUES (67,66,0);
INSERT INTO `wp_term_relationships` VALUES (67,85,0);
INSERT INTO `wp_term_relationships` VALUES (68,85,0);
INSERT INTO `wp_term_relationships` VALUES (69,85,0);
INSERT INTO `wp_term_relationships` VALUES (70,3,0);
INSERT INTO `wp_term_relationships` VALUES (70,71,0);
INSERT INTO `wp_term_relationships` VALUES (70,85,0);
INSERT INTO `wp_term_relationships` VALUES (71,85,0);
INSERT INTO `wp_term_relationships` VALUES (72,6,0);
INSERT INTO `wp_term_relationships` VALUES (72,71,0);
INSERT INTO `wp_term_relationships` VALUES (72,85,0);
INSERT INTO `wp_term_relationships` VALUES (73,86,0);
INSERT INTO `wp_term_relationships` VALUES (74,87,0);
INSERT INTO `wp_term_relationships` VALUES (75,3,0);
INSERT INTO `wp_term_relationships` VALUES (75,76,0);
INSERT INTO `wp_term_relationships` VALUES (75,87,0);
INSERT INTO `wp_term_relationships` VALUES (76,87,0);
INSERT INTO `wp_term_relationships` VALUES (77,6,0);
INSERT INTO `wp_term_relationships` VALUES (77,76,0);
INSERT INTO `wp_term_relationships` VALUES (77,87,0);
INSERT INTO `wp_term_relationships` VALUES (78,88,0);
INSERT INTO `wp_term_relationships` VALUES (79,88,0);
INSERT INTO `wp_term_relationships` VALUES (80,88,0);
INSERT INTO `wp_term_relationships` VALUES (81,88,0);
INSERT INTO `wp_term_relationships` VALUES (82,88,0);
INSERT INTO `wp_term_relationships` VALUES (83,88,0);
INSERT INTO `wp_term_relationships` VALUES (84,88,0);
INSERT INTO `wp_term_relationships` VALUES (85,89,0);
INSERT INTO `wp_term_relationships` VALUES (86,89,0);
INSERT INTO `wp_term_relationships` VALUES (87,89,0);
INSERT INTO `wp_term_relationships` VALUES (88,2,0);
/*!40000 ALTER TABLE `wp_term_relationships` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `wp_term_taxonomy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_term_taxonomy` (
  `term_taxonomy_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `taxonomy` varchar(32) NOT NULL DEFAULT '',
  `description` longtext NOT NULL,
  `parent` bigint(20) unsigned NOT NULL DEFAULT 0,
  `count` bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`term_taxonomy_id`),
  UNIQUE KEY `term_id_taxonomy` (`term_id`,`taxonomy`),
  KEY `taxonomy` (`taxonomy`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `wp_term_taxonomy` WRITE;
/*!40000 ALTER TABLE `wp_term_taxonomy` DISABLE KEYS */;
INSERT INTO `wp_term_taxonomy` VALUES (1,1,'category','',0,1);
INSERT INTO `wp_term_taxonomy` VALUES (2,2,'language','a:3:{s:6:\"locale\";s:5:\"it_IT\";s:3:\"rtl\";b:0;s:9:\"flag_code\";s:2:\"it\";}',0,22);
INSERT INTO `wp_term_taxonomy` VALUES (3,3,'term_language','',0,11);
INSERT INTO `wp_term_taxonomy` VALUES (5,5,'language','a:3:{s:6:\"locale\";s:5:\"en_US\";s:3:\"rtl\";b:0;s:9:\"flag_code\";s:2:\"us\";}',0,21);
INSERT INTO `wp_term_taxonomy` VALUES (6,6,'term_language','',0,11);
INSERT INTO `wp_term_taxonomy` VALUES (7,7,'category','',0,0);
INSERT INTO `wp_term_taxonomy` VALUES (8,8,'term_translations','a:2:{s:2:\"en\";i:7;s:2:\"it\";i:1;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (9,9,'post_translations','a:2:{s:2:\"it\";i:5;s:2:\"en\";i:6;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (10,10,'post_translations','a:2:{s:2:\"it\";i:3;s:2:\"en\";i:7;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (11,11,'post_translations','a:2:{s:2:\"it\";i:8;s:2:\"en\";i:9;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (12,12,'post_translations','a:2:{s:2:\"it\";i:10;s:2:\"en\";i:11;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (13,13,'post_translations','a:2:{s:2:\"it\";i:12;s:2:\"en\";i:13;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (14,14,'post_translations','a:2:{s:2:\"it\";i:14;s:2:\"en\";i:15;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (15,15,'post_translations','a:2:{s:2:\"it\";i:16;s:2:\"en\";i:17;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (16,16,'post_translations','a:2:{s:2:\"it\";i:18;s:2:\"en\";i:19;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (17,17,'post_translations','a:2:{s:2:\"it\";i:20;s:2:\"en\";i:21;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (18,18,'post_translations','a:2:{s:2:\"it\";i:22;s:2:\"en\";i:23;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (19,19,'post_translations','a:2:{s:2:\"it\";i:24;s:2:\"en\";i:25;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (20,20,'post_translations','a:2:{s:2:\"it\";i:26;s:2:\"en\";i:27;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (21,21,'post_translations','a:2:{s:2:\"it\";i:28;s:2:\"en\";i:29;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (22,22,'post_translations','a:2:{s:2:\"it\";i:30;s:2:\"en\";i:31;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (23,23,'post_translations','a:2:{s:2:\"it\";i:32;s:2:\"en\";i:33;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (24,24,'post_translations','a:2:{s:2:\"it\";i:34;s:2:\"en\";i:35;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (25,25,'post_translations','a:2:{s:2:\"it\";i:36;s:2:\"en\";i:37;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (26,26,'post_translations','a:2:{s:2:\"it\";i:38;s:2:\"en\";i:39;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (27,27,'post_translations','a:2:{s:2:\"it\";i:40;s:2:\"en\";i:41;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (28,28,'post_translations','a:2:{s:2:\"it\";i:42;s:2:\"en\";i:43;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (29,29,'post_translations','a:2:{s:2:\"it\";i:44;s:2:\"en\";i:45;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (30,30,'tipologia-luogo','',0,0);
INSERT INTO `wp_term_taxonomy` VALUES (31,31,'term_translations','a:2:{s:2:\"it\";i:30;s:2:\"en\";i:32;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (32,32,'tipologia-luogo','',0,0);
INSERT INTO `wp_term_taxonomy` VALUES (35,35,'tipologia-luogo','',0,0);
INSERT INTO `wp_term_taxonomy` VALUES (36,36,'term_translations','a:2:{s:2:\"it\";i:35;s:2:\"en\";i:37;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (37,37,'tipologia-luogo','',0,0);
INSERT INTO `wp_term_taxonomy` VALUES (40,40,'tipologia-luogo','',0,0);
INSERT INTO `wp_term_taxonomy` VALUES (41,41,'term_translations','a:2:{s:2:\"it\";i:40;s:2:\"en\";i:42;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (42,42,'tipologia-luogo','',0,0);
INSERT INTO `wp_term_taxonomy` VALUES (45,45,'tipologia-luogo','',0,0);
INSERT INTO `wp_term_taxonomy` VALUES (46,46,'term_translations','a:2:{s:2:\"it\";i:45;s:2:\"en\";i:47;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (47,47,'tipologia-luogo','',0,0);
INSERT INTO `wp_term_taxonomy` VALUES (50,50,'tipologia-luogo','',0,0);
INSERT INTO `wp_term_taxonomy` VALUES (51,51,'term_translations','a:2:{s:2:\"it\";i:50;s:2:\"en\";i:52;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (52,52,'tipologia-luogo','',0,0);
INSERT INTO `wp_term_taxonomy` VALUES (55,55,'tipologia-luogo','',0,0);
INSERT INTO `wp_term_taxonomy` VALUES (56,56,'term_translations','a:2:{s:2:\"it\";i:55;s:2:\"en\";i:57;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (57,57,'tipologia-luogo','',0,0);
INSERT INTO `wp_term_taxonomy` VALUES (60,60,'tipologia-luogo','',0,0);
INSERT INTO `wp_term_taxonomy` VALUES (61,61,'term_translations','a:2:{s:2:\"it\";i:60;s:2:\"en\";i:62;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (62,62,'tipologia-luogo','',0,0);
INSERT INTO `wp_term_taxonomy` VALUES (65,65,'struttura','',0,0);
INSERT INTO `wp_term_taxonomy` VALUES (66,66,'term_translations','a:2:{s:2:\"it\";i:65;s:2:\"en\";i:67;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (67,67,'struttura','',0,0);
INSERT INTO `wp_term_taxonomy` VALUES (70,70,'tipo-pubblicazione','',0,0);
INSERT INTO `wp_term_taxonomy` VALUES (71,71,'term_translations','a:2:{s:2:\"it\";i:70;s:2:\"en\";i:72;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (72,72,'tipo-pubblicazione','',0,0);
INSERT INTO `wp_term_taxonomy` VALUES (75,75,'tipo-pubblicazione','',0,0);
INSERT INTO `wp_term_taxonomy` VALUES (76,76,'term_translations','a:2:{s:2:\"it\";i:75;s:2:\"en\";i:77;}',0,2);
INSERT INTO `wp_term_taxonomy` VALUES (77,77,'tipo-pubblicazione','',0,0);
INSERT INTO `wp_term_taxonomy` VALUES (80,80,'nav_menu','',0,6);
INSERT INTO `wp_term_taxonomy` VALUES (81,81,'nav_menu','',0,1);
INSERT INTO `wp_term_taxonomy` VALUES (82,82,'nav_menu','',0,4);
INSERT INTO `wp_term_taxonomy` VALUES (83,83,'nav_menu','',0,7);
INSERT INTO `wp_term_taxonomy` VALUES (84,84,'nav_menu','',0,3);
INSERT INTO `wp_term_taxonomy` VALUES (85,85,'nav_menu','',0,6);
INSERT INTO `wp_term_taxonomy` VALUES (86,86,'nav_menu','',0,1);
INSERT INTO `wp_term_taxonomy` VALUES (87,87,'nav_menu','',0,4);
INSERT INTO `wp_term_taxonomy` VALUES (88,88,'nav_menu','',0,7);
INSERT INTO `wp_term_taxonomy` VALUES (89,89,'nav_menu','',0,3);
/*!40000 ALTER TABLE `wp_term_taxonomy` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `wp_termmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_termmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `term_id` (`term_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `wp_termmeta` WRITE;
/*!40000 ALTER TABLE `wp_termmeta` DISABLE KEYS */;
/*!40000 ALTER TABLE `wp_termmeta` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `wp_terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_terms` (
  `term_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `slug` varchar(200) NOT NULL DEFAULT '',
  `term_group` bigint(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`term_id`),
  KEY `slug` (`slug`(191)),
  KEY `name` (`name`(191))
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `wp_terms` WRITE;
/*!40000 ALTER TABLE `wp_terms` DISABLE KEYS */;
INSERT INTO `wp_terms` VALUES (1,'Uncategorized','uncategorized',0);
INSERT INTO `wp_terms` VALUES (2,'Italiano','it',0);
INSERT INTO `wp_terms` VALUES (3,'Italiano','pll_it',0);
INSERT INTO `wp_terms` VALUES (5,'English','en',0);
INSERT INTO `wp_terms` VALUES (6,'English','pll_en',0);
INSERT INTO `wp_terms` VALUES (7,'Uncategorized','uncategorized-en',0);
INSERT INTO `wp_terms` VALUES (8,'pll_6ad88e4cf4148','pll_6ad88e4cf4148',0);
INSERT INTO `wp_terms` VALUES (9,'pll_6ad88ea3a7351','pll_6ad88ea3a7351',0);
INSERT INTO `wp_terms` VALUES (10,'pll_6ad88ea3b6f35','pll_6ad88ea3b6f35',0);
INSERT INTO `wp_terms` VALUES (11,'pll_6ad88ea3c9452','pll_6ad88ea3c9452',0);
INSERT INTO `wp_terms` VALUES (12,'pll_6ad88ea3d93e8','pll_6ad88ea3d93e8',0);
INSERT INTO `wp_terms` VALUES (13,'pll_6ad88ea3eb6ec','pll_6ad88ea3eb6ec',0);
INSERT INTO `wp_terms` VALUES (14,'pll_6ad88ea40b556','pll_6ad88ea40b556',0);
INSERT INTO `wp_terms` VALUES (15,'pll_6ad88ea41d36b','pll_6ad88ea41d36b',0);
INSERT INTO `wp_terms` VALUES (16,'pll_6ad88ea42dcdc','pll_6ad88ea42dcdc',0);
INSERT INTO `wp_terms` VALUES (17,'pll_6ad88ea440350','pll_6ad88ea440350',0);
INSERT INTO `wp_terms` VALUES (18,'pll_6ad88ea455147','pll_6ad88ea455147',0);
INSERT INTO `wp_terms` VALUES (19,'pll_6ad88ea46abb1','pll_6ad88ea46abb1',0);
INSERT INTO `wp_terms` VALUES (20,'pll_6ad88ea47e950','pll_6ad88ea47e950',0);
INSERT INTO `wp_terms` VALUES (21,'pll_6ad88ea494d11','pll_6ad88ea494d11',0);
INSERT INTO `wp_terms` VALUES (22,'pll_6ad88ea4a73fd','pll_6ad88ea4a73fd',0);
INSERT INTO `wp_terms` VALUES (23,'pll_6ad88ea4be0fd','pll_6ad88ea4be0fd',0);
INSERT INTO `wp_terms` VALUES (24,'pll_6ad88ea4d16bd','pll_6ad88ea4d16bd',0);
INSERT INTO `wp_terms` VALUES (25,'pll_6ad88ea4e4116','pll_6ad88ea4e4116',0);
INSERT INTO `wp_terms` VALUES (26,'pll_6ad88ea505bec','pll_6ad88ea505bec',0);
INSERT INTO `wp_terms` VALUES (27,'pll_6ad88ea51b6c6','pll_6ad88ea51b6c6',0);
INSERT INTO `wp_terms` VALUES (28,'pll_6ad88ea531de9','pll_6ad88ea531de9',0);
INSERT INTO `wp_terms` VALUES (29,'pll_6ad88ea5470f9','pll_6ad88ea5470f9',0);
INSERT INTO `wp_terms` VALUES (30,'Aula','aula',0);
INSERT INTO `wp_terms` VALUES (31,'pll_6ad88ea54e56c','pll_6ad88ea54e56c',0);
INSERT INTO `wp_terms` VALUES (32,'Classroom','classroom',0);
INSERT INTO `wp_terms` VALUES (35,'Aula studio','aula-studio',0);
INSERT INTO `wp_terms` VALUES (36,'pll_6ad88ea564e16','pll_6ad88ea564e16',0);
INSERT INTO `wp_terms` VALUES (37,'Study room','study-room',0);
INSERT INTO `wp_terms` VALUES (40,'Biblioteca','biblioteca',0);
INSERT INTO `wp_terms` VALUES (41,'pll_6ad88ea584503','pll_6ad88ea584503',0);
INSERT INTO `wp_terms` VALUES (42,'Library','library',0);
INSERT INTO `wp_terms` VALUES (45,'Laboratorio','laboratorio',0);
INSERT INTO `wp_terms` VALUES (46,'pll_6ad88ea59e369','pll_6ad88ea59e369',0);
INSERT INTO `wp_terms` VALUES (47,'Laboratory','laboratory',0);
INSERT INTO `wp_terms` VALUES (50,'Parcheggio','parcheggio',0);
INSERT INTO `wp_terms` VALUES (51,'pll_6ad88ea5b92d2','pll_6ad88ea5b92d2',0);
INSERT INTO `wp_terms` VALUES (52,'Parking','parking',0);
INSERT INTO `wp_terms` VALUES (55,'Ufficio','ufficio',0);
INSERT INTO `wp_terms` VALUES (56,'pll_6ad88ea5d6830','pll_6ad88ea5d6830',0);
INSERT INTO `wp_terms` VALUES (57,'Office','office',0);
INSERT INTO `wp_terms` VALUES (60,'Sede','sede',0);
INSERT INTO `wp_terms` VALUES (61,'pll_6ad88ea5f2375','pll_6ad88ea5f2375',0);
INSERT INTO `wp_terms` VALUES (62,'Headquarter','headquarter',0);
INSERT INTO `wp_terms` VALUES (65,'Prima struttura','prima-struttura',0);
INSERT INTO `wp_terms` VALUES (66,'pll_6ad88ea614a32','pll_6ad88ea614a32',0);
INSERT INTO `wp_terms` VALUES (67,'First structure','first-structure',0);
INSERT INTO `wp_terms` VALUES (70,'Articolo in rivista','articolo-in-rivista',0);
INSERT INTO `wp_terms` VALUES (71,'pll_6ad88ea62c999','pll_6ad88ea62c999',0);
INSERT INTO `wp_terms` VALUES (72,'Article in journal','article-in-journal',0);
INSERT INTO `wp_terms` VALUES (75,'Monografia','monografia',0);
INSERT INTO `wp_terms` VALUES (76,'pll_6ad88ea643984','pll_6ad88ea643984',0);
INSERT INTO `wp_terms` VALUES (77,'Monograph','monograph',0);
INSERT INTO `wp_terms` VALUES (80,'Menu principale','menu-principale',0);
INSERT INTO `wp_terms` VALUES (81,'Presentazione','presentazione',0);
INSERT INTO `wp_terms` VALUES (82,'Novità','novita',0);
INSERT INTO `wp_terms` VALUES (83,'Menu footer','menu-footer',0);
INSERT INTO `wp_terms` VALUES (84,'Link utili','link-utili',0);
INSERT INTO `wp_terms` VALUES (85,'Main menu','main-menu',0);
INSERT INTO `wp_terms` VALUES (86,'Presentation','presentation',0);
INSERT INTO `wp_terms` VALUES (87,'News','news',0);
INSERT INTO `wp_terms` VALUES (88,'Menu footer en','menu-footer-en',0);
INSERT INTO `wp_terms` VALUES (89,'Useful link','useful-link',0);
/*!40000 ALTER TABLE `wp_terms` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `wp_usermeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_usermeta` (
  `umeta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL,
  PRIMARY KEY (`umeta_id`),
  KEY `user_id` (`user_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `wp_usermeta` WRITE;
/*!40000 ALTER TABLE `wp_usermeta` DISABLE KEYS */;
INSERT INTO `wp_usermeta` VALUES (44,3,'nickname','e2e-admin');
INSERT INTO `wp_usermeta` VALUES (45,3,'first_name','');
INSERT INTO `wp_usermeta` VALUES (46,3,'last_name','');
INSERT INTO `wp_usermeta` VALUES (47,3,'description','');
INSERT INTO `wp_usermeta` VALUES (48,3,'rich_editing','true');
INSERT INTO `wp_usermeta` VALUES (49,3,'syntax_highlighting','true');
INSERT INTO `wp_usermeta` VALUES (50,3,'infinite_scrolling','true');
INSERT INTO `wp_usermeta` VALUES (51,3,'comment_shortcuts','false');
INSERT INTO `wp_usermeta` VALUES (52,3,'admin_color','modern');
INSERT INTO `wp_usermeta` VALUES (53,3,'use_ssl','0');
INSERT INTO `wp_usermeta` VALUES (54,3,'show_admin_bar_front','true');
INSERT INTO `wp_usermeta` VALUES (55,3,'locale','');
INSERT INTO `wp_usermeta` VALUES (56,3,'wp_capabilities','a:1:{s:13:\"administrator\";b:1;}');
INSERT INTO `wp_usermeta` VALUES (57,3,'wp_user_level','10');
INSERT INTO `wp_usermeta` VALUES (58,3,'dismissed_wp_pointers','');
INSERT INTO `wp_usermeta` VALUES (59,4,'nickname','e2e-super-editor');
INSERT INTO `wp_usermeta` VALUES (60,4,'first_name','');
INSERT INTO `wp_usermeta` VALUES (61,4,'last_name','');
INSERT INTO `wp_usermeta` VALUES (62,4,'description','');
INSERT INTO `wp_usermeta` VALUES (63,4,'rich_editing','true');
INSERT INTO `wp_usermeta` VALUES (64,4,'syntax_highlighting','true');
INSERT INTO `wp_usermeta` VALUES (65,4,'infinite_scrolling','true');
INSERT INTO `wp_usermeta` VALUES (66,4,'comment_shortcuts','false');
INSERT INTO `wp_usermeta` VALUES (67,4,'admin_color','modern');
INSERT INTO `wp_usermeta` VALUES (68,4,'use_ssl','0');
INSERT INTO `wp_usermeta` VALUES (69,4,'show_admin_bar_front','true');
INSERT INTO `wp_usermeta` VALUES (70,4,'locale','');
INSERT INTO `wp_usermeta` VALUES (71,4,'wp_capabilities','a:1:{s:16:\"dli_super_editor\";b:1;}');
INSERT INTO `wp_usermeta` VALUES (72,4,'wp_user_level','7');
INSERT INTO `wp_usermeta` VALUES (73,4,'dismissed_wp_pointers','');
INSERT INTO `wp_usermeta` VALUES (74,5,'nickname','e2e-editor');
INSERT INTO `wp_usermeta` VALUES (75,5,'first_name','');
INSERT INTO `wp_usermeta` VALUES (76,5,'last_name','');
INSERT INTO `wp_usermeta` VALUES (77,5,'description','');
INSERT INTO `wp_usermeta` VALUES (78,5,'rich_editing','true');
INSERT INTO `wp_usermeta` VALUES (79,5,'syntax_highlighting','true');
INSERT INTO `wp_usermeta` VALUES (80,5,'infinite_scrolling','true');
INSERT INTO `wp_usermeta` VALUES (81,5,'comment_shortcuts','false');
INSERT INTO `wp_usermeta` VALUES (82,5,'admin_color','modern');
INSERT INTO `wp_usermeta` VALUES (83,5,'use_ssl','0');
INSERT INTO `wp_usermeta` VALUES (84,5,'show_admin_bar_front','true');
INSERT INTO `wp_usermeta` VALUES (85,5,'locale','');
INSERT INTO `wp_usermeta` VALUES (86,5,'wp_capabilities','a:1:{s:6:\"editor\";b:1;}');
INSERT INTO `wp_usermeta` VALUES (87,5,'wp_user_level','7');
INSERT INTO `wp_usermeta` VALUES (88,5,'dismissed_wp_pointers','');
/*!40000 ALTER TABLE `wp_usermeta` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `wp_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wp_users` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) NOT NULL DEFAULT '',
  `user_pass` varchar(255) NOT NULL DEFAULT '',
  `user_nicename` varchar(50) NOT NULL DEFAULT '',
  `user_email` varchar(100) NOT NULL DEFAULT '',
  `user_url` varchar(100) NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_activation_key` varchar(255) NOT NULL DEFAULT '',
  `user_status` int(11) NOT NULL DEFAULT 0,
  `display_name` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `user_login_key` (`user_login`),
  KEY `user_nicename` (`user_nicename`),
  KEY `user_email` (`user_email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `wp_users` WRITE;
/*!40000 ALTER TABLE `wp_users` DISABLE KEYS */;
INSERT INTO `wp_users` VALUES (3,'e2e-admin','$wp$2y$10$jpmlcWLIgH5jcOm7RNxtt.JsS7T91rVMAUk/lVRM78Ze0wUb4JwfG','e2e-admin','e2e-admin@example.test','','2026-10-21 12:10:08','',0,'E2E Admin');
INSERT INTO `wp_users` VALUES (4,'e2e-super-editor','$wp$2y$10$bbVE3YgKe2OYwI1at7KVDugb7JsxtzF1xvVcWN9xmp6/PjwxgRWom','e2e-super-editor','e2e-super-editor@example.test','','2026-10-21 12:10:11','',0,'E2E Super Editor');
INSERT INTO `wp_users` VALUES (5,'e2e-editor','$wp$2y$10$cUSAnPc4nFiVgt3aD57nOutafAmsKY8heZS091Aa2DMx1Jh9lEQAC','e2e-editor','e2e-editor@example.test','','2026-10-21 12:10:14','',0,'E2E Editor');
/*!40000 ALTER TABLE `wp_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

