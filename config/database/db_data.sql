-- phpMyAdmin SQL Dump
-- version 2.8.0.1
-- http://www.phpmyadmin.net
-- 
-- Host: custsqlmoo29
-- Generation Time: Jan 02, 2013 at 10:52 PM
-- Server version: 5.0.91
-- PHP Version: 4.4.9
-- 
-- Database: `modshare`
-- 

-- 
-- Dumping data for table `config`
-- 

INSERT INTO `config` (`c_name`, `c_value`) VALUES ('adminnews', '<p><span style="font-weight: bold;" id="news1357182718">j:</span> I implemented DDoS protection software.</p><p><span style="font-weight: bold;" id="news1357185050">j:</span> What is the casmsg table for?</p>'),
('status', 'normal'),
('downloadfiles', '__LS reminder: configure dreamweaver at home\n__LS reminder 2: make recovery disc\n__LS reminder 3: install batterycare\n'),
('maintenance_msg', 'This website is down for maintenance while we make some skin improvements.'),
('announcement', 'Happy new year!'),
('election_question', 'Who do you think should be moderator?'),
('lasthelp', '1356540852'),
('log', 'on'),
('election', '0'),
('election_mandatory', 'yes'),
('lastprojectid', '19'),
('comment_flood', '20');

-- 
-- Dumping data for table `flux_config`
-- 

INSERT INTO `flux_config` (`conf_name`, `conf_value`) VALUES ('o_cur_version', '1.4.9'),
('o_database_revision', '15'),
('o_searchindex_revision', '2'),
('o_parser_revision', '2'),
('o_board_title', 'Mod Share Forums'),
('o_board_desc', '<p><span>Welcome to the Mod Share forums.</span></p>'),
('o_default_timezone', '-5'),
('o_time_format', 'H:i:s'),
('o_date_format', 'Y-m-d'),
('o_timeout_visit', '259200'),
('o_timeout_online', '300'),
('o_redirect_delay', '0'),
('o_show_version', '0'),
('o_show_user_info', '1'),
('o_show_post_count', '1'),
('o_signatures', '1'),
('o_smilies', '1'),
('o_smilies_sig', '1'),
('o_make_links', '0'),
('o_default_lang', 'English'),
('o_default_style', 'Oxygen'),
('o_default_user_group', '5'),
('o_topic_review', '15'),
('o_disp_topics_default', '30'),
('o_disp_posts_default', '25'),
('o_indent_num_spaces', '4'),
('o_quote_depth', '3'),
('o_quickpost', '1'),
('o_users_online', '1'),
('o_censoring', '1'),
('o_ranks', '0'),
('o_show_dot', '0'),
('o_topic_views', '1'),
('o_quickjump', '1'),
('o_gzip', '0'),
('o_additional_navlinks', ''),
('o_report_method', '0'),
('o_regs_report', '0'),
('o_default_email_setting', '2'),
('o_mailing_list', 'jacob@futuresight.org'),
('o_avatars', '0'),
('o_avatars_dir', 'img/avatars'),
('o_avatars_width', '60'),
('o_avatars_height', '60'),
('o_avatars_size', '10240'),
('o_search_all_forums', '1'),
('o_base_url', 'http://modshare.org/forums'),
('o_admin_email', 'helpdesk@futuresight.org'),
('o_webmaster_email', 'no-reply@modshare.org'),
('o_forum_subscriptions', '0'),
('o_topic_subscriptions', '0'),
('o_smtp_host', NULL),
('o_smtp_user', NULL),
('o_smtp_pass', NULL),
('o_smtp_ssl', '0'),
('o_regs_allow', '1'),
('o_regs_verify', '0'),
('o_announcement', '0'),
('o_announcement_message', 'These forums will be down for maintenance at 5:30 Scratch Time. They will be back up after a few minutes.'),
('o_rules', '0'),
('o_rules_message', 'Enter your rules here'),
('o_maintenance', '0'),
('o_maintenance_message', 'These forums are down for maintenance while we make code improvements. Please come back later.'),
('o_default_dst', '1'),
('o_feed_type', '1'),
('o_feed_ttl', '0'),
('p_message_bbcode', '1'),
('p_message_img_tag', '1'),
('p_message_all_caps', '1'),
('p_subject_all_caps', '1'),
('p_sig_all_caps', '1'),
('p_sig_bbcode', '1'),
('p_sig_img_tag', '1'),
('p_sig_length', '600'),
('p_sig_lines', '4'),
('p_allow_banned_email', '1'),
('p_allow_dupe_email', '0'),
('p_force_guest_email', '1'),
('o_poll_enabled', '1'),
('o_poll_max_ques', '5'),
('o_poll_max_field', '10'),
('o_poll_time', '0'),
('o_poll_term', '0'),
('o_poll_guest', '1');

-- 
-- Dumping data for table `flux_groups`
-- 

INSERT INTO `flux_groups` (`g_id`, `g_title`, `g_user_title`, `g_moderator`, `g_global_moderator`, `g_mod_edit_users`, `g_mod_rename_users`, `g_mod_change_passwords`, `g_mod_ban_users`, `g_read_board`, `g_view_users`, `g_post_replies`, `g_post_topics`, `g_edit_posts`, `g_delete_posts`, `g_delete_topics`, `g_set_title`, `g_search`, `g_search_users`, `g_send_email`, `g_post_flood`, `g_search_flood`, `g_email_flood`, `g_report_flood`, `g_bin_posts`, `g_bin_topics`, `g_empty_bin`, `g_bin_restore`, `g_bin_delete`) VALUES (1, 'Mod Share Team', 'Santa', 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 1, 1, 0, 1, 0),
(2, 'Community Moderators', 'Santa''s Helper', 1, 1, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 1, 1, 0, 1, 0),
(3, 'Guests', 'Guest', 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 45, 10, 0, 0, 1, 1, 1, 1, 1),
(4, 'Mod Share-ers', 'Mod Share-er', 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 0, 45, 0, 60, 0, 1, 1, 1, 1, 1),
(5, 'New Mod Share-ers', 'New Mod Share-er', 0, 0, 0, 0, 0, 0, 1, 0, 1, 1, 0, 0, 0, 0, 1, 0, 0, 180, 10, 60, 30, 1, 1, 1, 1, 1),
(6, 'Restricted Mod Share-ers', 'Mod Share-er', 0, 0, 0, 0, 0, 0, 1, 0, 1, 1, 0, 0, 0, 0, 1, 0, 0, 180, 20, 60, 90, 1, 1, 1, 1, 1);

-- 
-- Dumping data for table `flux_users`
-- 

INSERT INTO `flux_users` (`id`, `group_id`, `username`, `password`, `email`, `title`, `realname`, `url`, `jabber`, `icq`, `msn`, `aim`, `yahoo`, `location`, `signature`, `disp_topics`, `disp_posts`, `email_setting`, `notify_with_post`, `auto_notify`, `show_smilies`, `show_img`, `show_img_sig`, `show_avatars`, `show_sig`, `timezone`, `dst`, `time_format`, `date_format`, `language`, `style`, `num_posts`, `last_post`, `last_search`, `last_email_sent`, `last_report_sent`, `registered`, `registration_ip`, `last_visit`, `admin_note`, `activate_string`, `activate_key`, `tracked_topics`) VALUES (1, 3, 'Guest', 'Guest', 'Guest', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0, 'English', 'Mod Share Default', 0, NULL, NULL, NULL, NULL, 0, '0.0.0.0', 1346972744, NULL, NULL, NULL, NULL),