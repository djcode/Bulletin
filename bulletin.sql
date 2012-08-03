# 
# Database : `bulletin`
# 

# --------------------------------------------------------

#
# Table structure for table `events`
#

CREATE TABLE `sb_events` (
  `ID` int(11) NOT NULL auto_increment,
  `visible` int(11) NOT NULL default '0',
  `event` text NOT NULL,
  `details` text NOT NULL,
  `event_date` date NOT NULL default '0000-00-00',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `author` text NOT NULL,
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

#
# Table structure for table `importance`
#

CREATE TABLE `sb_importance` (
  `ID` int(11) NOT NULL auto_increment,
  `importance` text NOT NULL,
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

#
# Dumping data for table `importance`
#

INSERT INTO `sb_importance` VALUES (1, 'Normal');
INSERT INTO `sb_importance` VALUES (2, 'High');

# --------------------------------------------------------

#
# Table structure for table `notices`
#

CREATE TABLE `sb_notices` (
  `ID` int(11) NOT NULL auto_increment,
  `visible` int(11) NOT NULL default '0',
  `title` text NOT NULL,
  `notice` longtext NOT NULL,
  `display_date` date NOT NULL default '0000-00-00',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `importance` int(11) NOT NULL default '0',
  `permission` int(11) NOT NULL default '0',
  `typeface` text NOT NULL,
  `size` int(11) NOT NULL default '0',
  `author` text NOT NULL,
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

#
# Table structure for table `permissions`
#

CREATE TABLE `sb_permissions` (
  `ID` int(11) NOT NULL auto_increment,
  `notice_id` int(11) NOT NULL default '0',
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

#
# Table structure for table `settings`
#

CREATE TABLE `sb_settings` (
  `ID` int(11) NOT NULL auto_increment,
  `name` text NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

#
# Table structure for table `users`
#

CREATE TABLE `sb_users` (
  `ID` int(11) NOT NULL auto_increment,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `realname` text NOT NULL,
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;

#
# Dumping data for table `users`
#

INSERT INTO `sb_users` VALUES (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Administrator');
INSERT INTO `sb_users` VALUES (2, 'test', '098f6bcd4621d373cade4e832627b4f6', 'Test');
INSERT INTO `sb_users` VALUES (3, 'staff', 'd416a002f0deffd2e6ccd8fd88ff92dc', 'All Staff');
INSERT INTO `sb_users` VALUES (4, 'student', 'cd73502828457d15655bbd7a63fb0bc8', 'All Students');