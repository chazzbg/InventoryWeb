<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Ingress Inventory by Chazz</title>
		<link href='http://fonts.googleapis.com/css?family=Coda:400,700' rel='stylesheet' type='text/css' />
		<link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
		<link href="css/bootstrap-theme.css " rel='stylesheet' type='text/css' />

    </head>
    <body>
    <div class="container">
	    <h1 class="title text-center"><a href="index.php">Ingress Inventory</a></h1>


		
<?php
require 'config.php';
require 'functions.php';
$errors = array();

if(isSubmit('SACSID')){
	if(!isSACSID(getValue('SACSID'))){
		$errors[] = 'Invalid authentication!';
	}
	
	
	if(!count($errors)){
	
		$res = mysql_query('SELECT * FROM regcodes WHERE sacsid = \''.getValue('SACSID').'\'');
		if(mysql_num_rows($res)==0){
		
			$chars = 'abcdefghijkmnpqrstuvwxyz123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
			$code ='';

			do{ 
				srand ();
				for($i=0; $i<7;$i++){


					$code .= $chars[rand (0,(strlen($chars)-1))];
				}
			} while (mysql_num_rows(mysql_query('SELECT * FROM regcodes WHERE code = \''.$code.'\''))!=0);
			
			mysql_query('INSERT INTO regcodes (code,sacsid,date_add) VALUES (\''.$code.'\', \''.  getValue('SACSID').'\',NOW())');
		} else {
			$regcode = mysql_fetch_assoc($res);
			$code = $regcode['code'];
		}
	}
	
	if(count($errors)>0){
		showErrors($errors);
	} else {
	
		
?>

		<h3 class="subtitle text-center">Your authentication code is:</h3>
		<h2 class="subtitle text-center"><?php echo $code?></h2>
<?php }
}?>


    </div>
	</body>
</html>
