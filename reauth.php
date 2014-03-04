<?php
//$errors = array();
//$api = new API();
//if (isSubmit('send')) {
//	if (getValue('code') == '') {
//		$errors[] = 'Authentication code is required!';
//	} else if (!isRegCode(getValue('code'))) {
//		$errors[] = 'Authentication code is invalid!';
//	} else {
//		$code_res = mysql_query('SELECT * FROM regcodes WHERE code = \'' . getValue('code') . '\' ');
//
//		if (mysql_num_rows($code_res) == 0) {
//			$errors[] = 'Authentication code was\'t found!';
//			$errors[] = 'Did you used our app at all?';
//		}
//
//	}
//
//	if (!count($errors)) {
//
//		$code_data = mysql_fetch_assoc($code_res);
//		mysql_query('UPDATE users SET sacsid = \'' . $code_data['sacsid'] . '\' , last_login = NOW() WHERE id_user = ' . (int)$_SESSION['id_user'] . ' LIMIT 1');
//		mysql_query('DELETE FROM regcodes WHERE id_regcode = ' . $code_data['id_regcode'] . ' LIMIT 1');
//		header('Location: index.php?forcesync');
//
//	}
//
//	if (count($errors)) {
//		showErrors($errors);
//	}
//}
?>

<div class="row">
	<div class="col-md-4 col-md-offset-4">
		<div class="row">
			<div class="col-sm-12 col-md-12">
				<p>Your authentication session is expired!</p>

				<p>You should authenticate again from our <strong><a href="<?php echo $inventoryapp; ?>">our app</a></strong> to sync your game state</p>

				<p>You can scan the code bellow to download our app</p>
				<a href="<?php echo $inventoryapp; ?>" class="thumbnail">
					<img
						data-src="http://qrfree.kaywa.com/?l=1&s=8&d=http%3A%2F%2F<?php echo $_SERVER['HTTP_HOST'];?>%2F<?php echo $inventoryapp; ?>"
						src="http://qrfree.kaywa.com/?l=1&s=8&d=http%3A%2F%2F<?php echo $_SERVER['HTTP_HOST'];?>%2F<?php echo $inventoryapp; ?>"
						alt="...">
				</a>
			</div>
		</div>

	</div>
</div>