<?php
/* Database data */
$dbHost = "127.0.0.1"; // Server host
$dbUser = "rtrans";   // User
$dbPass = "rtrans";  // Password
$dbName = "rtrans"; // Database name
$dbTablePrefix = "rtrans_"; // Table name prefix

$limitLen = 256; // Max. string length

// 'Translation code' => 'Translation type'
$translations = Array(
    'en2es' => 'English to Spanish',
    'en2gal' => 'English to Galician',
);

// Non autonomous characters, doesn't make a translation itself
$non_autonomous_characters = '\\/{}[]¡!"#$%&/()=¿?^*+`çÇ\'-_.:,;><ºª|@·¬';
$symbols = '/['.preg_quote($non_autonomous_characters, '/').']/';
?>