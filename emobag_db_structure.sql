--
-- Table structure for table `bag_checker`
--

CREATE TABLE IF NOT EXISTS `bag_checker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(64) NOT NULL,
  `msg` text NOT NULL,
  `checked_date` int(11) NOT NULL,
  `taken_flag` tinyint(4) NOT NULL,
  `temp_hold` tinyint(4) NOT NULL,
  `hold_date` int(11) NOT NULL,
  `ip_address` varchar(39) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

CREATE TABLE `bag_queue` (
  `qid` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL,
  `take_date` int(11) NOT NULL,
  `tiny_song_url` varchar(255) NOT NULL,
  `song_title` varchar(255) NOT NULL,
  `song_artist` varchar(255) NOT NULL,
  `msg` text NOT NULL,
  `send_ready` tinyint(4) NOT NULL,
  `sent` tinyint(4) NOT NULL,
  `sent_time` int(11) NOT NULL,
  `ip` varchar(39) NOT NULL,
  `gs_song_id` int(11) NOT NULL,
  PRIMARY KEY (`qid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
