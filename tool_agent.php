<?php 

$api = new API();
$api->setSACSID($_SESSION['sacsid']);

$player_profile = false;

if(isSubmit('check')) {

if($api->handshake()){
		$player_profile = $api->getPlayerProfile(getValue('agent'));

		if($player_profile == false) {
			showErrors(array('Agent not found!'));
		}
	} else {
		
		
		header('Location: index.php?page=reauth');
		die();
	}
} 
?>

	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<form role="form" method="post">
				<div class="form-group">
				<div class="input-group">


					<input type="text" name="agent" class="form-control" id="agent" placeholder="Agent name" <?php if(isSubmit('agent')) echo 'value="'.getValue ('agent').'"'; ?> >



					<span class="input-group-btn">
						<button type="submit" name="check" class="btn btn-default">Check</button>
					</span>

				</div>
				</div>
			</form>
		</div>
	</div>


<?php 

if(isSubmit('check') AND $player_profile != false) {


	$player_level = $api->getPlayerLevelByAp($player_profile['ap']);
	$ap_img = strtolower($player_profile['team']).'_0.png';

?>


	<div class="row">
		<div class="col-sm-6 col-sm-offset-4">
			<div id="ap_icon" style="background-image: url(img/<?php echo $ap_img; ?>);">
				<div id="level" class="level_<?php echo $player_level; ?>"><?php echo $player_level; ?></div>
			</div>
			<h2 class="player_name <?php echo strtolower($player_profile['team']); ?>"><?php echo getValue('agent'); ?> <small><?php echo number_format($player_profile['ap']); ?> AP</small></h2>

		</div>
		<hr class="col-sm-10 col-sm-offset-1" />
	</div>


	<br />

	<?php
require 'render_profile.php';

} ?>