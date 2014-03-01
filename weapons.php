<?php
require 'datafetch.php';
$xmp = $player_inventory['EMP_BURSTER'];
$ultra = $player_inventory['ULTRA_STRIKE'];
$flipCards = isset($player_inventory['FLIP_CARD']['total']) ? $player_inventory['FLIP_CARD']['total'] : 0;
$total =0;





?>


<div class="row">

	<table class="table table-condensed cols-md-4">
		<tbody>
<?php
foreach ($xmp as $lvl => $count){
	if($count> 0 OR $last['player_inventory']['EMP_BURSTER'][$lvl] >0){
		$diff = $count - $last['player_inventory']['EMP_BURSTER'][$lvl];
		if($diff >0) $diff ='+'.$diff;
		
		$total += $count;
		echo '
			<tr class=" level_'.$lvl.'">
				<td>XMP Level '.$lvl.'</td>
				<td>'.$count.($diff != 0? ' <span class=""> ['.($diff).']</span>' :'').'</td>
			</tr>';
	}
}
foreach ($ultra as $lvl => $count){
	if($count> 0 OR @$last['player_inventory']['ULTRA_STRIKE'][$lvl] >0){
		$diff = $count - @$last['player_inventory']['ULTRA_STRIKE'][$lvl];
		if($diff >0) $diff ='+'.$diff;

		$total += $count;
		echo '
			<tr class=" level_'.$lvl.'">
				<td>Ultra Strike Level '.$lvl.'</td>
				<td>'.$count.($diff != 0? ' <span class=""> ['.($diff).']</span>' :'').'</td>
			</tr>';
	}
}
$total += $flipCards;
$diff = $flipCards - $last['player_inventory']['FLIP_CARD']['total'];
if($diff >0) $diff ='+'.$diff;
echo '
	<tr>
		<td>Alignment Virus</td>
		<td>'.$flipCards.($diff != 0? ' <span class=""> ['.($diff).']</span>' :'').'</td>
	</tr>';
echo '<tr > <td colspan="2"> <hr /></td>';
echo '
			<tr >
				<td>Total </td>
				<td>'.$total.'</td>
			</tr>';
?>
		</tbody>
	</table>
</div>
<div id="chart" class="chart"> </div>
	<?php 
	 $series_res_data = array();
	 $categories = '';
	 foreach ($history as $date => $h){
			 $categories .= '\''.date('M j, Y H:i',  $date).'\', ';

		 foreach ($h['player_inventory']['EMP_BURSTER'] as $level =>$count){

			 $series_res_data[$level][$date] = $count;
		 }

		 if(isset($h['player_inventory']['ULTRA_STRIKE']))
		 foreach ($h['player_inventory']['ULTRA_STRIKE'] as $level =>$count){

			 $series_res_data[$level][$date] += $count;
		 }


		 if(isset($h['player_inventory']['FLIP_CARD']['total']))
				$series_res_data['FLIP_CARD'][$date] = $h['player_inventory']['FLIP_CARD']['total'];
			else
				$series_res_data['FLIP_CARD'][$date] = 0;
	 }
	 krsort($series_res_data);
	 
	 $categories = rtrim($categories,', ');
	?>
	<script type="text/javascript">
		$(function () {
        $('#chart').highcharts({
            chart: {
                type: 'area'
            },
            title: {
                text: 'Weapons History'
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
                    text: 'Weapons'
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
                    },
					
                },
            },
			
            series: [
				<?php
			foreach ($series_res_data as $level => $counts){
				
				echo '{
					name: "'.($level =='FLIP_CARD'? 'Alignment Virus' : 'LVL '.$level).'",marker: {enabled: false} ,color: "'.  getLevelColor($level).'", data: ['.  implode(',', $counts).']
				},';
			}
				
				?>
				
			]
        });
    });
	
	
	</script>
