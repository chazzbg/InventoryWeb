
<?php
require 'datafetch.php';

if($player_profile == null OR empty($player_profile) OR count($player_profile) ==0){


showErrors(array('No badges information, force sync to get it!'));

} else {
	require('render_profile.php');

 }