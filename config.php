<?php

/**
 * schoolbulletin: config.php
 * 
 * The configuration file.
 * Use the following variables to change settings for schoolbulletin.
 * 
 * @author Chris Goerner <cgoerner@users.sourceforge.net>
 * @version $Id: config.php,v 1.7 2005/04/10 03:36:28 cgoerner Exp $
 * @version $Revision: 1.7 $
 * 
 **/


error_reporting(E_ALL);
														//DATABASE SETTINGS
$db_server = 'localhost';								//The name of your MySQL sever
$db_user = 'bulletin';										//The user account
$db_password = 'bulletin';										//The password for that account
$db_database = 'bulletin';						//The database name
$db_table_prefix = 'sb_';									//String prepended to table names e.g. "sb_"



//You probably won't need to change anything below this line
														
$app_version = '0.1';
$app_name = 'schoolbulletin';

$importance_table = $db_table_prefix . "importance";
$notices_table = $db_table_prefix . "notices";
$permissions_table = $db_table_prefix . "permissions";
$users_table = $db_table_prefix . "users";
$settings_table = $db_table_prefix . "settings";
$events_table = $db_table_prefix . "events";

?>