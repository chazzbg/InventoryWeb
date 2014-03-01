<?php
$errors = array();
if (isSubmit('redeem')) {
	if (getValue('passcode') == '') {
		$errors[] = 'Passcode is empty!';
	} else if (!isPasscode(getValue('passcode'))) {
		$errors[] = 'Passcode is invalid!';
	}


	if (!count($errors)) {
		$api = new API();
		$api->setSACSID($_SESSION['sacsid']);
		if ($api->handshake()) {
			$resp = $api->redeemReward(getValue('passcode'));


			if ($resp == false)
				$errors[] = $api->getError();
			else {


				echo '<div class="alert alert-info col-md-6 col-md-offset-3"> <p class="text-center">' . $resp['result'] . '</p></div>';

				echo '<table class="table table condensed">';
				if ($resp['apAward'] > 0)
					echo '
									<tr>
										<td>AP</td>
										<td>' . $resp['apAward'] . '</td>
									</tr>';
				if ($resp['xmAward'] > 0)
					echo '
									<tr>
										<td>XM</td>
										<td>' . $resp['xmAward'] . '</td>
									</tr>';


				foreach ($resp['items'] as $item => $v) {
					if (in_array($item, array('EMITTER_A', 'EMP_BURSTER', 'POWER_CUBE'))) {
						foreach ($v as $lvl => $count) {
							if ($count > 0)
								echo '
											<tr class="level_' . $lvl . '" >
												<td>' . friendly_name($item) . ' L' . $lvl . '</td>
												<td>' . $count . '</td>
											</tr>';
						}
					} else if (in_array($item, array('RES_SHIELD', 'FORCE_AMP', 'HEATSINK', 'LINK_AMPLIFIER', 'MULTIHACK', 'TURRET'))) {
						foreach ($v as $rarity => $count) {
							if ($count > 0)
								echo '
											<tr class=" ' . strtolower($rarity) . '">
												<td>' . friendly_name($item) . ' ' . mb_convert_case(str_replace("_", " ", $rarity), MB_CASE_TITLE) . '</td>
												<td>' . $count . '</td>
											</tr>';
						}
					}
				}

				echo '</table>';
			}

		} else {
			$errors[] = 'Expired Auth code!';
		}
	}

	if (count($errors))
		showErrors($errors);
}
?>

<div class="row">
	<div class="col-md-4 col-md-offset-4">
		<form role="form" method="post">
			<div class="form-group">
			<div class="input-group">

				<input type="text" name="passcode" class="form-control" id="passcode" placeholder="Passcode">



				<span class="input-group-btn">
						<button type="redeem" name="check" class="btn btn-default">Redeem</button>
					</span>
			</div>
			</div>
		</form>
	</div>
</div>

