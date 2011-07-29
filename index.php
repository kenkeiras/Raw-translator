<?php
    session_start();
?>
<!DOCTYPE html>
<html><head>
<meta charset="utf-8" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<title>Raw translator</title>
</head>
<body>
<div id="banner">Raw translator</div>
<br /><br />
<form action="rt.php" method="post" enctype="multipart/form-data" >
    <label for="fileIn">File:</label> <input id="fileIn" name="file" type="file" required="required" />
    <br />
    <select name="trans" >
    <?php
        require 'operations.php';
        $t = getTranslationOptions();
        foreach( $t as $key => $value ){
            echo "<option value='$key'> $value</option>";
        }
    ?>
    </select>
    <input type="checkbox" checked=true name="add" id="addfile" value="y" /><label for="addFile" >Add to string repository</label>
    <br /><br />
    <input type="submit" value="Send">
</form>
<div id="footer"><a href="https://github.com/kenkeiras/Raw-translator"><img src="img/github-badge.png" alt="Source code repository"/></a> | <a href="tc.html">Service terms and conditions</a> | <a href="api_doc.html">API documentation</a></div>
</body>
</html>
