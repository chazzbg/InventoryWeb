<?php
require 'datafetch.php';
if(isset($error) AND $error) 
	return;

$total = 0;
$api = new API();
foreach ($player_inventory as $type => $arr){
	if($type =='EMITTER_A' || $type== 'EMP_BURSTER' || $type =='POWER_CUBE'){
		foreach ($arr as $v)
			$total += $v;
	} else if (in_array($type, array('RES_SHIELD','FORCE_AMP','HEATSINK' ,'LINK_AMPLIFIER','MULTIHACK','TURRET'))){
		foreach ($arr as $v)
			$total += $v;
	} else {
		foreach ($arr as $v){
			$total += $v['count'];
		}
	}
}

if (isset($player_inventory['FLIP_CARD']['total'])) {
	$total  += $player_inventory['FLIP_CARD']['total'];
}
$ap_img = strtolower($player_data['team']).'_'.((int)($player_data['apPercent']/10)).'.png';

?>
	<div class="row">
		<div class="col-sm-6 col-sm-offset-4">
			<div id="ap_icon" style="background-image: url(img/<?php echo $ap_img; ?>);">
				<div id="level" class="level_<?php echo $player_data['level']; ?>"><?php echo $player_data['level']; ?></div>
			</div>
			<h2 class="player_name <?php echo strtolower($player_data['team']); ?>"><?php echo $player_data['name']; ?></h2>

		</div>
		<hr class="col-sm-10 col-sm-offset-1" />
	</div>

	<div class="row">
		<div class="col-sm-4 col-sm-offset-4 ">
		<table class="table table-condensed">
			<tbody>
			<tr>
				<td>Agent AP</td>
				<td><?php echo number_format($player_data['ap']); ?> AP</td>
			</tr>
			<?php if ($player_data['level'] <8){ ?>
			<tr>
				<td>AP to next level</td>
				<td><?php echo $player_data['diffAp']; ?></td>
			</tr>
			<?php } ?>
			<tr>
				<td>Agent XM</td>
				<td><?php echo number_format($player_data['energy']).' / '.number_format($api->getXmForLevel($player_data['level'])); ?></td>
			</tr>
			<tr>
				<td>Total items</td>
				<td><?php echo $total; ?></td>
			</tr>
			<tr>
				<td>Invites</td>
				<td><?php echo $player_data['invites']; ?></td>
			</tr>
			</tbody>
		</table>
		</div>
	</div>

		<div id="chart" class="chart"> </div>





	<?php 
	 $series_ap_data = array();
	 $categories = '';
	 foreach ($history as $date => $h){


		 $categories .= '\''.date('M j, Y H:i',  $date).'\', ';
		 $series_ap_data[] = $h['player_data']['ap'];
	 }
	 
	 $categories = rtrim($categories,', ');
	?>
	<script type="text/javascript">
		$(function () {
        $('#chart').highcharts({
            chart: {
                type: 'area',
				style: {
					fontFamily: 'Coda, sans-serif',
				}
            },
            title: {
                text: 'AP history'
            },
            
            xAxis: {
                categories: [ <?php echo $categories ?> ],
                tickmarkPlacement: 'on',
                title: {
                    enabled: false
                }
            },
            yAxis: {
                title: {
                    text: 'AP'
                },
				plotBands: [ <?php 
				for ($level = 1; $level<=8; $level++){
					echo '
				{ 
                    from: '.($level>1? $api->getApForLevel($level) : '0').',
                    to:'.(($level+1)>8? '10000000' : $api->getApForLevel($level+1)).',
                    color: "'.getLevelColor($level).'",
                    label: {
                        text: "L'.$level.'",
                        style: {
                            color: "#afafaf"
                        }
                    }
                }';
				
					
					if ($level <8) echo ',';
				}
				?>
					]
            },
            tooltip: {
                shared: true,
                valueSuffix: ''
            },
            plotOptions: {
                area: {
                    stacking: 'normal',
                    lineColor: '<?php echo getFactionColor($player_data['team'])?>',
                    lineWidth: 1,
                    marker: {
                        enabled: false
                    }
                }
            },
            series: [{
                name: 'AP',
                data: [<?php echo implode(', ', $series_ap_data); ?>],
				color: '<?php echo getFactionColor($player_data['team'])?>'
            }]
        });
    });
	
	
	</script>
