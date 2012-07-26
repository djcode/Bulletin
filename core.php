<?php

/**
 * OSIC: core.php
 * 
 * This file contains functions common to all other source files.
 * 
 * @author Chris Goerner <cgoerner@users.sourceforge.net>
 * @version $Id: core.php,v 1.4 2005/04/10 03:36:28 cgoerner Exp $
 * @version $Revision: 1.4 $
 * 
 **/
 

$time_start = getmicrotime();

/**
 * @return float Returns current time in microseconds
 **/
function getmicrotime()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
} 

/**
 * Sends HTML header
 **/
function SendPageHeader()
{
    global $page_bgcolour, $page_heading;
    echo("<HTML><HEAD><TITLE>$page_heading</TITLE><STYLE type=\"text/css\"><!--BODY{background-color:" . $page_bgcolour . ";font-family:verdana,sans-serif;font-size:9pt}TABLE{font-family:verdana,sans-serif;font-size:8pt}PRE{font-family:sans-serif}--></STYLE></HEAD>");
    echo("<BODY>");
} 

/**
 * Sends HTML footer
 **/
function SendPageFooter()
{
    global $app_version, $app_name, $time_start, $show_server_software, $show_server_name;
    $time_end = getmicrotime();
    $time = $time_end - $time_start;
    echo "<small><p><a href=\"http://schoolreports.sourceforge.net/\">$app_name</a> " . $app_version;
    echo "<br>" . number_format($time, 3) . " seconds elapsed.";
    
	if ($show_server_software) {
	    echo "<br>".$_SERVER["SERVER_SOFTWARE"]." ";
	}
	if ($show_server_name) {
	    echo "<br>Running on ".$_SERVER["SERVER_NAME"];
	}
	
	echo "</small></BODY></HTML>";
} 

/**
 * Sends page title enclosed in <h1></h1>
 **/
function SendPageTitle()
{
    global $page_title;
    echo("<h1>$page_title</h1>");
} 

/**
 * Gets the value of GET or POST variables
 * 
 * @param string $nm name of variable to return
 * @return string Returns value of GET or POST variable
 **/
function getVAR($nm)
{
    global $HTTP_GET_VARS, $HTTP_POST_VARS;

    $tmp = "";

    if (isset($HTTP_GET_VARS[$nm])) {
        $tmp = $HTTP_GET_VARS[$nm];
    } 

    if (isset($HTTP_POST_VARS[$nm])) {
        $tmp = $HTTP_POST_VARS[$nm];
    } 

    return $tmp;
} 



/**
 * Will return the value of a particular field given the ID, table, and field name.
 * 
 * @param $table The name of the table
 * @param $id The ID of the record
 * @param $field The name of the field
 * @return string The value of the field
 **/
function getValueFromID($table, $id, $field)
{
    $result = do_mysql_query("SELECT * FROM $table WHERE ID=$id");
    $tempOutput = "";
	
	while ($row = mysql_fetch_array($result)) {
        $tempOutput = $row[$field];
    } 

    return $tempOutput;
} 



/**
 * Gets the highest ID in a table
 * 
 * @param $table The name of the table
 * @return integer The highest ID found
 **/
function getLastID($table)
{
    $result = do_mysql_query("SELECT * FROM $table ORDER BY ID");
    $tmp = "0";
    while ($row = mysql_fetch_array($result)) {
        $tmp = $row["ID"];
    } 
    return $tmp;
} 




/**
 * The function queries the database for the number of records in a table.  If $condition is not
 * empty then it will be used to filter records.
 * 
 * @param $table The name of the table
 * @param string $condition SQL compliant condition
 * @return integer The number of rows
 **/
function rowCount($table,$condition = "")
{
	if ($condition == "") {
	    $result = do_mysql_query("SELECT COUNT(*) AS count FROM $table");
	} else {
		$result = do_mysql_query("SELECT COUNT(*) AS count FROM $table WHERE $condition");
	}
	$row = mysql_fetch_assoc($result);
	return $row['count'];
}



function do_mysql_query($query) {
	$result = mysql_query("$query");

    if (!$result) {
        echo "<p>Error while performing SQL query!<p>" . mysql_error();
		echo "<p>Query: $query";
        exit();
    }
	
	return $result;
}

function sendNavBar($links,$username,$password,$display_date) {
	global $PHP_SELF, $table_heading_bgcolour;
	echo "<table width=100%><tr bgcolor=$table_heading_bgcolour>";
	foreach ($links as $l) {
		switch($l){
			case "home": 
				echo "<td align=center><a href=\"$PHP_SELF?username=$username&password=$password\">Today</a></td>";
				break;
			case "edit": 
				echo "<td align=center><a href=\"$PHP_SELF?display_date=$display_date&edit=on&username=$username&password=$password\">Edit Mode</a></td>";
				break;
			case "logout": 
				echo "<td align=center><a href=\"index.php\">Log Out</a></td>";
				break;
			case "create_notice": 
				echo "<td align=center><a href=\"$PHP_SELF?action=create_notice&display_date=$display_date&username=$username&password=$password\">Create Notice</a></td>";
				break;
			case "add_event": 
				echo "<td align=center><a href=\"$PHP_SELF?action=add_event&display_date=$display_date&username=$username&password=$password\">Add Event</a></td>";
				break;
			case "previous": 
				$tempdate = mktime (0,0,0,date("m",$display_date)  ,date("d",$display_date)-1,date("Y",$display_date));
				echo "<td align=center><a href=\"$PHP_SELF?display_date=$tempdate&username=$username&password=$password\">Previous Day</a></td>";
				break;
			case "next": 
				$tempdate = mktime (0,0,0,date("m",$display_date)  ,date("d",$display_date)+1,date("Y",$display_date));
				echo "<td align=center><a href=\"$PHP_SELF?display_date=$tempdate&username=$username&password=$password\">Next Day</a></td>";
				break;
			case "manage_users": 
				echo "<td align=center><a href=\"$PHP_SELF?action=manage_users&username=$username&password=$password\">Manage Users</a></td>";
				break;
			case "absentees": 
				echo "<td align=center><a href=\"http://intranet/a\"><font color=red>Absentees</font></a></td>";
				break;
			
		} // switch
	} // foreach
	echo "</tr></table>";
}

function getSetting($setting_name, $default_value) {
	global $settings_table;
	
	$result = do_mysql_query("SELECT * FROM $settings_table WHERE name='$setting_name'");
    $row = mysql_fetch_array($result);
	
	$val = $row["value"];
	if ($val=="") {
	    $val = $default_value;
	}
	return $val;
}

function setSetting($setting_name, $setting_value) {
	global $settings_table;
	
	$result = do_mysql_query("DELETE FROM $settings_table WHERE name='$setting_name'");
	$result = do_mysql_query("INSERT INTO $settings_table (ID,name,value) VALUES ('','$setting_name','$setting_value')");
	
}

function authenticate($username,$password) {
	global $users_table;
	$username = strtolower($username);
	$result = do_mysql_query("SELECT * FROM $users_table WHERE username='$username'");
    $row = mysql_fetch_array($result);
	if ($password==$row["password"]) {
	    return true;
	} else {
		return false;
	}
}

function getImportanceList($default="") {
	global $importance_table;
	$result = do_mysql_query("SELECT * FROM $importance_table");
    $tempOutput = "<select size=\"1\" name=\"lstimportance\">";
    while ($row = mysql_fetch_array($result)) {
    	if ($row["ID"] == $default) { $selected="selected"; } else { $selected=""; } 
        $tempOutput .= "<option value=".$row["ID"]." $selected>".$row["importance"]."</option>";
    }
	$tempOutput .= "</select>";
	return $tempOutput;
}

function getUserList() {
	global $users_table;
	$result = do_mysql_query("SELECT * FROM $users_table ORDER BY realname");
    $tempOutput = "<select size=\"3\" name=\"lstusers[]\">";
    $tempOutput .= "<option value=\"-1\">Staff and Students</option>";
	while ($row = mysql_fetch_array($result)) {
        if ($row["username"]!="admin") {
            $selected="";
            if ($row["ID"]=="3") {$selected = "selected";}
            $tempOutput .= "<option $selected value=".$row["ID"].">".$row["realname"]." only</option>";
        }
    }
	
	$tempOutput .= "</select>";
	return $tempOutput;
}

function getEditUserList($nid) {
	global $permissions_table,$users_table;
	$result = do_mysql_query("SELECT * FROM $users_table ORDER BY realname");
    $tempOutput = "<select size=\"3\" name=\"lstusers[]\">";
    
    $p_result = do_mysql_query("SELECT * FROM $permissions_table WHERE notice_id='$nid' AND user_id='-1'");
    if (mysql_num_rows($p_result)) { $selected="selected"; } else { $selected=""; }
    $tempOutput .= "<option value=\"-1\" $selected>Staff and Students</option>";
    
	while ($row = mysql_fetch_array($result)) {
        if ($row["username"]!="admin") {
            $id = $row["ID"];
            $p_result = do_mysql_query("SELECT * FROM $permissions_table WHERE notice_id='$nid' AND user_id='$id'");
            if (mysql_num_rows($p_result)) { $selected="selected"; } else { $selected=""; }
            $tempOutput .= "<option value=$id $selected>".$row["realname"]." only</option>";
        }
    }
	
	$tempOutput .= "</select>";
	return $tempOutput;
}

function getDateList($multi=true,$default) {
    if ($multi) {
    	$tempOutput = "<select size=\"5\" name=\"lstdate[]\" multiple>";
    } else {
    	$tempOutput = "<select size=\"5\" name=\"lstdate[]\">";
    }
    $tempdate = mktime (0,0,0,date("m")  ,date("d"),date("Y"));
    if ($tempdate == $default) { $selected="selected"; } else { $selected=""; } 
	$tempOutput .= "<option value=\"$tempdate\" $selected>Today (".date("jS \o\f F",$tempdate).")</option>";
	
	$tempdate = mktime (0,0,0,date("m")  ,date("d")+1,date("Y"));
	if ($tempdate == $default) { $selected="selected"; } else { $selected=""; } 
	$tempOutput .= "<option value=\"$tempdate\" $selected>Tomorrow (".date("jS \o\f F",$tempdate).")</option>";
	
	for ($i=2; $i<100; $i++) {
		$tempdate  = mktime (0,0,0,date("m")  ,date("d")+$i,date("Y"));
		if (date("l",$tempdate)!="Saturday" AND date("l",$tempdate)!="Sunday") {
			if ($tempdate == $default) { $selected="selected"; } else { $selected=""; } 
			$tempOutput .= "<option value=\"$tempdate\" $selected>".date("l \\t\h\e jS \o\f F",$tempdate)."</option>";
		}
	}

	$tempOutput .= "</select>";
	return $tempOutput;
}

function getTypeFaceList($default="") {
    $tempOutput = "<select size=\"1\" name=\"lsttypeface\">";
    
	if ($default == "Default") { $selected="selected"; } else { $selected=""; }
	$tempOutput .= "<option value=\"Default\" $selected>Default</option>";
	
	if ($default == "Times New Roman") { $selected="selected"; } else { $selected=""; }
	$tempOutput .= "<option value=\"Times New Roman\" $selected>Times New Roman</option>";
	
	if ($default == "Arial") { $selected="selected"; } else { $selected=""; }
	$tempOutput .= "<option value=\"Arial\" $selected>Arial</option>";
	
	if ($default == "Courier New") { $selected="selected"; } else { $selected=""; }
	$tempOutput .= "<option value=\"Courier New\" $selected>Courier New</option>";
	
	$tempOutput .= "</select>";
	return $tempOutput;
}

function getFontSizeList($default="") {
    $tempOutput = "<select size=\"1\" name=\"lstfontsize\">";
    
    if ($default == "2") { $selected="selected"; } else { $selected=""; } 
	$tempOutput .= "<option value=\"2\" $selected>Normal</option>";
	
	if ($default == "3") { $selected="selected"; } else { $selected=""; }
	$tempOutput .= "<option value=\"3\" $selected>+1</option>";
	
	if ($default == "4") { $selected="selected"; } else { $selected=""; }
	$tempOutput .= "<option value=\"4\" $selected>+2</option>";
	
	$tempOutput .= "</select>";
	return $tempOutput;
}

function haveaccess($username,$notice_id) {
	global $users_table,$permissions_table;
	$result = do_mysql_query("SELECT * FROM $users_table WHERE username='$username' LIMIT 1");
	$row = mysql_fetch_array($result);
	$user_id=$row["ID"];
	
	$tempOutput = FALSE;
	
	$result = do_mysql_query("SELECT * FROM $permissions_table WHERE notice_id=$notice_id");
	while ($row = mysql_fetch_array($result)) {
        if ($row["user_id"]==$user_id OR $row["user_id"]=="-1") {
            $tempOutput = TRUE;
        }
    }

	return $tempOutput;
}

function send_notice($type,$msg) {
	global $page_bgcolour, $table_heading_bgcolour;
	switch($type){
		case "ERROR":
			$text = "<font color=red><big>$msg</big></font>";
			$img = "<img src=\"images/stop.png\">";
		break;
		
		case "OK":
			$text = "<font color=green><big>$msg</big></font>";
			$img = "<img src=\"images/ok.png\">";
		break;
		
		case "QUESTION":
			$text = "<font><big>$msg</big></font>";
			$img = "<img src=\"images/question.png\">";
		break;
		
		case "EXCLAMATION":
			$text = "<fontn><big>$msg</big></font>";
			$img = "<img src=\"images/exclamation.png\">";
		break;
		
		case "NONE":
			$text = "<big>$msg</big>";
			$img = "";
		break;
	}
	
	return "<table bgcolor=$page_bgcolour style=\"border: 3px solid $table_heading_bgcolour; padding-left: 4; padding-right: 4; padding-top: 1; padding-bottom: 1\"><tr><td>$img</td><td>$text</td></tr></table>";
	
}

function formattext($text) {
	$temp = trim($text);
	
	$temp = str_replace("\n* ","\n<li>",$temp);
	
	$temp = str_replace("\n","<br>\n",$temp);
	return $temp;
}

# PHP Calendar (version 2.3), written by Keith Devens
# http://keithdevens.com/software/php_calendar
#  see example at http://keithdevens.com/weblog
# License: http://keithdevens.com/software/license

function generate_calendar($year, $month, $days = array(), $day_name_length = 3, $month_href = NULL, $first_day = 0, $pn = array()){
	
	global $page_bgcolour; //cgoerner - added 
	
	$first_of_month = gmmktime(0,0,0,$month,1,$year);
	#remember that mktime will automatically correct if invalid dates are entered
	# for instance, mktime(0,0,0,12,32,1997) will be the date for Jan 1, 1998
	# this provides a built in "rounding" feature to generate_calendar()

	$day_names = array(); #generate all the day names according to the current locale
	for($n=0,$t=(3+$first_day)*86400; $n<7; $n++,$t+=86400) #January 4, 1970 was a Sunday
		$day_names[$n] = ucfirst(gmstrftime('%A',$t)); #%A means full textual day name

	list($month, $year, $month_name, $weekday) = explode(',',gmstrftime('%m,%Y,%B,%w',$first_of_month));
	$weekday = ($weekday + 7 - $first_day) % 7; #adjust for $first_day
	$title   = htmlentities(ucfirst($month_name)).'&nbsp;'.$year;  #note that some locales don't capitalize month and day names

	#Begin calendar. Uses a real <caption>. See http://diveintomark.org/archives/2002/07/03
	@list($p, $pl) = each($pn); @list($n, $nl) = each($pn); #previous and next links, if applicable
	if($p) $p = '<span class="calendar-prev">'.($pl ? '<a href="'.htmlspecialchars($pl).'">'.$p.'</a>' : $p).'</span>&nbsp;';
	if($n) $n = '&nbsp;<span class="calendar-next">'.($nl ? '<a href="'.htmlspecialchars($nl).'">'.$n.'</a>' : $n).'</span>';
	$calendar = '<table class="calendar">'."\n".
		'<caption class="calendar-month">'.$p.($month_href ? '<a href="'.htmlspecialchars($month_href).'">'.$title.'</a>' : $title).$n."</caption>\n<tr>";

	if($day_name_length){ #if the day names should be shown ($day_name_length > 0)
		#if day_name_length is >3, the full name of the day will be printed
		foreach($day_names as $d)
			$calendar .= '<th abbr="'.htmlentities($d).'">'.htmlentities($day_name_length < 4 ? substr($d,0,$day_name_length) : $d).'</th>';
		$calendar .= "</tr>\n<tr>";
	}

	if($weekday > 0) $calendar .= '<td colspan="'.$weekday.'">&nbsp;</td>'; #initial 'empty' days
	for($day=1,$days_in_month=gmdate('t',$first_of_month); $day<=$days_in_month; $day++,$weekday++){
		if($weekday == 7){
			$weekday   = 0; #start a new week
			$calendar .= "</tr>\n<tr>";
		}
		if(isset($days[$day]) and is_array($days[$day])){
			@list($link, $classes, $content) = $days[$day];
			if(is_null($content))  $content  = $day;
							//cgoerner - added bgcolour here
			$calendar .= "<td bgcolor=$page_bgcolour ".($classes ? ' class="'.htmlspecialchars($classes).'">' : '>').
				($link ? '<a href="'.htmlspecialchars($link).'">'.$content.'</a>' : $content).'</td>';
		}
		else $calendar .= "<td>$day</td>";
	}
	if($weekday != 7) $calendar .= '<td colspan="'.(7-$weekday).'">&nbsp;</td>'; #remaining "empty" days

	return $calendar."</tr>\n</table>\n";
}


?>