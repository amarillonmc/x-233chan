-- --------------------------------------------------------
	
--
-- Drop tables not used in Kusaba X.
--

DROP TABLE `PREFIX_announcements`;
DROP TABLE `PREFIX_faptcha_attempts`;
DROP TABLE `PREFIX_spamfilter`;
DROP TABLE `PREFIX_menu`;
ALTER TABLE `PREFIX_boards` DROP enablefaptcha;
ALTER TABLE `PREFIX_boards` DROP enableporn;
ALTER TABLE `PREFIX_staff` DROP suspended;
ALTER TABLE `PREFIX_staff` DROP access;
ALTER TABLE `PREFIX_banlist` CHANGE `note` `staffnote` TEXT;



-- --------------------------------------------------------

--
-- Table structure for table `ads`
--

CREATE TABLE `PREFIX_ads` (
  `id` smallint(1) unsigned NOT NULL,
  `position` varchar(3) NOT NULL,
  `disp` tinyint(1) NOT NULL,
  `boards` varchar(255) NOT NULL,
  `code` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `PREFIX_announcements` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parentid` int(10) unsigned NOT NULL default '0',
  `subject` varchar(255) NOT NULL,
  `postedat` int(20) NOT NULL,
  `postedby` varchar(75) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `embeds`
--

CREATE TABLE `PREFIX_embeds` (
  `id` tinyint(5) unsigned NOT NULL auto_increment,
  `filetype` varchar(3) NOT NULL,
  `name` varchar(255) NOT NULL,
  `videourl` varchar(510) NOT NULL,
  `width` tinyint(3) unsigned NOT NULL,
  `height` tinyint(3) unsigned NOT NULL,
  `code` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `PREFIX_faq` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `order` smallint(5) unsigned NOT NULL,
  `heading` varchar(255) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rules`
--

CREATE TABLE `PREFIX_rules` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `order` smallint(5) unsigned NOT NULL,
  `heading` varchar(255) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

ALTER TABLE `PREFIX_boards` CHANGE `maxpages` `maxpages` INT( 20 ) NOT NULL DEFAULT '11';
ALTER TABLE `PREFIX_boards` CHANGE `maxpages` `maxpages` INT( 20 ) NOT NULL DEFAULT '11';
INSERT INTO `PREFIX_ads` (`id`, `position`, `disp`, `boards`, `code`) VALUES (1, 'top', 0, '', 'Right Frame Top'), (2, 'bot', 0, '', 'Right Frame Bottom');
INSERT INTO `PREFIX_embeds` (`filetype`, `name`, `videourl`, `width`, `height`, `code`) VALUES ('you', 'Youtube', 'http://www.youtube.com/watch?v=', 200, 164, '<object type="application/x-shockwave-flash" width="SET_WIDTH" height="SET_HEIGHT" data="http://www.youtube.com/v/EMBED_ID"> <param name="movie" value="http://www.youtube.com/v/EMBED_ID" /> </object>'), ('goo', 'Google', 'http://video.google.com/videoplay?docid=', 200, 164, '<embed width="SET_WIDTH" height="SET_HEIGHT" id="VideoPlayback" type="application/x-shockwave-flash" src="http://video.google.com/googleplayer.swf?docId=EMBED_ID"></embed>') ;