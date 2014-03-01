<?php
require 'datafetch.php';

$media = $player_inventory['MEDIA'];
$total = 0;
?>
<div class="row">
	<div class="col-sm-10 col-sm-offset-1">
		<div class="row">
<?php
foreach ($media as $lvl => $m){
	if($m['count']> 0){
		$total += $m['count'];
		if(stristr($m['url'], 'youtube')){
			$data = 'media';
		} else {
			$data ='image';
		}
		echo '
			<div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                    <div class="thumbnail media">
						<a class="medias" rel="gallery" target="_blank" href="'.$m['url'].'" title="'.$m['name'].'"><img src="'.$m['image'].'"  /></a>
						<div class="caption">
							<p class="text-center"><a class="medias" target="_blank" href="'.$m['url'].'">'.$m['name'].'</a></p>
						</div>
					</div>
			</div>';
	}
}
echo '
		<hr class="col-sm-10 col-sm-offset-1" />';
echo '
<table class="table table-condensed">
<tbody>
			<tr>
				<td>Total </td>
				<td>'.$total.'</td>
			</tr>
</tbody>
</table>';
?>
</div>
	<script type="text/javascript">
$(document).ready(function(){
	$(".medias").fancybox({
			openEffect	: 'elastic',
			closeEffect	: 'elastic',
			padding: 4,
			loop: false,
			helpers : {
				
				media: {}
			}
		});
	});
</script>