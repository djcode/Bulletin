<?php

/**
 * schoolbulletin: index.php
 * 
 * The client user interface.
 * 
 * @author Chris Goerner <cgoerner@users.sourceforge.net>
 * @version $Id: index.php,v 1.11 2005/04/10 03:36:28 cgoerner Exp $
 * @version $Revision: 1.11 $
 * 
 **/

include 'config.php';
include 'db_connect.php';
include 'core.php';
include 'read_settings.php';

global $PHP_SELF;

if (!getVAR("display_date")) {
    $display_date = time();
} else {
	$display_date = getVAR("display_date");
}

if (getVAR("username")) {
    $username = getVAR("username");
	$password = getVAR("password");
} else {
    $username = getVAR("txtusername");
    $password = md5(getVAR("txtpassword"));
}

echo "\n<table width=100%><tr><td width=90%>";

SendPageHeader();
SendPageTitle();

echo "\n</td>";

if (authenticate($username,$password)) {
	echo "<td bgcolor=$table_bgcolour valign=top align=right>";
	
		$tempdate = $display_date;
		
		for ($i=1; date("m",$tempdate) == date("m",$display_date); $i++) {
			$tempdate = mktime (0,0,0,date("m",$display_date), $i, date("Y",$display_date));
			$link = "$PHP_SELF?display_date=$tempdate&username=$username&password=$password";
			if (date("d",$display_date) == $i) {
				$days[$i] = array(NULL,NULL,'<span style="color: red;">'.$i.'</span>');
			} else {
				$days[$i] = array($link,"linked-day");
			}
					
		}
	
	    echo generate_calendar(date("Y",$display_date),
	    					   date("m",$display_date),
	    					   $days,
	    					   1
	    					   );
	    
	echo "\n</td><td bgcolor=$table_bgcolour valign=top align=right>";
	        
	    $tempdate = mktime (0,0,0,date("m",$display_date)+1, 1, date("Y",$display_date));
	    $initdate=$tempdate;
		
		for ($i=1; date("m",$tempdate) == date("m",$initdate); $i++) {
			$tempdate = mktime (0,0,0,date("m",$initdate), $i, date("Y",$initdate));
			$link = "$PHP_SELF?display_date=$tempdate&username=$username&password=$password";
			$days[$i] = array($link,"linked-day"); 			
		}
	    
	    echo generate_calendar(date("Y",$initdate),
	    					   date("m",$initdate),
		   					   $days,
	    					   1
	    					   );
	
	echo "\n</td>";
}
echo "</tr></table>";

if (authenticate($username,$password)) {
	sendNavBar(array("previous","logout","home","absentees","create_notice","add_event","edit","next"),$username,$password,$display_date);
}

echo "\n<table width=100%><tr><td width=75% bgcolor=$table_bgcolour valign=top>";

if (!authenticate($username,$password)) {
    
	echo "\n<form method=\"POST\" action=\"$PHP_SELF\">";
	echo "\n<table>";
	echo "\n<tr><td><h2>Please Login</h2></td><td></td></tr>";
	echo "\n<tr><td>Username:</td><td><input type=\"text\" name=\"txtusername\"></td><td rowspan=2><img src=\"images/password.png\"></td></tr>";
	echo "\n<tr><td>Password:</td><td><input type=\"password\" name=\"txtpassword\"></td></tr>";
	echo "\n<tr><td><input type=\"submit\" value=\"Login\" name=\"B1\"></td><td></td></tr>";
	echo "\n</table>";
	echo "\n</form>";
} else {

	switch(getVAR("action")){
		case "create_notice": 
			if (strtolower($username)=="student") {
			    echo send_notice("ERROR","Students may not create notices.");
			} else {
				
				echo "\n<form method=\"POST\" action=\"$PHP_SELF?action=create_notice_add&display_date=$display_date&username=$username&password=$password\">";
				echo "\n<table bgcolor=$table_heading_bgcolour width=100%>";
				echo "\n<tr><td colspan=2><h2>Create New Notice</h2></td></tr>";
				echo "\n<tr><td>Date(s):</td><td><table><tr><td>".getDateList(true,"")."</td><td>Note: CTRL + click to select multiple days.</td></tr></table></td></tr>";
				echo "\n<tr><td>Title:</td><td><input type=\"text\" size=40 name=\"txttitle\"></td></tr>";
				echo "\n<tr><td>Notice:</td><td><textarea rows=5 cols=40 name=\"txtnotice\"></textarea></td></tr>";
				echo "\n<tr><td>Author:</td><td><input type=\"text\" size=40 name=\"txtauthor\"></td></tr>";
				echo "\n<tr><td>Importance:</td><td>".getImportanceList()."</td></tr>";
				echo "\n<tr><td>Who should see this notice?:</td><td>".getUserList()."</td></tr>";
				echo "\n<tr><td>Typeface:</td><td>".getTypeFaceList()."</td></tr>";
				echo "\n<tr><td>Size:</td><td>".getFontSizeList()."<p>";
				echo "\n<input type=\"submit\" value=\"Create\" name=\"B1\"></td></tr>";
				echo "\n</table>";
				
				echo "\n</form>";
			}
			break;
		
		case "create_notice_add":
			
			if (!getVAR("lstdate")) {
            	echo send_notice("ERROR","No date was specified.");
            	continue;
            }
			
			$tempDate = getVAR("lstdate");
            $tempTitle = getVAR("txttitle");
			$tempNotice = getVAR("txtnotice");
			$tempAuthor = getVAR("txtauthor");
			$tempImportance = getVAR("lstimportance");
			$tempUsers = getVAR("lstusers");
			$tempTypeface = getVAR("lsttypeface");
			$tempFontsize = getVAR("lstfontsize");
			$tempDateAdded = date("Y-m-d H:i:s",time());
			
    		$msg=""; 
			foreach ($tempDate as $d) {
            	
            	$d = date("Y-m-d",$d);
				$result = do_mysql_query("INSERT INTO $notices_table (ID,visible,title,notice,display_date,date_added,importance,typeface,size,author) VALUES ('','1','$tempTitle','$tempNotice','$d','$tempDateAdded','$tempImportance','$tempTypeface','$tempFontsize','$tempAuthor')");
				$msg .= "Notice added.<br>";
				
				$last_id = mysql_insert_id();
				foreach ($tempUsers as $u) {
					$result = do_mysql_query("INSERT INTO $permissions_table (ID,notice_id,user_id) VALUES ('','$last_id','$u')");
					$msg .= "Permissions applied.<br>";
				}
			}
			
			echo send_notice("OK",$msg);
			
			break;
			
		case "add_event": 
			if (strtolower($username)=="student") {
			    echo send_notice("ERROR","Students may not create events.");
			} else {
				
				echo "\n<form method=\"POST\" action=\"$PHP_SELF?action=add_event_add&display_date=$display_date&username=$username&password=$password\">";
				echo "\n<table bgcolor=$table_heading_bgcolour width=100%>";
				echo "\n<tr><td colspan=2><h2>Create New Event</h2></td></tr>";
				echo "\n<tr><td>Date(s):</td><td>".getDateList(false,"")."</td></tr>";
				echo "\n<tr><td>Event:</td><td><input type=\"text\" size=40 name=\"txttitle\"></td></tr>";
				echo "\n<tr><td>Details:</td><td><textarea rows=5 cols=40 name=\"txtnotice\"></textarea><p>";
				echo "\n<input type=\"submit\" value=\"Create\" name=\"B1\"></td></tr>";
				echo "\n</table>";
				echo "\n</form>";
			}
			break;
		
		case "add_event_add":
			
			if (!getVAR("lstdate")) {
            	echo send_notice("ERROR","No date was specified.");
            	continue;
            }
			
			$tempDate = $getVAR("lstdate");
                       
            $tempTitle = getVAR("txttitle");
			$tempNotice = getVAR("txtnotice");
			$tempAuthor = getVAR("txtauthor");
			$tempDateAdded = date("Y-m-d H:i:s",time());
            
			foreach ($tempDate as $d) {
            	$d = date("Y-m-d",$d);
				$result = do_mysql_query("INSERT INTO $events_table (ID,visible,event,details,event_date,date_added,author) VALUES ('','1','$tempTitle','$tempNotice','$d','$tempDateAdded','$tempAuthor')");
				echo send_notice("OK","Event added.<br>");
			}
			
			break;
			
		case "del_notice": 
			if (strtolower($username)=="student") {
			    echo send_notice("ERROR","Students may not delete notices.");
			} else {
				$id = getVAR("id");
				if (getVAR("confirm") != "yes") {
					echo send_notice("QUESTION","Are you sure you want to delete notice number $id? <a href=\"$PHP_SELF?action=del_notice&display_date=$display_date&confirm=yes&id=$id&username=$username&password=$password\">[YES]</a> <a href=\"$PHP_SELF?display_date=$display_date&id=$id&username=$username&password=$password\">[NO]</a>");
				} else {
					$result = do_mysql_query("DELETE FROM $notices_table WHERE ID=$id");
					echo send_notice("EXCLAMATION","Notice deleted.");
				}
			}
			break;
			
		case "edit_notice": 
			if (strtolower($username)=="student") {
			    echo send_notice("ERROR","Students may not edit notices.");
			} else {
				$id = getVAR("id");
				if (getVAR("txttitle") == "") {
					
					$title = getValueFromID($notices_table,$id,"title");
					$notice = getValueFromID($notices_table,$id,"notice");
					$author = getValueFromID($notices_table,$id,"author");
					$display_date = strtotime(getValueFromID($notices_table,$id,"display_date"));
					$importance = getValueFromID($notices_table,$id,"importance");
					$typeface = getValueFromID($notices_table,$id,"typeface");
					$size = getValueFromID($notices_table,$id,"size");
					
					echo "\n<form method=\"POST\" action=\"$PHP_SELF?action=edit_notice&id=$id&display_date=$display_date&username=$username&password=$password\">";
					echo "\n<table bgcolor=$table_heading_bgcolour width=100%>";
					echo "\n<tr><td colspan=2><h2>Edit Notice</h2></td></tr>";
					echo "\n<tr><td>Date(s):</td><td>".getDateList(false,$display_date)."</td></tr>";
					echo "\n<tr><td>Title:</td><td><input type=\"text\" size=40 value=\"$title\" name=\"txttitle\"></td></tr>";
					echo "\n<tr><td>Notice:</td><td><textarea rows=5 cols=40 name=\"txtnotice\">$notice</textarea></td></tr>";
					echo "\n<tr><td>Author:</td><td><input type=\"text\" size=40 value=\"$author\" name=\"txtauthor\"></td></tr>";
					echo "\n<tr><td>Importance:</td><td>".getImportanceList($importance)."</td></tr>";
					echo "\n<tr><td>Who should see this notice?:</td><td>".getEditUserList($id)."</td></tr>";
					echo "\n<tr><td>Typeface:</td><td>".getTypeFaceList($typeface)."</td></tr>";
					echo "\n<tr><td>Size:</td><td>".getFontSizeList($size)."<p>";
					echo "\n<input type=\"submit\" value=\"Save Changes\" name=\"B1\"></td></tr>";
					echo "\n</table>";
					echo "\n</form>";
					
				} else {
					$tempDate = getVAR("lstdate");
		            $tempTitle = getVAR("txttitle");
					$tempNotice = getVAR("txtnotice");
					$tempAuthor = getVAR("txtauthor");
					$tempImportance = getVAR("lstimportance");
					$tempUsers = getVAR("lstusers");
					$tempTypeface = getVAR("lsttypeface");
					$tempFontsize = getVAR("lstfontsize");
					$tempDateAdded = date("Y-m-d H:i:s",time());
					
		     		$msg = "";
					foreach ($tempDate as $d) {
		            	
		            	$d = date("Y-m-d",$d);
						
						$query = "UPDATE $notices_table SET title='$tempTitle',notice='$tempNotice', display_date='$d', author='$tempAuthor', importance='$tempImportance', typeface='$tempTypeface', size='$tempFontsize' WHERE ID=$id";
	            		$result = do_mysql_query($query);
												
						$msg .= "Notice edited.<br>";
						
						$result = do_mysql_query("DELETE FROM $permissions_table WHERE notice_id='$id'");
						
						
						foreach ($tempUsers as $u) {
							$result = do_mysql_query("INSERT INTO $permissions_table (ID,notice_id,user_id) VALUES ('','$id','$u')");
							$msg .= "Permissions applied.<br>";
						}
					}
					echo send_notice("OK",$msg);
				}
			}
			break;
			
		case "del_event": 
			if (strtolower($username)=="student") {
			    echo send_notice("ERROR","Students may not delete events.");
			} else {
				$id = getVAR("id");
				if (getVAR("confirm") != "yes") {
					echo send_notice("QUESTION","Are you sure you want to delete event number $id? <a href=\"$PHP_SELF?action=del_event&display_date=$display_date&confirm=yes&id=$id&username=$username&password=$password\">[YES]</a> <a href=\"$PHP_SELF?display_date=$display_date&id=$id&username=$username&password=$password\">[NO]</a>");
				} else {
					$result = do_mysql_query("DELETE FROM $events_table WHERE ID=$id");
					echo send_notice("EXCLAMATION","Event deleted.");
				}
			}
			break;
			
		case "edit_event": 
			if (strtolower($username)=="student") {
			    echo send_notice("ERROR","Students may not edit events.");
			} else {
				$id = getVAR("id");
				if (getVAR("txttitle") == "") {
					
					$event = getValueFromID($events_table,$id,"event");
					$details = getValueFromID($events_table,$id,"details");
					$date = strtotime(getValueFromID($events_table,$id,"event_date"));
					
					echo "\n<form method=\"POST\" action=\"$PHP_SELF?action=edit_event&display_date=$display_date&id=$id&username=$username&password=$password\">";
					echo "\n<table bgcolor=$table_heading_bgcolour width=100%>";
					echo "\n<tr><td colspan=2><h2>Edit Event</h2></td></tr>";
					echo "\n<tr><td>Date(s):</td><td>".getDateList(false,$date)."</td></tr>";
					echo "\n<tr><td>Event:</td><td><input type=\"text\" size=40 value=\"$event\" name=\"txttitle\"></td></tr>";
					echo "\n<tr><td>Details:</td><td><textarea rows=5 cols=40 name=\"txtnotice\">$details</textarea><p>";
					echo "\n<input type=\"submit\" value=\"Save Changes\" name=\"B1\"></td></tr>";
					echo "\n</table>";
					echo "\n</form>";
				} else {
					$tempDate = getVAR("lstdate");
		            $tempTitle = getVAR("txttitle");
					$tempNotice = getVAR("txtnotice");
		            
		            foreach ($tempDate as $d) {
            			$d = date("Y-m-d",$d);
						$query = "UPDATE $events_table SET event='$tempTitle',details='$tempNotice', event_date='$d' WHERE ID=$id";
	            		$result = do_mysql_query($query);
					
						echo send_notice("OK","Event edited.");
		            }

				}
			}
			break;
		
		case "manage_users":
			if (authenticate($username,$password)) {
				echo "<h2>Edit Users</h2>";
				
				if (getVAR("txtUsername")) {
                    if (getVAR("txtPwd1") != getVAR("txtPwd2")) {
                        echo "Passwords don't match.<br>";
                    } else {
						$tempUsername = getVAR("txtUsername");
	                    $tempRealname = getVAR("txtRealname");
						$tempPassword = md5(getVAR("txtPwd1"));
	                    
	                    $result = do_mysql_query("INSERT INTO $users_table (ID,username,realname,password) VALUES ('','$tempUsername','$tempRealname','$tempPassword')");
					}
                } 

                if (getVAR("del_id")) {
                	$del_id = getVAR("del_id");
                    if (getVAR("do") == "true") {
                        $result = do_mysql_query("DELETE FROM $users_table WHERE ID=$del_id");
                    } else {
                        echo "<b><font color=red>Are you sure you want to delete this user?</font> <a href=\"$PHP_SELF?action=manage_users&username=$username&password=$password&del_id=$del_id&do=true\">YES</a></b>";
                    } 
                } 

                if (getVAR("txtEditUsername")) {
                    if (getVAR("txtPwd1") != getVAR("txtPwd2")) {
                        echo "Passwords don't match.<br>";
                    } else {
						$id = getVAR("id");
	                    $tempUsername = getVAR("txtEditUsername");
	                    $tempRealname = getVAR("txtEditRealname");
						$tempPassword = md5(getVAR("txtPwd1"));
						
						if (getVAR("txtPwd1"=="")) {
						    $result = do_mysql_query("UPDATE $users_table SET username='$tempUsername', realname='$tempRealname' WHERE ID=$id");
						} else {
	                    	$result = do_mysql_query("UPDATE $users_table SET username='$tempUsername', realname='$tempRealname', password='$tempPassword' WHERE ID=$id");
						}
					}
                } 

                $result = do_mysql_query("SELECT * FROM $users_table ORDER BY username");
				
				$edit_id = getVAR("edit_id");
                $dont_show_add = false;
                echo "\n<form method=\"POST\" action=\"$PHP_SELF?action=manage_users&username=$username&password=$password\">";
                echo "\n<table><tr bgcolor=\"$table_heading_bgcolour\"><td align=center><b>Username</b></td><td align=center><b>Real Name</b></td><td align=center><b>Password</b></td><td align=center><b>Operations</b></td></tr>";
                while ($row = mysql_fetch_array($result)) {
                    
					if ($edit_id == $row["ID"]) {
				        echo "\n<tr bgcolor=\"$table_bgcolour\"><td><input type=\"text\" name=\"txtEditUsername\" value=\"" . $row["username"] . "\" size=\"20\"></td><td><input type=\"text\" name=\"txtEditRealname\" value=\"" . $row["realname"] . "\" size=\"20\"></td><td><table><tr><td>Password:</td><td><input type=\"password\" name=\"txtPwd1\" size=\"20\"></td></tr><tr><td>Confirm:</td><td><input type=\"password\" name=\"txtPwd2\" size=\"20\"></td></tr></table></td><td><input type=\"submit\" value=\"Save Changes\" name=\"B1\"></td></tr>";
				        echo "\n<input type=hidden name=\"id\" value=\"" . $row["ID"] . "\">";
				        $dont_show_add = true;
				    } else {
				        echo "\n<tr bgcolor=\"$table_bgcolour\"><td>" . $row["username"] . "</td><td>" . $row["realname"] . "</td><td>" . $row["password"] . "</td><td><a href=\"$PHP_SELF?action=manage_users&username=$username&password=$password&edit_id=" . $row["ID"] . "\">[Edit]</a> <a href=\"$PHP_SELF?action=manage_users&username=$username&password=$password&del_id=" . $row["ID"] . "\">[Delete]</a></td></tr>";
				    } 
					
                } 
                if ($dont_show_add == false) {
                    echo "\n<tr bgcolor=\"$table_bgcolour\"><td><input type=\"text\" name=\"txtUsername\" size=\"20\"></td><td><input type=\"text\" name=\"txtRealname\" size=\"20\"></td><td><table><tr><td>Password:</td><td><input type=\"password\" name=\"txtPwd1\" size=\"20\"></td></tr><tr><td>Confirm:</td><td><input type=\"password\" name=\"txtPwd2\" size=\"20\"></td></tr></table></td><td><input type=\"submit\" value=\"Add New User\" name=\"B1\"></td></tr>";
                } 
                echo "\n</table></form>";
			}
			break;

	} // switch

	
	echo "\n<h2>Notices for ".date("l \\t\h\e jS \o\f F",$display_date)."</h2>";
	
	$query = "SELECT * FROM $notices_table WHERE display_date='".date("Y-m-d",$display_date)."' ORDER BY date_added";
	//echo $query."<br>";
	$result = do_mysql_query($query);
	while ($row = mysql_fetch_array($result)) {
		if (haveaccess($username,$row["ID"]) OR ($username == "staff" and getVAR("edit") == "on")) {
			echo "\n<font ";
			
			if ($username == "staff" AND haveaccess("student",$row["ID"])) {
				echo "color=\"blue\" ";	
			} else {
				if ($row["importance"] == "2") {
					echo "color=\"red\" ";			
				} //if
			}
			
			if ($row["typeface"] != "Default") {
				echo "face=\"".$row["typeface"]."\" ";			
			} //if
			echo "size=".$row["size"];
			echo ">";
			echo "<b>".$row["title"]."</b> ";
			if ($username == "staff" and getVAR("edit") == "on") { //add edit/del buttons
				$id = $row["ID"];
				echo "\n<a href=\"$PHP_SELF?action=edit_notice&display_date=$display_date&id=$id&username=$username&password=$password\"><img border=0 align=right src=\"images/edit.gif\"> </a> ";
				echo "\n<a href=\"$PHP_SELF?action=del_notice&display_date=$display_date&id=$id&username=$username&password=$password\"><img border=0 align=right src=\"images/delete.gif\"></a>";
			}
			echo "\n<br>".formattext($row["notice"]);
			echo "\n</font>";
			echo "\n<br><small><i>Created by ".$row["author"]." on ".date("l \\t\h\e jS \o\f F \a\\t g:ia",strtotime($row["date_added"]))."</i></small><p>";
		} //if
	} //while
	
	
}

echo "\n</td><td width=25% align=right valign=top>";

if (authenticate($username,$password)) {

	echo "\n<table width=100%>";
	echo "\n<tr bgcolor=$table_heading_bgcolour><td align=center><b>Event Calendar</b></td></tr>";
	
	$query = "SELECT * FROM $events_table WHERE event_date >= '".date("Y-m-d",time())."' ORDER BY event_date";
	//echo $query."<br>";
	$result = do_mysql_query($query);
	while ($row = mysql_fetch_array($result)) {
		echo "\n<tr bgcolor=$table_bgcolour><td><font ";
		if ($row["event_date"]==date("Y-m-d",time())) {
			echo "color=red";
		} else {
			echo "color=black";
		}
		echo ">";
		
		if ($username == "staff" and getVAR("edit") == "on") { //add edit/del buttons
			$id = $row["ID"];
			echo "\n<a href=\"$PHP_SELF?action=edit_event&display_date=$display_date&id=$id&username=$username&password=$password\"><img border=0 align=right src=\"images/edit.gif\"> </a> ";
			echo "\n<a href=\"$PHP_SELF?action=del_event&display_date=$display_date&id=$id&username=$username&password=$password\"><img border=0 align=right src=\"images/delete.gif\"></a>";
		}
		
		echo "\n<small><b>".strftime("%d/%m/%Y",strtotime($row["event_date"]))." - ".$row["event"]."</b><br>".formattext($row["details"])."</small>";
		
		
		
		echo "\n</font></td></tr>";
		
	}
	
	if (!mysql_num_rows($result)) {
		echo "\n<tr bgcolor=$table_bgcolour><td>No events could be found.<br>Click <i>Add Event</i> to create an event.</td></tr>";
	}
	
	echo "\n</table>";
	
}

echo "\n</td></tr>";

echo "\n</table>";

if (authenticate($username,$password)) {
	sendNavBar(array("previous","logout","home","absentees","create_notice","add_event","edit","next"),$username,$password,$display_date);
}

if ($username=="admin") {
    sendNavBar(array("manage_users"),$username,$password,$display_date);
}

if (authenticate($username,$password)) {
	SendPageFooter();
} else {
	echo "\n</body></html>";
}

?>