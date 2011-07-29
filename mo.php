<?php
/* Gnu message catalog */
/* This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details. */ 

    /* Checks the 0x950412de magic number */
    function isMoFile($f){
        $s = fgets($f, 5);
        fseek($f, 0, SEEK_SET);

        $little = ($s == "\xde\x12\x04\x95"); // Little endian
        $big = ($s == "\x95\x04\x12\xde"); // Big endian
        return $little or $big;
    }

    /* Tokenizes a MO file */
    function parseMoFile($f, $fname){
        require_once 'operations.php';
        require_once 'catalog.php';

        /* Headers */
        $magic = fgets($f, 5);
        $little = ($magic == "\xde\x12\x04\x95"); // Little endian

        $revision = rawInt(fgets($f, 5), $little); // Revision
        $num  = rawInt(fgets($f, 5), $little); // String number
        $osto = rawInt(fgets($f, 5), $little); // Original string table offset
        $tsto = rawInt(fgets($f, 5), $little); // Translation string table offset
        $hst  = rawInt(fgets($f, 5), $little); // Hashing table size
        $hso  = rawInt(fgets($f, 5), $little); // Hashing table offset


        $t = Array( Array(Catalog::comment, "Revision: $revision"),
                    Array(Catalog::comment, "String number: $num"),
                    );

        for($i = 0; $i < $num; $i++){
            fseek($f, $osto + ( $i * 8 ), SEEK_SET);
            $ol  = rawInt(fgets($f, 5), $little); // Original string length
            $of  = rawInt(fgets($f, 5), $little); // Original string offset

            fseek($f, $tsto + ( $i * 8 ), SEEK_SET);
            $tl  = rawInt(fgets($f, 5), $little); // Translation string length
            $tf  = rawInt(fgets($f, 5), $little); // Translation string offset

            fseek($f, $of, SEEK_SET);
            $orig = cut(fgets($f, $ol+1),"\0");
            $t[] = Array( Catalog::msgId, Array($orig));
            
            fseek($f, $tf, SEEK_SET);
            $trans = cut(fgets($f, $tl+1),"\0");
            $t[] = Array( Catalog::msgStr, Array($trans));
        }

        return $t;        
    }
?>