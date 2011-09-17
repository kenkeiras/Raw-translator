<?php
/* Common operations */
/* This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details. */ 

    /* Returns the first chars from $s, in $chars */
    function firstChars($s, $chars){
        $l = strlen($s);
        $r = "";
        for($i = 0; $i < $l; $i++){
            $c = $s[$i];
            if(strpos($chars, $c)!== false){
                $r .= $c;
            }
            else{
                break;
            }
        }
        return $r;
    }

    /* Returns the last chars from $s, in $chars */
    function lastChars($s, $chars){
        $r = "";
        for($i = strlen($s) - 1; $i >= 0; $i--){
            $c = $s[$i];
            if(strpos($chars, $c)!== false){
                $r = $c.$r;
            }
            else{
                break;
            }
        }
        return $r;
    }

    /* Returns unescaped verions of charactes \n \t \r and \\ */
    function unescape($c){
        switch( $c ){
            case "\\":
                $c = "\\";
                break;
            case "n":
                $c = "\n";
                break;
            case "r":
                $c = "\r";
                break;
            case "t":
                $c = "\t";
                break;
            default:
                $c = "\\$c";
        }
        return $c;
    }

    /* Unescapes string characters */
    function unescape_all($f){
        $c = $s = '';
        $scaped = false;
        $i = 0;
        $l = strlen($f);
        do{
            $scaped = (!$scaped and $c == '\\');
            if(!$scaped){
                $s .= $c;
            }
            $c = $f[$i];
            if( $scaped ){
                $s .= unescape($c);
                $c = '';
            }
            $i++;
        }while($i < $l);
        if(!$scaped){
            $s .= $c;
        }
        return $s;
    }

    /* Reads a file until a certain character */
    function getUntil($f, &$i,$u, $l, $check_scaped = false ){
        $c = '';
        $s = '';
        $scaped = false;
        do{
            $scaped = (!$scaped and $check_scaped and $c == '\\');
            if(!$scaped){
                $s .= $c;
            }
            $c = $f[$i];
            if( $scaped ){
                $s .= unescape($c);
                $c = '';
            }
            $i++;
        }while((($c != $u )or ($scaped)) and ($i < $l));
        return $s;
    }

    /* Scapes non-printable charactes */
    function toString($s){
        $r = "";
        $l = strlen($s);
        for($i = 0; $i < $l; $i++){
            $c = $s[$i];
            switch( $c ){
                case "\n":
                    $x = "\\n";
                    break;
                case "\r":
                    $x = "\\r";
                    break;
                case "\t":
                    $x = "\\t";
                    break;
                case "\"":
                    $x = "\\\"";
                    break;
                default:
                    $x = $c;
            }
            $r .= $x;
        }
        return $r;
    }

    /* Checks if a string contains only printable characters */
    function useful_string($s, $symbols){
        $a = preg_split('/[[:cntrl:]|�]/', $s);
        if (count($a) != 1){
            return false;
        }

        $clean = preg_replace($symbols, '', $s);
        return strlen($clean) > 0;
    }

    /* Add origin - result pairs to database */
    function addPairs( $po, $dbConn, $table ){
        require 'config.php';
        while( $p = ($po->getPair()) ){
            $orig   = trim(implode('', $p[0][1]));
            $result = trim(implode('', $p[1][1]));
            if (useful_string($orig, $symbols) && useful_string($result, $symbols)){

                $ol = strlen($orig); 
                $rl = strlen($result);

                if(($ol > 0) and ($rl > 0) and ($ol < $limitLen) and( $rl < $limitLen)){
                    $insertQuery = "INSERT IGNORE into ".$dbTablePrefix.$table." values( '".
                            mysql_real_escape_string($orig)."', '".
                            mysql_real_escape_string($result)."');";

                    mysql_query($insertQuery, $dbConn) or die( "Error: ". mysql_error() );
                }
            }
        }
        $po->resetPairIt();
    }

    /* Requests the translation for the $orig string, from $tableName */
    function reqString($tableName, $orig, $dbConn){
        $selectQuery = "SELECT result from $tableName where ".
                        "origin='".mysql_real_escape_string($orig)."';";

        $r = mysql_query($selectQuery, $dbConn) or
            die( "Error: ". mysql_error() );

        $v = mysql_fetch_array($r);
        return $v[0];
    }



    /* Fills results with translations */
    function fillTranslation( $po, $dbConn, $table ){
        require 'config.php';
        while($p = ($po->getPair())){
            $orig   = trim(implode('', $p[0][1]));
            $result = trim(implode('', $p[1][1]));

            $ol = strlen($orig); 
            $rl = strlen($result);
            if(($ol > 0) and ($rl == 0) and ($ol < $limitLen)){
                $v = reqString($dbTablePrefix.$table, $orig, $dbConn);

                if($v){
                    echo "$orig -> $result\n";
                    $po->setResult($v);
                }
            }
        }
        $po->resetPairIt();
    }

    /* Convers a binary string into a number */
    function rawInt($s, $littleEndian = false){
        $n = 0;
        $l = strlen($s);
        if (!$littleEndian){
            for($i = 0; $i < $l; $i++){
                $c = $s[$i];
                $n *= 256;
                $n += ord($c);
            }
        }
        else{
            for($i = $l-1; $i >= 0; $i--){
                $c = $s[$i];
                $n *= 256;
                $n += ord($c);
            }
        }
        return $n;
    }

    /* Returns only the string $s part before $delim */
    function cut($s, $delim){
        $r = "";
        $l = strlen($s);
        for($i = 0;$i < $l;$i++){
            $c = $s[$i];
            if($c == $delim){
                break;
            }
            $r .= $c;
        }
        return $r;
    }

    /* Returns translation options */
    function getTranslationOptions(){
        require 'config.php';
        return $translations;
    }

    /* Checks if $trans is an available translation */
    function checkTranslation( $trans ){
        require 'config.php';
        return isset($translations[$trans]);
    }

    /* Return the table sufixes */
    function getTables(){
        require 'config.php';
        return array_keys($translations);
    }
?>