<?php
/* From website query handler */
/* This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details. */ 

    if (!(isset($_FILES['file']['tmp_name']) and isset($_POST['trans']))){
        header("Location: .");
    }
    
    
    $trans = $_POST['trans'];
    $fname = $_FILES['file']['tmp_name'];

    require 'operations.php';
    checkTranslation($trans) or die('Translation not available');

    require 'catalog.php';
    require 'config.php';

    $cat = new Catalog($fname);

    $dbConn = mysql_connect($dbHost, $dbUser, $dbPass);
    mysql_select_db($dbName, $dbConn ) or die("Database error: ".mysql_error());

    if(isset($_POST['add'])){
        addPairs( $cat, $dbConn, $trans );
    }

    fillTranslation( $cat, $dbConn, $trans );
    mysql_close( $dbConn );

    header("Content-type: text/x-gettext-translation");
    $cat->printPo();
    
?>