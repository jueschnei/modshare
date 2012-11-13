-- phpMyAdmin SQL Dump
-- version 2.8.0.1
-- http://www.phpmyadmin.net
-- 
-- Host: custsqlmoo29
-- Generation Time: Nov 12, 2012 at 08:23 PM
-- Server version: 5.0.91
-- PHP Version: 4.4.9
-- 
-- Database: `modshare`
-- 

-- --------------------------------------------------------

-- 
-- Dumping data for table `config`
-- 

INSERT INTO `config` VALUES ('adminnews', '');
INSERT INTO `config` VALUES ('status', 'normal');
INSERT INTO `config` VALUES ('downloadfiles', 'Download EVERYTHING\n');
INSERT INTO `config` VALUES ('maintenance_msg', 'This site is down for maintenance. Please come back in a few minutes.');
INSERT INTO `config` VALUES ('announcement', '');
INSERT INTO `config` VALUES ('election_question', 'Who do you think should be moderator?');
INSERT INTO `config` VALUES ('lasthelp', '1351463968');
INSERT INTO `config` VALUES ('log', 'on');
INSERT INTO `config` VALUES ('election', '0');
INSERT INTO `config` VALUES ('election_mandatory', 'yes');

-- --------------------------------------------------------

-- 
-- Dumping data for table `flux_config`
-- 

INSERT INTO `flux_config` VALUES ('o_cur_version', '1.4.9');
INSERT INTO `flux_config` VALUES ('o_database_revision', '15');
INSERT INTO `flux_config` VALUES ('o_searchindex_revision', '2');
INSERT INTO `flux_config` VALUES ('o_parser_revision', '2');
INSERT INTO `flux_config` VALUES ('o_board_title', 'Mod Share Forums');
INSERT INTO `flux_config` VALUES ('o_board_desc', '<p><span>Welcome to the Mod Share forums.</span></p>');
INSERT INTO `flux_config` VALUES ('o_default_timezone', '-5');
INSERT INTO `flux_config` VALUES ('o_time_format', 'H:i:s');
INSERT INTO `flux_config` VALUES ('o_date_format', 'Y-m-d');
INSERT INTO `flux_config` VALUES ('o_timeout_visit', '259200');
INSERT INTO `flux_config` VALUES ('o_timeout_online', '300');
INSERT INTO `flux_config` VALUES ('o_redirect_delay', '0');
INSERT INTO `flux_config` VALUES ('o_show_version', '0');
INSERT INTO `flux_config` VALUES ('o_show_user_info', '1');
INSERT INTO `flux_config` VALUES ('o_show_post_count', '1');
INSERT INTO `flux_config` VALUES ('o_signatures', '1');
INSERT INTO `flux_config` VALUES ('o_smilies', '1');
INSERT INTO `flux_config` VALUES ('o_smilies_sig', '1');
INSERT INTO `flux_config` VALUES ('o_make_links', '0');
INSERT INTO `flux_config` VALUES ('o_default_lang', 'English');
INSERT INTO `flux_config` VALUES ('o_default_style', 'Oxygen');
INSERT INTO `flux_config` VALUES ('o_default_user_group', '5');
INSERT INTO `flux_config` VALUES ('o_topic_review', '15');
INSERT INTO `flux_config` VALUES ('o_disp_topics_default', '30');
INSERT INTO `flux_config` VALUES ('o_disp_posts_default', '25');
INSERT INTO `flux_config` VALUES ('o_indent_num_spaces', '4');
INSERT INTO `flux_config` VALUES ('o_quote_depth', '3');
INSERT INTO `flux_config` VALUES ('o_quickpost', '1');
INSERT INTO `flux_config` VALUES ('o_users_online', '1');
INSERT INTO `flux_config` VALUES ('o_censoring', '1');
INSERT INTO `flux_config` VALUES ('o_ranks', '0');
INSERT INTO `flux_config` VALUES ('o_show_dot', '0');
INSERT INTO `flux_config` VALUES ('o_topic_views', '1');
INSERT INTO `flux_config` VALUES ('o_quickjump', '1');
INSERT INTO `flux_config` VALUES ('o_gzip', '0');
INSERT INTO `flux_config` VALUES ('o_additional_navlinks', '');
INSERT INTO `flux_config` VALUES ('o_report_method', '0');
INSERT INTO `flux_config` VALUES ('o_regs_report', '0');
INSERT INTO `flux_config` VALUES ('o_default_email_setting', '2');
INSERT INTO `flux_config` VALUES ('o_mailing_list', 'jacob@futuresight.org');
INSERT INTO `flux_config` VALUES ('o_avatars', '0');
INSERT INTO `flux_config` VALUES ('o_avatars_dir', 'img/avatars');
INSERT INTO `flux_config` VALUES ('o_avatars_width', '60');
INSERT INTO `flux_config` VALUES ('o_avatars_height', '60');
INSERT INTO `flux_config` VALUES ('o_avatars_size', '10240');
INSERT INTO `flux_config` VALUES ('o_search_all_forums', '1');
INSERT INTO `flux_config` VALUES ('o_base_url', 'http://modshare.org/forums');
INSERT INTO `flux_config` VALUES ('o_admin_email', 'helpdesk@futuresight.org');
INSERT INTO `flux_config` VALUES ('o_webmaster_email', 'no-reply@modshare.tk');
INSERT INTO `flux_config` VALUES ('o_forum_subscriptions', '0');
INSERT INTO `flux_config` VALUES ('o_topic_subscriptions', '0');
INSERT INTO `flux_config` VALUES ('o_smtp_host', NULL);
INSERT INTO `flux_config` VALUES ('o_smtp_user', NULL);
INSERT INTO `flux_config` VALUES ('o_smtp_pass', NULL);
INSERT INTO `flux_config` VALUES ('o_smtp_ssl', '0');
INSERT INTO `flux_config` VALUES ('o_regs_allow', '1');
INSERT INTO `flux_config` VALUES ('o_regs_verify', '0');
INSERT INTO `flux_config` VALUES ('o_announcement', '0');
INSERT INTO `flux_config` VALUES ('o_announcement_message', '<b style="font-weight: bold; font-size: 18px">Moderator elections have begun! <a href="/vote">Vote here</a>!</b>');
INSERT INTO `flux_config` VALUES ('o_rules', '0');
INSERT INTO `flux_config` VALUES ('o_rules_message', 'Enter your rules here');
INSERT INTO `flux_config` VALUES ('o_maintenance', '0');
INSERT INTO `flux_config` VALUES ('o_maintenance_message', 'The forums are temporarily down for maintenance. Please try again in a few minutes.');
INSERT INTO `flux_config` VALUES ('o_default_dst', '1');
INSERT INTO `flux_config` VALUES ('o_feed_type', '1');
INSERT INTO `flux_config` VALUES ('o_feed_ttl', '0');
INSERT INTO `flux_config` VALUES ('p_message_bbcode', '1');
INSERT INTO `flux_config` VALUES ('p_message_img_tag', '1');
INSERT INTO `flux_config` VALUES ('p_message_all_caps', '1');
INSERT INTO `flux_config` VALUES ('p_subject_all_caps', '1');
INSERT INTO `flux_config` VALUES ('p_sig_all_caps', '1');
INSERT INTO `flux_config` VALUES ('p_sig_bbcode', '1');
INSERT INTO `flux_config` VALUES ('p_sig_img_tag', '1');
INSERT INTO `flux_config` VALUES ('p_sig_length', '600');
INSERT INTO `flux_config` VALUES ('p_sig_lines', '4');
INSERT INTO `flux_config` VALUES ('p_allow_banned_email', '1');
INSERT INTO `flux_config` VALUES ('p_allow_dupe_email', '0');
INSERT INTO `flux_config` VALUES ('p_force_guest_email', '1');
INSERT INTO `flux_config` VALUES ('o_poll_enabled', '1');
INSERT INTO `flux_config` VALUES ('o_poll_max_ques', '5');
INSERT INTO `flux_config` VALUES ('o_poll_max_field', '10');
INSERT INTO `flux_config` VALUES ('o_poll_time', '0');
INSERT INTO `flux_config` VALUES ('o_poll_term', '0');
INSERT INTO `flux_config` VALUES ('o_poll_guest', '1');

-- --------------------------------------------------------

-- 
-- Dumping data for table `flux_groups`
-- 

INSERT INTO `flux_groups` VALUES (1, 'Mod Share Team', 'Mod Share Team', 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 1, 1, 1, 1, 1);
INSERT INTO `flux_groups` VALUES (2, 'Community Moderators', 'Community Moderator', 1, 1, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 1, 1, 0, 1, 0);
INSERT INTO `flux_groups` VALUES (3, 'Guests', 'Guest', 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 45, 10, 0, 0, 1, 1, 1, 1, 1);
INSERT INTO `flux_groups` VALUES (4, 'Mod Share-ers', 'Mod Share-er', 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 0, 45, 0, 60, 0, 1, 1, 1, 1, 1);
INSERT INTO `flux_groups` VALUES (5, 'New Mod Share-ers', 'New Mod Share-er', 0, 0, 0, 0, 0, 0, 1, 0, 1, 1, 0, 0, 0, 0, 1, 0, 0, 120, 10, 60, 30, 1, 1, 1, 1, 1);
INSERT INTO `flux_groups` VALUES (6, 'Restricted Mod Share-ers', 'Mod Share-er', 0, 0, 0, 0, 0, 0, 1, 0, 1, 1, 0, 0, 0, 0, 1, 0, 0, 180, 20, 60, 90, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

-- 
-- Dumping data for table `flux_users`
-- 

INSERT INTO `flux_users` VALUES (1, 3, 'Guest', 'Guest', 'Guest', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0, 'English', 'Mod Share Default', 0, NULL, NULL, NULL, NULL, 0, '0.0.0.0', 1346972744, NULL, NULL, NULL, NULL);