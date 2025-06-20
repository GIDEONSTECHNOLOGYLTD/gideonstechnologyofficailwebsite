<?php
header('Content-Type: text/plain');
echo 'PHP is working!';
echo '\n\nServer Software: ' . $_SERVER['SERVER_SOFTWARE'];
echo '\nDocument Root: ' . $_SERVER['DOCUMENT_ROOT'];
echo '\nScript Filename: ' . $_SERVER['SCRIPT_FILENAME'];
echo '\nRequest URI: ' . $_SERVER['REQUEST_URI'];
?>
