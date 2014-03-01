<?php
// find if point is in polygon
// set @p = GeomFromText('POINT(43.0756739 25.6171514)');
// SELECT id_cell,region_name  FROM `regions` WHERE MBRContains(vertices,@p)
$api = new API();
$api->setSACSID($_SESSION['sacsid']);
$errors = array();
$fetch_new_score = false;
$insert = true;
date_default_timezone_set('Europe/Athens');

if (isSubmit('check')) {

	$location = getCoordinates(getValue('loc'));

	if (!$location) {
		showErrors(array('Invalid Location'));
	} else {
//		mysql_query(' SET @p = GeomFromText(\'POINT(' . $location['lat'] . ' ' . $location['lng'] . ')\');');
//		$res = mysql_query('SELECT id_cell, region_name FROM `regions` WHERE Contains( vertices, @p);');

		$cell  =json_decode(file_get_contents('http://ingress-cells.appspot.com/query?lat='.$location['lat'].'&lng='.$location['lng']),true);


		if ($cell == false OR isset($cell['error'])) {
			$errors[] = 'Cant determine region of the location!';
		} else {

			$res = mysql_query('SELECT * FROM region_scores WHERE id_cell = \'' . $cell['s2'] . '\'') or die(mysql_error());;

			if ($res != false AND mysql_num_rows($res) > 0) {
				$score  = mysql_fetch_assoc($res);
				$next_update = (int) $score['next_update'];
				$score = json_decode($score['score_data'],true);

				$vertices = $cell['geom'];
				$verticles = array();
				$verticles[] = array('lat' => $vertices['nw'][0], 'lng' => $vertices['nw'][1]);
				$verticles[] = array('lat' => $vertices['sw'][0], 'lng' => $vertices['sw'][1]);
				$verticles[] = array('lat' => $vertices['se'][0], 'lng' => $vertices['se'][1]);
				$verticles[] = array('lat' => $vertices['ne'][0], 'lng' => $vertices['ne'][1]);


				if($next_update < time())
					$fetch_new_score = true;



				$insert = false;
			} else {
				$insert = true;
				$fetch_new_score = true;
			}

			if(isSubmit('force')) $fetch_new_score = true;
			if ($fetch_new_score) {
				if ($api->handshake()) {

					$score = $api->getRegionScore($cell['s2']);

					if ($score != false) {

						$leaderBoard = $api->getRegionScoreLeaderBoard($cell['s2']);

						if($leaderBoard != false){
							$score['viewingPlayerRank'] = $leaderBoard['viewingPlayerRank'];
							$score['enlightenedTopAgents'] = $leaderBoard['enlightenedTopAgents'];
							$score['resistanceTopAgents'] = $leaderBoard['resistanceTopAgents'];
						}
						$vertices = $cell['geom'];
						$verticles = array();
						$verticles[] = array('lat' => $vertices['nw'][0], 'lng' => $vertices['nw'][1]);
						$verticles[] = array('lat' => $vertices['sw'][0], 'lng' => $vertices['sw'][1]);
						$verticles[] = array('lat' => $vertices['se'][0], 'lng' => $vertices['se'][1]);
						$verticles[] = array('lat' => $vertices['ne'][0], 'lng' => $vertices['ne'][1]);

						$next_update = (int)time() + (int)($score['cycleTimelineDetails']['timeLeftInCurrentBaseCycleMs'] / 1000);

						if($insert){ // insert the socre
							mysql_query('INSERT INTO region_scores ( `id_cell`, `score_data` , `last_update` , `next_update`)
								VALUES ( \''.$cell['s2'].'\', \''.json_encode($score).'\', '.time().' , '.$next_update.' )') or die(mysql_error());
						} else { //just update them
							mysql_query('UPDATE region_scores SET
								`score_data`  = \''.json_encode($score).'\',
								`last_update` = '.time().',
								`next_update` = '.$next_update.'
								WHERE `id_cell` = \''.$cell['s2'].'\'') or die(mysql_error());
						}

					} else {
						$errors[] = $api->getError();
					}

				} else {


					header('Location: index.php?page=reauth');
					die();
				}
			}
			$leadFaction = "NEUTRAL";
			if ($score['card']['currentScore']['resistanceScore'] < $score['card']['currentScore']['alienScore']) {
				$leadFaction = "ALIENS";
			} else {
				$leadFaction = "RESISTANCE";
			}


		}

		if (count($errors))
			showErrors($errors);
	}
}


?>

	<div class="row">
		<div class="col-md-4 col-md-offset-4 clearfix">
			<form role="form" method="post">
				<div class="form-group">
				<div class="input-group">
					<input type="text" name="loc" class="form-control" id="loc"
					       placeholder="Location" <?php if (isSubmit('loc'))
						echo 'value="' . getValue('loc') . '"'; ?>  />

					<span class="input-group-btn">
						<button type="submit" name="check" class="btn btn-default">Check</button>
					</span>
				</div>
					</div>
			</form>
		</div>

	</div>



<?php if (isset ($score) AND $score != false) { ?>

	<script type="text/javascript"
	        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAIZy_IkcPcdC-YluFC-CJYFnz_Kb9p_6k&sensor=true">
	</script>
	<script type="text/javascript" src="js/infobubble.js?<?php echo _VERSION_; ?>"></script>

	<script type="text/javascript">
		var data = <?php echo json_encode($verticles); ?>;
		var center = {lat: 0, lng: 0};

		var minLat = data[0].lat, maxLat = data[0].lat,
			minLng = data[0].lng, maxLng = data[0].lng;


		for (d = 0; d < data.length; d++) {
			if (data[d].lat < minLat)
				minLat = data[d].lat;
			if (data[d].lat > maxLat)
				maxLat = data[d].lat;

			if (data[d].lng < minLng)
				minLng = data[d].lng;
			if (data[d].lng > maxLng)
				maxLng = data[d].lng;
		}

		center.lat = (  maxLat + minLat ) / 2;
		center.lng = ( maxLng + minLng) / 2;


		var maps_name = "<?php echo $score['card']['regionName']; ?>";
		google.maps.visualRefresh = true;
		var position = {coords: {latitude: <?php echo $location['lat']; ?>, longitude: <?php echo $location['lng']; ?> }};
		window.BIG_REGION = false;
		window.PERSIST = false;
		function getLocation() {
			initialize(position, 8);
		}

		function initialize(position, zooom) {
			if (zooom == undefined) zooom = 9;
			var mapOptions = {
				center: new google.maps.LatLng(position.coords.latitude, position.coords.longitude),
				zoom: zooom,
				styles: [
					{featureType: "all", elementType: "all", stylers: [
						{visibility: "on"},
						{hue: "#131c1c"},
						{saturation: "-50"},
						{invert_lightness: true}
					]},
					{featureType: "water", elementType: "all", stylers: [
						{visibility: "on"},
						{hue: "#172e2e"}
					]},
					{featureType: "poi", stylers: [
						{visibility: "off"}
					]},
					{featureType: "transit", elementType: "all", stylers: [
						{visibility: "off"}
					]}
				],
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			var map = new google.maps.Map(document.getElementById("map-canvas"),
				mapOptions);

			var cellOutline = null;
			var cellName = null;

			var CellNameMarker = function (map, position, name, recorded_at_ms) {
				this.map_ = map;
				this.position_ = position;
				this.name_ = name;
				this.div_ = null;
				this.setMap(map);
			};
			CellNameMarker.prototype = new google.maps.OverlayView();

			CellNameMarker.prototype.onAdd = function () {
				var div = document.createElement('div');
				div.className = 'cell-name-marker';
				div.innerHTML = this.name_;
				this.div_ = div;
				var panes = this.getPanes();
				panes.overlayImage.appendChild(div);
			};
			CellNameMarker.prototype.onRemove = function () {
				this.div_.parentNode.removeChild(this.div_);
				this.div_ = null;
			};
			CellNameMarker.prototype.draw = function () {
				var overlayProjection = this.getProjection();
				var pos = overlayProjection.fromLatLngToDivPixel(this.position_);

				var div = this.div_;
				div.style.left = pos.x + 'px';
				div.style.top = pos.y + 'px';
			};
			var drawCellOnMap = function (data, center, jump) {

				if (cellName != null && !window.PERSIST) {
					cellName.setMap(null);
					cellName = null;
				}
				if (cellOutline != null && !window.PERSIST) {
					cellOutline.setMap(null);
					cellOutline = null;
				}

				var path = [];
				path.push(new google.maps.LatLng(data[0].lat, data[0].lng));
				path.push(new google.maps.LatLng(data[1].lat, data[1].lng));
				path.push(new google.maps.LatLng(data[2].lat, data[2].lng));
				path.push(new google.maps.LatLng(data[3].lat, data[3].lng));


				cellOutline = new google.maps.Polygon({
					map: map,
					paths: path,
					geodesic: true,
					strokeColor: '<?php echo getFactionColor($leadFaction)?>',
					strokeOpacity: 0.95,
					strokeWeight: 2,
					fillColor: '<?php echo getFactionColor($leadFaction)?>',
					fillOpacity: 0.20
				});
				cellName = new CellNameMarker(
					map,
					new google.maps.LatLng(center.lat, center.lng),
					maps_name
				);
				if (jump) {
					var bounds = new google.maps.LatLngBounds();
					for (var i = 0; i < 3; ++i) {
						bounds.extend(path[i]);
					}


					map.fitBounds(bounds);
				}
			};

			drawCellOnMap(data, center, true);

			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(position.coords.latitude,position.coords.longitude),
				map: map,
				icon: 'img/portal_marker.png',
				shadow: 'img/portal_shadow.png',
				zIndex: Math.round(100000)<<5
			});
		}
		google.maps.event.addDomListener(window, 'load', getLocation);


	</script>
	<div class="row">
		<h3 class="text-center"><?php echo $score['card']['regionName']; ?></h3>

		<div id="map-canvas" class="col-md-6 col-md-offset-3" style="height: 300px"></div>
	</div>
	<div class="score row">
		<h3 class="text-center">Score</h3>
		<hr/>
		<div class="col-md-6 text-right">
			<h3  class="resistance">Resistance</h3>
			<p class="resistance"><?php echo number_format($score['card']['currentScore']['resistanceScore']); ?> MU</p>
		</div>
		<div class="col-md-6 text-left">
			<h3 class="aliens">Enligthened</h3>
			<p class="aliens"><?php echo number_format($score['card']['currentScore']['alienScore']); ?> MU</p>
		</div>

	</div>
	<div class="row">
		<h3 style="text-align: center">Top 3 deployed agents</h3>
		<hr/>
			<?php

			foreach ($score['topAgents'] as $k => $player) {

				echo '
	<div class="col-md-4 text-center '.strtolower($player['team']).'">
		<h3 class="'.strtolower($player['team']).'">' . ($k + 1) . '. <a style="color: inherit;" href="index.php?page=tools&tool=agent&agent=' . ($player['nickname']) . '&check">' . ($player['nickname']) . '</a></h3>
	</div>';

			}?>

	</div>
	<div class="row">
		<h3 style="text-align: center">Cicle History</h3>
		<hr/>
		<div id="chart" class="chart"></div>
	</div>
	<?php
	$categories     = '';
	$series_mu_data = array();
	$avg_res        = 0;
	$avg_enl        = 0;
	for ($i = 0; $i < 35; $i++) {


		$categories .= '\'\', ';
		//if($i < 10 OR $i > 15){
		if (isset($score['cycleTimelineDetails']['scoreHistory'][$i])) {

			$series_mu_data['RESISTANCE'][] = $score['cycleTimelineDetails']['scoreHistory'][$i]['resistanceScore'] * $score['cycleTimelineDetails']['multipliers'][$i];
			$series_mu_data['ALIENS'][]     = $score['cycleTimelineDetails']['scoreHistory'][$i]['alienScore'] * $score['cycleTimelineDetails']['multipliers'][$i];

			$avg_res += $score['cycleTimelineDetails']['scoreHistory'][$i]['resistanceScore'] * $score['cycleTimelineDetails']['multipliers'][$i];
			$avg_enl += $score['cycleTimelineDetails']['scoreHistory'][$i]['alienScore'] * $score['cycleTimelineDetails']['multipliers'][$i];
		} else {
			$series_mu_data['RESISTANCE'][] = 'null';
			$series_mu_data['ALIENS'][]     = 'null';
		}

	}
	if(count($score['cycleTimelineDetails']['scoreHistory']))
	$avg_enl = (int)($avg_enl / count($score['cycleTimelineDetails']['scoreHistory']));
	if(count($score['cycleTimelineDetails']['scoreHistory']))
	$avg_res = (int)($avg_res / count($score['cycleTimelineDetails']['scoreHistory']));

	$categories = rtrim($categories, ', ');
	?>
	<script type="text/javascript">
		$(function () {
			$('#chart').highcharts({
				chart: {
					style: {
						fontFamily: 'Coda, sans-serif',
					}
				},
				title: {
					text: ''
				},

				xAxis: {
					categories: [ <?php echo $categories ?> ],
					tickmarkPlacement: 'off',

				},
				yAxis: {
					title: {
						text: ''
					},
					min: 0,
					plotLines: [
						{
							value:<?php echo $avg_res; ?>,
							color: '<?php echo getFactionColor('RESISTANCE'); ?>',
							width: 1,
							zIndex: 4,

						},
						{
							value:<?php echo $avg_enl; ?>,
							color: '<?php echo getFactionColor('ALIENS'); ?>',
							width: 1,
							zIndex: 4,

						}
					]
				},
				tooltip: {
					shared: true,
					valueSuffix: ' MU'
				},
				series: [<?php
			foreach ($series_mu_data as $team => $scores){
				echo '{
					name: "'.($team =='ALIENS' ? 'Enlightened':'Resistance').' ",marker: {enabled: true} ,color: "'.  getFactionColor($team).'", data: ['.  implode(',', $scores).']
				},';
			}

				?>]
			});
		});


	</script>
	<div class="row">
		<h3 style="text-align: center">Next checkpoint</h3>
		<hr />
		<h1 class="text-center">
			<?php echo date("g:i:00 A , D j M Y",$next_update); ?>
		</h1>
	</div>

	<div class="score row">
		<h3 style="text-align: center">Leaderboard</h3>
		<hr />
		
		<div class="col-md-6 text-right">
			<h3 class="resistance">Resistance</h3>
			<?php
			if(isset($score['resistanceTopAgents']))
			foreach($score['resistanceTopAgents'] as $k => $player){
				echo '
				<p class="'.strtolower($player['team']).'"><a style="color: inherit;" href="index.php?page=tools&tool=agent&agent=' . ($player['nickname']) . '&check"> '.($k+1).'. ' . ($player['nickname']) . ' [ L'.$api->getLevelByInt($player['playerLevel']).' ] </a></p>';
			}
			?>

		</div>

		<div class="col-md-6 text-left">
			<h3 class="aliens">Enligthened</h3>
			<?php
			if(isset($score['enlightenedTopAgents']))
			foreach($score['enlightenedTopAgents'] as $k => $player){
				echo '
				<p class="'.strtolower($player['team']).'"><a style="color: inherit;" href="index.php?page=tools&tool=agent&agent=' . ($player['nickname']) . '&check"> '.($k+1).'. ' . ($player['nickname']) . ' [ L'.$api->getLevelByInt($player['playerLevel']).' ] </a></p>';
			}
			?>
		</div>
	</div>
<?php } ?>
