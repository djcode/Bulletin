<?php

/**
 * OSIC: db_connect.php
 * 
 * The database connection script.
 * Use config.php to change settings.
 * 
 * @author Chris Goerner <cgoerner@users.sourceforge.net>
 * @version $Id: db_connect.php,v 1.1 2004/03/19 08:59:04 cgoerner Exp $
 * @version $Revision: 1.1 $
 * 
 **/

include 'config.php';

$dbcx = mysql_connect($db_server, $db_user, $db_password);

if (!$dbcx) {
    echo "Error connecting to database server: <b>$db_server</b> <BR>";
    exit();
} 
if (! @mysql_select_db($db_database, $dbcx)) {
	echo "Error selecting database: <b>$db_database</b> <BR>" . mysql_error();
    exit();
} 

?>