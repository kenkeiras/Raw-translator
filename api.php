<?php
/* Raw translator API */
/* This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details. */ 

    /* Check needed parameter */
    if (!isset($_REQUEST['op'])){
        require 'api_doc.html';
        exit();
    }

    require 'operations.php'; // Common operations
    require 'config.php'; // Database configuration

    $op = $_REQUEST['op'];

    /* Check if its a valid operation */
    switch($op){
        case 'get_translations':
        case 'translate_string':
        case 'add_to_database':
        case 'translate':
            break;
        default:
            require 'api_doc.html';
            exit();
    }

    /* Just show translation options */
    if ($op == 'get_translations'){
        echo json_encode($translations);
    }
    /* Translate something */
    else{
        /* So we open a connection to the database */
        $dbConn = mysql_connect($dbHost, $dbUser, $dbPass);
        mysql_select_db($dbName, $dbConn) or
            die("Database error: ".mysql_error());

        isset($_REQUEST['trans']) or die('Translation not set');

        $trans = $_REQUEST['trans'];


        /* Check if it is a valid translation */
        checkTranslation($trans) or die('Translation not available');

        /* Translate a string */
        if ($op == 'translate_string'){
            $orig = $_REQUEST['str'];

            /* Unescape and trim it */
            $o = unescape_all($orig);
            $orig = trim($o);

            /* Request the translation */
            $v = reqString($dbTablePrefix.$trans, $orig, $dbConn);

            /* Adapt it to fit the input one */
            if(strlen($v) > 0){
                echo toString(firstChars($o, " \r\n\t").
                              trim($v).
                              lastChars($o, " \r\n\t")
                              );
            }
        }
        /* Operate over a file */
        else{
            /* Check file */
            if (! isset($_FILES['file']['tmp_name'])){
                die('File not received');
            }

            $fname = $_FILES['file']['tmp_name'];

            require 'catalog.php';

            /* Parse the file */
            $cat = new Catalog($fname);

            /* Just add it to the database */
            if ($op == 'add_to_database'){
                addPairs($cat, $dbConn, $trans);
            }
            /* Translate it */
            else{
                /* And add its contents to database, if we are allowed ;) */
                if(isset($_REQUEST['add'])){
                    addPairs($cat, $dbConn, $trans);
                }

                /* Fill the file with translations */
                fillTranslation($cat, $dbConn, $trans);

                /* PO file output */
                header("Content-type: text/x-gettext-translation");

                /* Print the output */
                $cat->printPo();
            }
        }

        mysql_close();            
    }
?>