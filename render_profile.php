<?php



$metrics = array();
foreach($player_profile['metrics'] as $m){
	$metrics[$m['metricCategory']][] = $m;
}
$badges = $player_profile['highlightedAchievements'];


$i =0;

echo '
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2">
			<div class="row" id="badges">';
foreach($badges as $k=>$b){
	//if($b['group'] =='founder') continue;


	echo '
				 <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 ">
                    <div class="thumbnail">';
	if(!is_array($b['tiers'])) continue;
	foreach($b['tiers'] as $j=>$t){
		if((bool)$t['locked']) break;
	}
	if($j == 0 AND count($b['tiers']) ==1 AND $b['tiers'][$j]['locked'] == false)
		echo '
						<img src="'.$b['tiers'][0]['badgeImageUrl'].'"  width="100px" title="'.$b['description']."\nUnlocked on: ".date('Y-m-d H:m:s',substr($b['timestampAwarded'],0,10)).'" data-toggle="tooltip" data-placement="bottom" />';

	elseif($j >0)
		echo '
						<img src="'.$b['tiers'][$j-1]['badgeImageUrl'].'"  width="100px" title="'.$b['description']."\n".$b['tiers'][$j-1]['value'].($j < 4 ? "\nNext badge on: ".$b['tiers'][$j]['value'] :'')."\nUnlocked on: ".date('Y-m-d H:m:s',substr($b['timestampAwarded'],0,10)).'"  data-toggle="tooltip" data-placement="bottom" />';
	else
		echo '
					<div style="background: url('.$b['tiers'][$j]['badgeImageUrl'].')  no-repeat center center ; background-size:98px 98px;" class="text-center" >
						<img src="img/locked.png"  width="100px" height="100px" title="'.$b['description'].($j < 4 ? "\nNext badge on: ".$b['tiers'][$j]['value'] :'').'"  data-toggle="tooltip" data-placement="bottom" />
					</div>';


	echo '
						<div class="caption">
			                <p class="text-center">'.$b['title'].'</p>

			            </div>
                    </div>
                   </div>';


	$i++;
}


echo '
         </div></div> </div>';
?>
<div class="clearfix">&nbsp;</div>
<!-- Nav tabs -->
<div class="row">
	<div class="">
		<ul class="nav nav-tabs col-md-12">
			<li class="active col-md-4 col-sm-4 col-xs-4 text-center"><a href="#alltime" data-toggle="tab">All time</a></li>
			<li class="col-md-4 col-sm-4 col-xs-4 text-center"><a href="#month" data-toggle="tab">Month</a></li>
			<li class="col-md-4 col-sm-4 col-xs-4 text-center"><a href="#week" data-toggle="tab">Week</a></li>

		</ul>
	</div>
</div>

<!-- Tab panes -->
<div class="tab-content">


	<?php
	echo '<div class="tab-pane active" id="alltime">';
	foreach($metrics as $group => $group_metrics){
		echo '

		<div class="row">
			<h3 class="text-center">'.$group.'</h3>

			<hr class="col-sm-10 col-sm-offset-1" />
			<table class="table table-condensed">
				<tbody>';
		foreach($group_metrics as  $m){
			if(isset($m['formattedValueAllTime']))
				echo '
				<tr>
					<td>'.$m['metricName'].'</td>
					<td>'.$m['formattedValueAllTime'].'</td>
				</tr>';
		}
		echo '
				</tbody>
			</table>
		</div>
	';
	}
	echo '</div>';
	echo '<div class="tab-pane" id="month">';
	foreach($metrics as $group => $group_metrics){
		echo '

	<div class="row">
	<h3 class="text-center">'.$group.'</h3>

	<hr class="col-sm-10 col-sm-offset-1" />
	<table class="table table-condensed">
		<tbody>';
		foreach($group_metrics as  $m){
			if(isset($m['formattedValue30Days']))
				echo '<tr>
		<td>'.$m['metricName'].'</td>
		<td>'.$m['formattedValue30Days'].'</td>
		</tr>';
		}
		echo '</tbody></table></div>';
	}
	echo '</div>';

	echo '<div class="tab-pane" id="week">';
	foreach($metrics as $group => $group_metrics){
		echo '

	<div class="row">
	<h3 class="text-center">'.$group.'</h3>

	<hr class="col-sm-10 col-sm-offset-1" />
	<table class="table table-condensed">
		<tbody>';
		foreach($group_metrics as  $m){
			if(isset($m['formattedValue7Days']))
				echo '<tr>
		<td>'.$m['metricName'].'</td>
		<td>'.$m['formattedValue7Days'].'</td>
		</tr>';
		}
		echo '</tbody></table></div>';
	}
	?>
</div>
</div>

<script>
	$('#alltime a').click(function (e) {
		e.preventDefault()
		$(this).tab('show')
	});
	$('#month a').click(function (e) {
		e.preventDefault()
		$(this).tab('show')
	});
	$('#week a').click(function (e) {
		e.preventDefault()
		$(this).tab('show')
	});

$("#badges img").tooltip();

</script>