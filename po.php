<?php
/* Gettext message catalogue */
/* This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details. */
     require_once 'catalog.php';

    /* Reads a string */
    function getString($f, &$i, $l ){
        $s = Array();
        $done = false;
        $comment = -1;
        while(true){ 
            $scaped = false;
            /* Trim initial whitespaces/whatsoever */
            do{
              $c = $f[$i];
              $i++;
            }while((strpos(" \n\t\r", $c) !== false )and($i < $l));

            /* Check if in string bounds */
            if( $i >= $l ){
                break;
            }
            
            /* Read initial token */
            switch($c){
                case '#':
                    if ($comment == -1){
                        $comment = $i - 1;
                    }
                    $com = getUntil($f, $i, "\n", $l);
                    break;

                case '"': // Double quote
                case "'": // Single quote
                    $quote_type = $c;
                    $comment = -1;
                    break;

                default:
                    $done = true;
                    $i--;
                    break;
            }
            if($done){
                break;
            }
            elseif($comment != -1){
                continue;
            }
            // Reads a scapable string delimited by $quote_type
            $a = getUntil( $f, $i, $quote_type, $l, true );
            $s[] = $a;
        }
        if($comment != -1){
            $i = $comment;
        }
        return $s;
    }

    /* Reads the next token from a PO file */
    function getNextToken($f, &$i, $l){
        $i--;
        do{
            $i++;
            $c = $f[$i];
        }while((strpos(" \n\t\r", $c) !== false) and ( ($i + 1 ) < $l ));

        if( $i + 1 >= $l ){
            $i++;
            return Array( Catalog::void, '');
        }

        if( $c == '#' ){
            return Array(Catalog::comment, getUntil($f, $i, "\n", $l));
        }
        $s = getUntil($f, $i, ' ', $l);

        switch(strtolower($s)){
            case 'msgid':
                return Array(Catalog::msgId, getString($f, $i, $l));

            case 'msgstr':
                return Array(Catalog::msgStr, getString($f, $i, $l));

            default:
                die('Error parsing PO file, unknown token '.$s);
        }
        
    }

    /* Tokenizes a PO file */
    function parsePoFile( $f, $fname ){
        $t = Array();
        $s = fread($f, filesize($fname));
        $l = strlen($s);
        $i = 0;
        while($i < $l){
            $t[] = getNextToken($s, $i, $l);
        }
        return $t;
    }
?>