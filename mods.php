<?php
require 'datafetch.php';
$mods = array('RES_SHIELD','MULTIHACK','HEATSINK','FORCE_AMP','TURRET' ,'LINK_AMPLIFIER');
$shields = $player_inventory['RES_SHIELD'];

$mod_names = array('RES_SHIELD' => 'Portal Shield'
	,'FORCE_AMP' =>'Force Amp'
	,'HEATSINK' => 'Heat Sink'
	,'LINK_AMPLIFIER' => 'Link Amp'
	,'MULTIHACK' => 'Multi-hack'
	,'TURRET'=> 'Turret');

$total =0;
?>

<div class="row">
<?php

foreach ($mods as $mod){
	echo '<div class="col-xs-6 col-sm-3 col-md-2 col-lg-2 ">
                    <div class="thumbnail">
					<img src="img/'.$mod.'_EXTRARE.png" width="100px" />
					<div class="caption">
					<h4> '.$mod_names[$mod].'</h4>
	';
	$has = false;
foreach ($player_inventory[$mod] as $rarity => $count){
	
	if(!isset( $last['player_inventory'][$mod][$rarity])) $last['player_inventory'][$mod][$rarity] =0; 
	if($count> 0 OR (isset( $last['player_inventory'][$mod][$rarity]) AND  $last['player_inventory'][$mod][$rarity] >0)){
		$has = true;
		if(isset( $last['player_inventory'][$mod][$rarity]))
			$diff = $count - $last['player_inventory'][$mod][$rarity];
		else 
			$diff = 0;
		if($diff >0 ) $diff ='+'.$diff;
		$total += $count;
		echo '

			<p class="clearfix '.strtolower($rarity).'">
				<span class="pull-left">'.strtolower(str_replace("_", " ", $rarity)).'</span>
				<span class="pull-right">'.$count.($diff != 0? ' <span class=""> ['.($diff).'] </span>' :'').'</span>
			</p>';
	} else {
		echo '<p class="clearfix ">&nbsp;</p>';

	}
}
if(!$has) echo '
<p class="clearfix ">&nbsp;</p><p class=""> none </p>
<p class="clearfix ">&nbsp;</p>';

echo '</div>
</div>
</div>';


}


//echo '<hr />';
//echo '
//			<div class="info_row">
//				<div>Total </div>
//				<div>'.$total.'</div>
//			</div>';
?>
	
</div>
<div id="chart" class="chart"> </div>
	<?php 
	 $series_res_data = array();
	 $categories = '';
	 foreach ($history as $date => $h){
			 $categories .= '\''.date('M j, Y H:i',  $date).'\', ';
			 foreach ($mods as $mod){
				if(!isset($h['player_inventory'][$mod])){
					$h['player_inventory'][$mod]['COMMON'] = 0;
					$h['player_inventory'][$mod]['RARE'] = 0;
					$h['player_inventory'][$mod]['VERY_RARE'] = 0;
				}
				foreach ($h['player_inventory'][$mod] as $level =>$count){
					

					@$series_res_data[$level][ $date] += $count;
				}
			 }
			
	 }
	 
	 

	 //krsort($series_res_data);
	 $categories = rtrim($categories,', ');
	?>
	<script type="text/javascript">
		$(function () {
        $('#chart').highcharts({
            chart: {
                type: 'area'
            },
            title: {
                text: 'Mods History'
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
                    text: 'Mods'
                }
            },
            tooltip: {
                shared: true,
                valueSuffix: ''
            },
            plotOptions: {
                area: {
                    stacking: 'normal',
                    lineWidth: 1,
                    marker: {
                        enabled: false,
						lineWidth: 0,
                        lineColor: '#666666',
						symbol: "circle",
                    }
                },
            },
			
            series: [
				<?php
			foreach ($series_res_data as $level => $counts){
				

				echo '{
					name: "'.$level.'",marker: {enabled: false} ,color: "'.  getRarityColor($level) .'", data: ['.  implode(',', $counts).']
				},';
			}
				
				?>
				
			]
        });
    });
	
	
	</script>
