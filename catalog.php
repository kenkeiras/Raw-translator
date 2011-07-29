<?php
/* Catalog class */
/* This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details. */ 
    require_once 'operations.php';
    require_once 'mo.php';
    require_once 'po.php';



    /* Catalog file*/
    class Catalog{

        /* Internal array */
        public $core = Array();
        private $_pairMark = 0;
        private $_resultMark = null;
        private $_changeNum = 0;

        /* Token types */
        const void = 0;
        const comment = 1;
        const msgId = 2;
        const msgStr = 3;

        /* Debugging */
        public function showInners(){
            var_dump($this->core);
            echo count($this->core)." elements\n";
        }


        /* Resets the getPair() iterator */
        public function resetPairIt(){
            $this->_pairMark = 0;
            $this->_resultMark = null;
        }

        /* Set's the result for the current pair */
        public function setResult( $r ){
            if ($this->_resultMark !== null){
                $this->core[$this->_resultMark][1] = Array($r);
                $this->_changeNum++;
            }
        }

        /* Iterates over msgId/msgStr pairs */
        public function getPair(){
            $id = null;
            $str = null;
            while(($id === null) or ($str === null)){
                if($this->_pairMark >= count($this->core)){
                    return false;
                }

                $e = $this->core[$this->_pairMark];
                $type = $e[ 0 ];
                switch( $type ){
                    case self::msgId :
                        if( $id !== null ){
                            die("Malformed file ( two consecutive msgId )");
                        }
                        $id = $e;
                        break;

                    case self::msgStr :
                        if( $str !== null ){
                            die("Malformed file ( two consecutive msgStr )");
                        }
                        $this->_resultMark = $this->_pairMark;
                        $str = $e;
                        break;
                }
                $this->_pairMark++;
            }
            if (($id === null) or ($str === null)){
                return false;
            }
            else{
                return Array($id, $str);
            }
        }

        /* Prints as a PO file */
        /* TODO: Optimize ? */
        public function printPo(){
            echo '# '.$this->_changeNum." change(s) made\n";
            $id = "";
            foreach($this->core as $e){
                $type = $e[0];
                $content = $e[1];
                switch( $type ){
                    case self::comment :
                        echo "$content\n";
                        break;

                    case self::msgId :
                        echo "msgid ";
                        $id = implode('', $content);
                        foreach($content as $line){
                            echo "\"".toString($line)."\"\n";
                        }
                        break;

                    case self::msgStr :
                        echo "msgstr ";
                        $len = strlen(implode('',$content));
                        if($len > 0){
                            $i = 0;
                            $last = count($content) - 1;
                            foreach($content as $line){
                                if($i == 0){
                                    $line = firstChars($id, " \r\n\t").ltrim($line);
                                }
                                if($i == $last){
                                    $line = rtrim($line).lastChars($id, " \r\n\t");
                                }
                                echo "\"".toString($line)."\"\n";
                                $i++;
                            }
                        }
                        else{
                            echo '""'."\n";
                        }
                        echo "\n";
                    
                }
            }
        }

        function __construct($fname){
            $f = fopen($fname, "rb");
            if (isMoFile($f)){
                $this->core = parseMoFile($f, $fname);
            }
            else{
                $this->core = parsePoFile($f, $fname);
            }
            fclose($f);
        }
    }
?>