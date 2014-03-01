<?php
$dbhost='localhost'; //Обикновенно е localhost
$dbusername='ingress'; 
$dbuserpass='inventory';
$db='inventory';
//////////////
// ime na grupata na moderatorite na saia
/////////////

mysql_connect($dbhost, $dbusername, $dbuserpass) or die("MySQL Грешка! Моля уведомете администратора!");
mysql_select_db($db) or die("MySQL Грешка! Моля уведомете администратора!") ;
mysql_set_charset('utf8');

function secure($text) {

    if ( (int)$text )
        return $text;
    else
        {
        	
            $text = strip_tags($text);
            $text = addslashes($text);
            $text = htmlspecialchars($text, ENT_NOQUOTES);
            return $text;
        }
} 

$_POST = array_map("secure", $_POST);
$_GET = array_map("secure",$_GET);
$_COOKIE = array_map("secure",$_COOKIE);

date_default_timezone_set('Europe/Athens');

ini_set('display_errors', 'on');
error_reporting(E_ALL);

define('DEV_MODE', true);
?>