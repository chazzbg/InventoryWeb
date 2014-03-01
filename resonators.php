<?php
require 'datafetch.php';
$resonators = $player_inventory['EMITTER_A'];
$total =0;
?>

<div class="row">

	<table class="table table-condensed cols-md-4">
		<tbody>
<?php
foreach ($resonators as $lvl => $count){
	if($count> 0 OR $last['player_inventory']['EMITTER_A'][$lvl] >0){
		$diff = $count - $last['player_inventory']['EMITTER_A'][$lvl];
		if($diff >0) $diff ='+'.$diff;
		$total += $count;
		echo '
			<tr class="level_'.$lvl.'">
				<td>Resonator Level '.$lvl.'</td>
				<td>'.$count.($diff != 0? '<span class=""> ['.($diff).']</span>' :'').'</td>
			</tr>';
	}
}
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
		 foreach ($h['player_inventory']['EMITTER_A'] as $level =>$count){
			 
			$series_res_data[$level][] = $count;
		 }
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
                text: 'Resonator History'
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
                    text: 'Resonators'
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
					name: "LVL '.$level.'",marker: {enabled: false} ,color: "'.  getLevelColor($level).'", data: ['.  implode(',', $counts).']
				},';
			}
				
				?>
				
			]
        });
    });
	
	
	</script>
