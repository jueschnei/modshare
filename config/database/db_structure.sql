-- phpMyAdmin SQL Dump
-- version 2.8.0.1
-- http://www.phpmyadmin.net
-- 
-- Host: custsqlmoo29
-- Generation Time: Nov 12, 2012 at 08:21 PM
-- Server version: 5.0.91
-- PHP Version: 4.4.9
-- 
-- Database: `modshare`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `adminhistory`
-- 

CREATE TABLE `adminhistory` (
  `id` int(11) NOT NULL auto_increment,
  `to_user` int(11) NOT NULL,
  `from_user` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `action` text collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=66 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=66 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `bans`
-- 

CREATE TABLE `bans` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `ip` text collate latin1_general_ci NOT NULL,
  `expires` int(11) default NULL,
  `message` varchar(500) collate latin1_general_ci NOT NULL,
  `starts` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=46 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `cloudvars`
-- 

CREATE TABLE `cloudvars` (
  `name` varchar(100) collate latin1_general_ci NOT NULL,
  `value` text collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `comments`
-- 

CREATE TABLE `comments` (
  `id` int(11) NOT NULL auto_increment,
  `project` int(11) NOT NULL,
  `posted` int(11) NOT NULL,
  `author` int(11) NOT NULL,
  `parent` int(11) default NULL,
  `content` text collate latin1_general_ci NOT NULL,
  `ip_addr` varchar(20) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=409 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=409 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `config`
-- 

CREATE TABLE `config` (
  `c_name` varchar(50) collate latin1_general_ci NOT NULL,
  `c_value` text collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`c_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `election_options`
-- 

CREATE TABLE `election_options` (
  `id` int(11) NOT NULL auto_increment,
  `text` varchar(100) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `election_voted`
-- 

CREATE TABLE `election_voted` (
  `voter` int(11) NOT NULL,
  `choice` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `favorites`
-- 

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=34 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `flags`
-- 

CREATE TABLE `flags` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) default NULL,
  `comment_id` int(11) default NULL,
  `flagged_by` int(11) NOT NULL,
  `reason` varchar(1000) collate latin1_general_ci NOT NULL,
  `time_flagged` int(11) NOT NULL,
  `zapped` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `flux_bans`
-- 

CREATE TABLE `flux_bans` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(200) default NULL,
  `ip` varchar(255) default NULL,
  `email` varchar(80) default NULL,
  `message` varchar(255) default NULL,
  `expire` int(10) unsigned default NULL,
  `ban_creator` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `flux_bans_username_idx` (`username`(25))
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `flux_categories`
-- 

CREATE TABLE `flux_categories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `cat_name` varchar(80) NOT NULL default 'New Category',
  `disp_position` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `flux_censoring`
-- 

CREATE TABLE `flux_censoring` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `search_for` varchar(60) NOT NULL default '',
  `replace_with` varchar(60) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `flux_config`
-- 

CREATE TABLE `flux_config` (
  `conf_name` varchar(255) NOT NULL default '',
  `conf_value` text,
  PRIMARY KEY  (`conf_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `flux_forum_perms`
-- 

CREATE TABLE `flux_forum_perms` (
  `group_id` int(10) NOT NULL default '0',
  `forum_id` int(10) NOT NULL default '0',
  `read_forum` tinyint(1) NOT NULL default '1',
  `post_replies` tinyint(1) NOT NULL default '1',
  `post_topics` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`group_id`,`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `flux_forum_subscriptions`
-- 

CREATE TABLE `flux_forum_subscriptions` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `forum_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `flux_forums`
-- 

CREATE TABLE `flux_forums` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `forum_name` varchar(80) NOT NULL default 'New forum',
  `forum_desc` text,
  `redirect_url` varchar(100) default NULL,
  `moderators` text,
  `num_topics` mediumint(8) unsigned NOT NULL default '0',
  `num_posts` mediumint(8) unsigned NOT NULL default '0',
  `last_post` int(10) unsigned default NULL,
  `last_post_id` int(10) unsigned default NULL,
  `last_poster` varchar(200) default NULL,
  `sort_by` tinyint(1) NOT NULL default '0',
  `disp_position` int(10) NOT NULL default '0',
  `cat_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `flux_groups`
-- 

CREATE TABLE `flux_groups` (
  `g_id` int(10) unsigned NOT NULL auto_increment,
  `g_title` varchar(50) NOT NULL default '',
  `g_user_title` varchar(50) default NULL,
  `g_moderator` tinyint(1) NOT NULL default '0',
  `g_global_moderator` tinyint(1) unsigned NOT NULL default '0',
  `g_mod_edit_users` tinyint(1) NOT NULL default '0',
  `g_mod_rename_users` tinyint(1) NOT NULL default '0',
  `g_mod_change_passwords` tinyint(1) NOT NULL default '0',
  `g_mod_ban_users` tinyint(1) NOT NULL default '0',
  `g_read_board` tinyint(1) NOT NULL default '1',
  `g_view_users` tinyint(1) NOT NULL default '1',
  `g_post_replies` tinyint(1) NOT NULL default '1',
  `g_post_topics` tinyint(1) NOT NULL default '1',
  `g_edit_posts` tinyint(1) NOT NULL default '1',
  `g_delete_posts` tinyint(1) NOT NULL default '1',
  `g_delete_topics` tinyint(1) NOT NULL default '1',
  `g_set_title` tinyint(1) NOT NULL default '1',
  `g_search` tinyint(1) NOT NULL default '1',
  `g_search_users` tinyint(1) NOT NULL default '1',
  `g_send_email` tinyint(1) NOT NULL default '1',
  `g_post_flood` smallint(6) NOT NULL default '30',
  `g_search_flood` smallint(6) NOT NULL default '30',
  `g_email_flood` smallint(6) NOT NULL default '60',
  `g_report_flood` smallint(6) NOT NULL default '60',
  `g_bin_posts` tinyint(1) NOT NULL default '1',
  `g_bin_topics` tinyint(1) NOT NULL default '1',
  `g_empty_bin` tinyint(1) NOT NULL default '1',
  `g_bin_restore` tinyint(1) NOT NULL default '1',
  `g_bin_delete` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`g_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `flux_online`
-- 

CREATE TABLE `flux_online` (
  `user_id` int(10) unsigned NOT NULL default '1',
  `ident` varchar(200) NOT NULL default '',
  `logged` int(10) unsigned NOT NULL default '0',
  `idle` tinyint(1) NOT NULL default '0',
  `last_post` int(10) unsigned default NULL,
  `last_search` int(10) unsigned default NULL,
  UNIQUE KEY `flux_online_user_id_ident_idx` (`user_id`,`ident`(25)),
  KEY `flux_online_ident_idx` (`ident`(25)),
  KEY `flux_online_logged_idx` (`logged`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `flux_poll`
-- 

CREATE TABLE `flux_poll` (
  `tid` int(10) unsigned NOT NULL default '0',
  `question` tinyint(4) NOT NULL default '0',
  `field` tinyint(4) NOT NULL default '0',
  `choice` varchar(255) NOT NULL default '',
  `votes` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`tid`,`question`,`field`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `flux_poll_voted`
-- 

CREATE TABLE `flux_poll_voted` (
  `tid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `rez` text,
  PRIMARY KEY  (`tid`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `flux_posts`
-- 

CREATE TABLE `flux_posts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `poster` varchar(200) NOT NULL default '',
  `poster_id` int(10) unsigned NOT NULL default '1',
  `poster_ip` varchar(39) default NULL,
  `poster_email` varchar(80) default NULL,
  `message` mediumtext,
  `hide_smilies` tinyint(1) NOT NULL default '0',
  `posted` int(10) unsigned NOT NULL default '0',
  `edited` int(10) unsigned default NULL,
  `edited_by` varchar(200) default NULL,
  `topic_id` int(10) unsigned NOT NULL default '0',
  `hidden` int(11) default NULL,
  `num_reports` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `fbb_posts_topic_id_idx` (`topic_id`),
  KEY `fbb_posts_multi_idx` (`poster_id`,`topic_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1509 DEFAULT CHARSET=utf8 AUTO_INCREMENT=1509 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `flux_ranks`
-- 

CREATE TABLE `flux_ranks` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `rank` varchar(50) NOT NULL default '',
  `min_posts` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `flux_reports`
-- 

CREATE TABLE `flux_reports` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `post_id` int(10) unsigned NOT NULL default '0',
  `topic_id` int(10) unsigned NOT NULL default '0',
  `forum_id` int(10) unsigned NOT NULL default '0',
  `reported_by` int(10) unsigned NOT NULL default '0',
  `created` int(10) unsigned NOT NULL default '0',
  `message` text,
  `zapped` int(10) unsigned default NULL,
  `zapped_by` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `flux_reports_zapped_idx` (`zapped`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `flux_search_cache`
-- 

CREATE TABLE `flux_search_cache` (
  `id` int(10) unsigned NOT NULL default '0',
  `ident` varchar(200) NOT NULL default '',
  `search_data` mediumtext,
  PRIMARY KEY  (`id`),
  KEY `flux_search_cache_ident_idx` (`ident`(8))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `flux_search_matches`
-- 

CREATE TABLE `flux_search_matches` (
  `post_id` int(10) unsigned NOT NULL default '0',
  `word_id` int(10) unsigned NOT NULL default '0',
  `subject_match` tinyint(1) NOT NULL default '0',
  KEY `flux_search_matches_word_id_idx` (`word_id`),
  KEY `flux_search_matches_post_id_idx` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `flux_search_words`
-- 

CREATE TABLE `flux_search_words` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `word` varchar(20) character set utf8 collate utf8_bin NOT NULL default '',
  PRIMARY KEY  (`word`),
  KEY `flux_search_words_id_idx` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4394 DEFAULT CHARSET=utf8 AUTO_INCREMENT=4394 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `flux_topic_subscriptions`
-- 

CREATE TABLE `flux_topic_subscriptions` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `topic_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `flux_topics`
-- 

CREATE TABLE `flux_topics` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `poster` varchar(200) NOT NULL default '',
  `subject` varchar(255) NOT NULL default '',
  `posted` int(10) unsigned NOT NULL default '0',
  `first_post_id` int(10) unsigned NOT NULL default '0',
  `last_post` int(10) unsigned NOT NULL default '0',
  `last_post_id` int(10) unsigned NOT NULL default '0',
  `last_poster` varchar(200) default NULL,
  `num_views` mediumint(8) unsigned NOT NULL default '0',
  `num_replies` mediumint(8) unsigned NOT NULL default '0',
  `closed` tinyint(1) NOT NULL default '0',
  `sticky` tinyint(1) NOT NULL default '0',
  `moved_to` int(10) unsigned default NULL,
  `forum_id` int(10) unsigned NOT NULL default '0',
  `poll_type` tinyint(4) NOT NULL default '0',
  `poll_time` int(10) unsigned NOT NULL default '0',
  `poll_term` tinyint(4) NOT NULL default '0',
  `poll_kol` int(10) unsigned NOT NULL default '0',
  `readers` mediumtext,
  PRIMARY KEY  (`id`),
  KEY `fbb_topics_forum_id_idx` (`forum_id`),
  KEY `fbb_topics_moved_to_idx` (`moved_to`),
  KEY `fbb_topics_last_post_idx` (`last_post`),
  KEY `fbb_topics_first_post_id_idx` (`first_post_id`)
) ENGINE=MyISAM AUTO_INCREMENT=259 DEFAULT CHARSET=utf8 AUTO_INCREMENT=259 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `flux_trash_posts`
-- 

CREATE TABLE `flux_trash_posts` (
  `id` int(11) NOT NULL,
  `poster` varchar(200) NOT NULL default '',
  `poster_id` int(10) unsigned NOT NULL default '1',
  `poster_ip` varchar(39) default NULL,
  `poster_email` varchar(80) default NULL,
  `message` mediumtext,
  `hide_smilies` tinyint(1) NOT NULL default '0',
  `posted` int(10) unsigned NOT NULL default '0',
  `edited` int(10) unsigned default NULL,
  `edited_by` varchar(200) default NULL,
  `topic_id` int(10) unsigned NOT NULL default '0',
  `trasher` varchar(200) NOT NULL default '',
  `trasher_id` int(10) unsigned NOT NULL default '1',
  `trashed` int(10) unsigned NOT NULL default '0',
  `post_alone` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `flux_trash_posts_topic_id_idx` (`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `flux_trash_topics`
-- 

CREATE TABLE `flux_trash_topics` (
  `id` int(11) NOT NULL,
  `poster` varchar(200) NOT NULL default '',
  `subject` varchar(255) NOT NULL default '',
  `posted` int(10) unsigned NOT NULL default '0',
  `first_post_id` int(10) unsigned NOT NULL default '0',
  `last_post` int(10) unsigned NOT NULL default '0',
  `last_post_id` int(10) unsigned NOT NULL default '0',
  `last_poster` varchar(200) default NULL,
  `num_views` mediumint(8) unsigned NOT NULL default '0',
  `num_replies` mediumint(8) unsigned NOT NULL default '0',
  `closed` tinyint(1) NOT NULL default '0',
  `sticky` tinyint(1) NOT NULL default '0',
  `moved_to` int(10) unsigned default NULL,
  `forum_id` int(10) unsigned NOT NULL default '0',
  `trasher` varchar(200) NOT NULL default '',
  `trasher_id` int(10) unsigned NOT NULL default '1',
  `trashed` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `flux_trash_topics_forum_id_idx` (`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `flux_users`
-- 

CREATE TABLE `flux_users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `group_id` int(10) unsigned NOT NULL default '3',
  `username` varchar(200) NOT NULL default '',
  `password` varchar(40) NOT NULL default '',
  `email` varchar(80) NOT NULL default '',
  `title` varchar(100) default NULL,
  `realname` varchar(40) default NULL,
  `url` varchar(100) default NULL,
  `jabber` varchar(80) default NULL,
  `icq` varchar(12) default NULL,
  `msn` varchar(80) default NULL,
  `aim` varchar(30) default NULL,
  `yahoo` varchar(30) default NULL,
  `location` varchar(30) default NULL,
  `signature` text,
  `disp_topics` tinyint(3) unsigned default NULL,
  `disp_posts` tinyint(3) unsigned default NULL,
  `email_setting` tinyint(1) NOT NULL default '1',
  `notify_with_post` tinyint(1) NOT NULL default '0',
  `auto_notify` tinyint(1) NOT NULL default '0',
  `show_smilies` tinyint(1) NOT NULL default '1',
  `show_img` tinyint(1) NOT NULL default '1',
  `show_img_sig` tinyint(1) NOT NULL default '1',
  `show_avatars` tinyint(1) NOT NULL default '1',
  `show_sig` tinyint(1) NOT NULL default '1',
  `timezone` float NOT NULL default '0',
  `dst` tinyint(1) NOT NULL default '0',
  `time_format` tinyint(1) NOT NULL default '0',
  `date_format` tinyint(1) NOT NULL default '0',
  `language` varchar(25) NOT NULL default 'English',
  `style` varchar(25) NOT NULL default 'Oxygen',
  `num_posts` int(10) unsigned NOT NULL default '0',
  `last_post` int(10) unsigned default NULL,
  `last_search` int(10) unsigned default NULL,
  `last_email_sent` int(10) unsigned default NULL,
  `last_report_sent` int(10) unsigned default NULL,
  `registered` int(10) unsigned NOT NULL default '0',
  `registration_ip` varchar(39) NOT NULL default '0.0.0.0',
  `last_visit` int(10) unsigned NOT NULL default '0',
  `admin_note` varchar(30) default NULL,
  `activate_string` varchar(80) default NULL,
  `activate_key` varchar(8) default NULL,
  `tracked_topics` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `fbb_users_username_idx` (`username`(25)),
  KEY `fbb_users_registered_idx` (`registered`)
) ENGINE=MyISAM AUTO_INCREMENT=1340665203 DEFAULT CHARSET=utf8 AUTO_INCREMENT=1340665203 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `friends`
-- 

CREATE TABLE `friends` (
  `id` int(11) NOT NULL auto_increment,
  `friender` int(11) NOT NULL,
  `friendee` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `imgsrv`
-- 

CREATE TABLE `imgsrv` (
  `id` int(11) NOT NULL auto_increment,
  `user` int(11) NOT NULL,
  `filename` varchar(100) NOT NULL,
  `uploaded` int(11) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `loves`
-- 

CREATE TABLE `loves` (
  `id` int(11) NOT NULL auto_increment,
  `project` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=44 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `notifications`
-- 

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL auto_increment,
  `user` int(11) NOT NULL,
  `type` tinyint(2) NOT NULL default '0',
  `message` text collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=157 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=157 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `notificationstoadmin`
-- 

CREATE TABLE `notificationstoadmin` (
  `id` int(11) NOT NULL auto_increment,
  `text` text NOT NULL,
  `zapped` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `project_views`
-- 

CREATE TABLE `project_views` (
  `id` int(11) NOT NULL auto_increment,
  `project_id` int(11) NOT NULL,
  `user` int(11) default NULL,
  `ip` varchar(50) default NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1392 DEFAULT CHARSET=latin1 AUTO_INCREMENT=1392 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `projects`
-- 

CREATE TABLE `projects` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(250) collate latin1_general_ci NOT NULL,
  `filename` varchar(100) collate latin1_general_ci NOT NULL,
  `description` text collate latin1_general_ci NOT NULL,
  `license` enum('pd','ms','cc','arr') collate latin1_general_ci NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `thumbnail` longblob NOT NULL,
  `modification` varchar(10) collate latin1_general_ci NOT NULL,
  `status` enum('normal','blocked','deleted') collate latin1_general_ci NOT NULL default 'normal',
  `time` int(11) NOT NULL,
  `ip_addr` varchar(20) collate latin1_general_ci NOT NULL,
  `featured` int(11) default NULL,
  `downloads` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=127 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=127 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `uploadqueue`
-- 

CREATE TABLE `uploadqueue` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(50) NOT NULL,
  `time` int(10) NOT NULL default '0',
  `description` text NOT NULL,
  `modification` varchar(20) NOT NULL,
  `license` varchar(3) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=latin1 AUTO_INCREMENT=32 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(50) collate latin1_general_ci NOT NULL,
  `password_hash` varchar(50) collate latin1_general_ci NOT NULL,
  `status` enum('normal','delbyrequest','disabledbyadmin') collate latin1_general_ci NOT NULL default 'normal',
  `registered` int(11) NOT NULL,
  `registration_ip` varchar(50) collate latin1_general_ci NOT NULL,
  `permission` int(11) NOT NULL default '1',
  `avatar` longblob NOT NULL,
  `timezone` int(11) NOT NULL default '0',
  `style_col` varchar(3) collate latin1_general_ci NOT NULL default '000',
  `style_logo` enum('default','black','white','red','green','blue','yellow','purple') collate latin1_general_ci NOT NULL default 'default' COMMENT 'header logo',
  `imgsrv` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=143 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=143 ;
