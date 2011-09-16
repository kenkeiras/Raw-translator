<?php
/* This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details. */ 
    require('config.php');
    require('operations.php');

    echo "<html>Connecting... ";
    $dbConn = mysql_connect($dbHost, $dbUser, $dbPass);
    mysql_select_db($dbName, $dbConn ) or 
                die("<font color='red'>Database error: ".mysql_error()."</font>");

    echo "[<font color='green'>OK</font>]<br />";
    $tables = getTables();

    foreach( $tables as $table ){
        echo "Checking $table ";
        $t = mysql_real_escape_string( $dbTablePrefix.$table );
        $q = "show tables like '$t';";
        $r = mysql_query( $q ) or die( "[<font color='red'>Error: ". mysql_error()."</font>]" );
        if (mysql_fetch_array($r)){
            echo "[<font color='red'>Table exists</font>]";
        }
        else{
            $q = "create table $t (origin varchar( $limitLen ), result varchar( $limitLen ), PRIMARY KEY (origin, result));";
            mysql_query($q) or die( "[<font color='red'>Error: ". mysql_error()."</font]>" );
            echo " [<font color='green'>Done</font>]";
        }
    
        echo "<br />";
    }
    echo "<br /><hr />Done</html>"
?>