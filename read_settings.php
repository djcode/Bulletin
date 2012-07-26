<?php

/**
 * schoolbulletin: read_settings.php
 * 
 * Reads settings from database.
 * 
 * @author Chris Goerner <cgoerner@users.sourceforge.net>
 * @version $Id: read_settings.php,v 1.4 2005/04/10 03:36:28 cgoerner Exp $
 * @version $Revision: 1.4 $
 * 
 **/

//PAGE SETTINGS
$page_title = getSetting("page_title","School Bulletin");	//String used as the page title
$page_heading = getSetting("page_heading","School Bulletin");//String used as the page heading
$page_bgcolour = getSetting("page_bgcolour","#FAFAFA");									//The background colour of all pages
$table_heading_bgcolour = getSetting("table_heading_bgcolour","#D4DCE2");					//Table heading background colour
$table_bgcolour = getSetting("table_bgcolour","#EEEEEE");							//Table background colour

//FOOTER SETTINGS
$show_server_software = getSetting("show_server_software",false);
$show_server_name = getSetting("show_server_name",false);
														

														
?>