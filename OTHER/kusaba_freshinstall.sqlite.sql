-- --------------------------------------------------------

--
-- Table structure for table ads
--

CREATE TABLE PREFIX_ads (
  id smallint NOT NULL,
  position varchar(3) NOT NULL,
  disp smallint NOT NULL,
  boards varchar(255) NOT NULL,
  code text NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table announcements
--

CREATE TABLE PREFIX_announcements (
  id INTEGER PRIMARY KEY,
  parentid int NOT NULL default '0',
  subject varchar(255) NOT NULL,
  postedat int NOT NULL,
  postedby varchar(75) NOT NULL,
  message text NOT NULL
  
);

-- --------------------------------------------------------

--
-- Table structure for table banlist
--

CREATE TABLE PREFIX_banlist (
  id INTEGER PRIMARY KEY,
  type smallint NOT NULL default '0',
  expired smallint NOT NULL default '0',
  allowread smallint NOT NULL default '1',
  ip varchar(50) NOT NULL,
  ipmd5 char(32) NOT NULL,
  globalban smallint NOT NULL default '0',
  boards varchar(255) NOT NULL,
  `by` varchar(75) NOT NULL,
  at int NOT NULL,
  until int NOT NULL,
  reason text NOT NULL,
  staffnote text NOT NULL,
  appeal text NOT NULL default '',
  appealat int NOT NULL default '0'
  
);

-- --------------------------------------------------------

--
-- Table structure for table bannedhashes
--

CREATE TABLE PREFIX_bannedhashes (
  id INTEGER PRIMARY KEY,
  md5 varchar(255) NOT NULL,
  bantime int,
  description text NOT NULL,
  UNIQUE (id)
);

-- --------------------------------------------------------

--
-- Table structure for table blotter
--

CREATE TABLE PREFIX_blotter (
  id INTEGER PRIMARY KEY,
  important smallint NOT NULL,
  at int NOT NULL,
  message text NOT NULL
  
);

-- --------------------------------------------------------

--
-- Table structure for table boards
--

CREATE TABLE PREFIX_boards (
  id INTEGER PRIMARY KEY,
  `order` smallint,
  name varchar(75) NOT NULL default '',
  type smallint NOT NULL default '0',
  start int NOT NULL,
  uploadtype smallint,
  `desc` varchar(75) NOT NULL default '',
  image varchar(255) NOT NULL,
  section smallint NOT NULL default '0',
  maximagesize int NOT NULL default '1024000',
  maxpages int NOT NULL default '11',
  maxage int NOT NULL default '0',
  markpage smallint NOT NULL default '9',
  maxreplies int NOT NULL default '200',
  messagelength int NOT NULL default '8192',
  createdon int NOT NULL,
  locked smallint NOT NULL default '0',
  includeheader text NOT NULL default '',
  redirecttothread smallint NOT NULL default '0',
  anonymous varchar(255) NOT NULL default 'Anonymous',
  forcedanon smallint NOT NULL default '0',
  embeds_allowed varchar(255) NOT NULL default '',
  trial smallint NOT NULL default '0',
  popular smallint NOT NULL default '0',
  defaultstyle varchar(50) NOT NULL default '',
  locale varchar(30) NOT NULL default '',
  showid smallint NOT NULL default '0',
  compactlist smallint NOT NULL default '0',
  enablereporting smallint NOT NULL default '1',
  enablecaptcha smallint NOT NULL default '0',
  enablenofile smallint NOT NULL default '0',
  enablearchiving smallint NOT NULL default '0',
  enablecatalog smallint NOT NULL default '1',
  loadbalanceurl varchar(255) NOT NULL default '',
  loadbalancepassword varchar(255) NOT NULL default ''
  
);

--
-- Table structure for table board_filetypes
--

CREATE TABLE PREFIX_board_filetypes (
  boardid smallint NOT NULL default '0',
  typeid int NOT NULL default '0'
);

-- --------------------------------------------------------

--
-- Table structure for table embeds
--

CREATE TABLE PREFIX_embeds (
  id INTEGER PRIMARY KEY,
  filetype varchar(3) NOT NULL,
  name varchar(255) NOT NULL,
  videourl varchar(510) NOT NULL,
  width smallint NOT NULL,
  height smallint NOT NULL,
  code text NOT NULL  
);

-- --------------------------------------------------------

--
-- Table structure for table events
--

CREATE TABLE PREFIX_events (
  name varchar(255) NOT NULL,
  at int NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table filetypes
--

CREATE TABLE PREFIX_filetypes (
  id INTEGER PRIMARY KEY,
  filetype varchar(255) NOT NULL,
  mime varchar(255) NOT NULL default '',
  image varchar(255) NOT NULL default '',
  image_w int NOT NULL default '0',
  image_h int NOT NULL default '0',
  force_thumb int NOT NULL default '1'
  
);

-- --------------------------------------------------------

--
-- Table structure for table front
--

CREATE TABLE PREFIX_front (
	id INTEGER PRIMARY KEY,
	page smallint NOT NULL default '0',
	`order` smallint NOT NULL default '0',
	subject varchar(255) NOT NULL,
	message text NOT NULL,
	timestamp int NOT NULL DEFAULT '0',
	poster varchar(75) NOT NULL DEFAULT '',
	email varchar(255) NOT NULL DEFAULT ''
	
);

-- --------------------------------------------------------

--
-- Table structure for table loginattempts
--

CREATE TABLE PREFIX_loginattempts (
  username varchar(255) NOT NULL,
  ip varchar(20) NOT NULL,
  timestamp int NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table modlog
--

CREATE TABLE PREFIX_modlog (
  entry text NOT NULL,
  `user` varchar(255) NOT NULL,
  category smallint NOT NULL default '0',
  timestamp int NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table module_settings
--

CREATE TABLE PREFIX_module_settings (
  module varchar(255) NOT NULL,
  key varchar(255) NOT NULL,
  value text NOT NULL,
  type varchar(255) NOT NULL default 'string'
);

-- --------------------------------------------------------

--
-- Table structure for table posts
--

CREATE TABLE PREFIX_posts (
  id int default '0' ,
  boardid smallint NOT NULL,
  parentid int NOT NULL default '0',
  name varchar(255) NOT NULL,
  tripcode varchar(30) NOT NULL,
  email varchar(255) NOT NULL,
  subject varchar(255) NOT NULL,
  message text NOT NULL,
  password varchar(255) NOT NULL,
  file varchar(50) NOT NULL,
  file_md5 char(32) NOT NULL,
  file_type varchar(20) NOT NULL,
  file_original varchar(255) NOT NULL,
  file_size int NOT NULL default '0',
  file_size_formatted varchar(75) NOT NULL,
  image_w smallint NOT NULL default '0',
  image_h smallint NOT NULL default '0',
  thumb_w smallint NOT NULL default '0',
  thumb_h smallint NOT NULL default '0',
  ip varchar(75) NOT NULL,
  ipmd5 char(32) NOT NULL,
  tag varchar(5) NOT NULL,
  timestamp int NOT NULL,
  stickied smallint NOT NULL default '0',
  locked smallint NOT NULL default '0',
  posterauthority smallint NOT NULL default '0',
  reviewed smallint NOT NULL default '0',
  deleted_timestamp int NOT NULL default '0',
  IS_DELETED smallint NOT NULL default '0',
  bumped int NOT NULL default '0',
  PRIMARY KEY (boardid, id)
);
  CREATE TRIGGER posts_trigger AFTER INSERT on PREFIX_posts
	BEGIN
		UPDATE PREFIX_posts SET id = (SELECT COALESCE(MAX(id),0) + 1  FROM PREFIX_posts WHERE boardid = NEW.boardid) WHERE rowid = last_insert_rowid():semicolon:
	END;
  CREATE INDEX parentid ON PREFIX_posts (parentid);
  CREATE INDEX bumped ON PREFIX_posts (bumped);
  CREATE INDEX file_md5 On PREFIX_posts (file_md5);
  CREATE INDEX stickied ON PREFIX_posts (stickied);

-- --------------------------------------------------------

--
-- Table structure for table reports
--

CREATE TABLE PREFIX_reports (
  id INTEGER PRIMARY KEY,
  cleared smallint NOT NULL default '0',
  board varchar(255) NOT NULL,
  postid int NOT NULL,
  `when` int NOT NULL,
  ip varchar(75) NOT NULL,
  reason varchar(255) NOT NULL
  
);

-- --------------------------------------------------------

--
-- Table structure for table sections
--

CREATE TABLE PREFIX_sections (
  id INTEGER PRIMARY KEY,
  `order` smallint,
  hidden smallint NOT NULL default '0',
  name varchar(255) NOT NULL NOT NULL default '0',
  abbreviation varchar(10) NOT NULL
  
);

-- --------------------------------------------------------

--
-- Table structure for table staff
--

CREATE TABLE PREFIX_staff (
  id INTEGER PRIMARY KEY,
  username varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  salt varchar(3) NOT NULL,
  type smallint NOT NULL default '0',
  boards text,
  addedon int NOT NULL,
  lastactive int NOT NULL default '0'
  
);

-- --------------------------------------------------------

--
-- Table structure for table watchedthreads
--

CREATE TABLE PREFIX_watchedthreads (
  id INTEGER PRIMARY KEY,
  threadid int NOT NULL,
  board varchar(255) NOT NULL,
  ip char(15) NOT NULL,
  lastsawreplyid int NOT NULL
  
);

-- --------------------------------------------------------

--
-- Table structure for table wordfilter
--

CREATE TABLE PREFIX_wordfilter (
  id INTEGER PRIMARY KEY,
  word varchar(75) NOT NULL,
  replacedby varchar(75) NOT NULL,
  boards text NOT NULL,
  time int NOT NULL,
  regex smallint NOT NULL default '0'
  
);


INSERT INTO `PREFIX_ads` (`id`, `position`, `disp`, `boards`, `code`) VALUES (1, 'top', 0, '', 'Right Frame Top');
INSERT INTO `PREFIX_ads` (`id`, `position`, `disp`, `boards`, `code`) VALUES (2, 'bot', 0, '', 'Right Frame Bottom');
INSERT INTO `PREFIX_filetypes` (`filetype`, `force_thumb`) VALUES ('jpg', 0);
INSERT INTO `PREFIX_filetypes` (`filetype`, `force_thumb`) VALUES ('gif', 0);
INSERT INTO `PREFIX_filetypes` (`filetype`, `force_thumb`) VALUES ('png', 0) ;
INSERT INTO `PREFIX_events` (`name`, `at`) VALUES ('pingback', 0);
INSERT INTO `PREFIX_events` (`name`, `at`) VALUES ('sitemap', 0);
INSERT INTO `PREFIX_embeds` (`filetype`, `name`, `videourl`, `width`, `height`, `code`) VALUES ('you', 'Youtube', 'http://www.youtube.com/watch?v=', 200, 164, '<object type="application/x-shockwave-flash" width="SET_WIDTH" height="SET_HEIGHT" data="http://www.youtube.com/v/EMBED_ID"> <param name="movie" value="http://www.youtube.com/v/EMBED_ID" /> </object>');
INSERT INTO `PREFIX_embeds` (`filetype`, `name`, `videourl`, `width`, `height`, `code`) VALUES ('goo', 'Google', 'http://video.google.com/videoplay?docid=', 200, 164, '<embed width="SET_WIDTH" height="SET_HEIGHT" id="VideoPlayback" type="application/x-shockwave-flash" src="http://video.google.com/googleplayer.swf?docId=EMBED_ID"></embed>');
