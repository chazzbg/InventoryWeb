<?php
require 'config.php';
require 'functions.php';
session_start();
require 'API.php';
require 'kml.class.php';
if (!isset($_COOKIE['PHPSESSID']) OR (isset($_COOKIE['PHPSESSID']) AND count($_SESSION) ==0)) {
	header("Location:index.php");
} else {

	$res = mysql_query('SELECT * FROM sessions s
	JOIN users u ON (u.id_user = s.id_user)
	WHERE s.sess_id = \'' . $_COOKIE['PHPSESSID'] . '\'');


	if (!@mysql_numrows($res)) {
		die();
	} else {

		$_SESSION           = mysql_fetch_assoc($res);
		$_SESSION['logged'] = true;
		$force_sync         = false;
		require 'datafetch.php';
		$keys         = $player_inventory['PORTAL_LINK_KEY'];
		$total        = 0;
		$portals      = '';
		$orderBy      = getValue('orderby', 'name');
		$orderWay     = getValue('orderway', 'asc');
		$filter       = getValue('filter','');
		$filterBy     = getValue('filterBy','');
		$itemsPerPage = 30;
		$page         = (int)getValue('page', 1);
		$filter_new   = (bool)getValue('filter_new',0);


		$original_pagesNum = (int)getValue('original_pagesNum');


		$sorted = array();
		if ($page < 1)
			$page = 1;


		if($filter !='' AND $filter[0] == "@")
			$filterBy = 'city';
		else
			$filterBy = 'title';


		$filter =substr($filter,1,strlen($filter)-1);

		$last = $last['player_inventory']['PORTAL_LINK_KEY'];
		foreach ($keys as $guid => $k) {
			if($filter != '' ){

				if($filterBy =='city')
					$haystack = $k['address'];
				 else
					$haystack = $k['title'];

				if(stripos($haystack,$filter) === false)
						continue;

//				if($filter[0] == "@"){
//					if(stripos($haystack,substr($filter,1,strlen($filter)-1)) ===false)
//						continue;
//				} else {
//					$temp_f = explode(" ", $filter);
//
//					$found = false;
//					foreach($temp_f as $f){
//						if(stripos($haystack,$f) !==false)
//							$found |= true;
//					}
//					if(!$found) continue;
//				}
			}

			if($filter_new) {

				if (!isset($last[$guid])) {
					$diff = $k['count'];
				} else {
					$diff = $k['count'] - $last[$guid]['count'];
				}

				if($diff <=0) continue;

			}

			$k['image_small'] = $k['image'];
			$k['image']       = str_replace('/small/', '/medium/', $k['image']);

			if ($orderBy == 'count') {
				$sorted[$k['count'] . '_' . $k['title'] . '_' . $guid]         = $k;
				$sorted[$k['count'] . '_' . $k['title'] . '_' . $guid]['guid'] = $guid;

			} else {
				$sorted[$k['title'] . '_' . $guid]         = $k;
				$sorted[$k['title'] . '_' . $guid]['guid'] = $guid;
			}
		}
		$pages_count = ceil(count($sorted)/$itemsPerPage);
	if(count($sorted)){

		if($orderBy !='none')
			uksort($sorted, 'cmp');





		if ($orderWay == 'desc') {
			$sorted = array_reverse($sorted);
		}

		if($page > $pages_count) $page = 1;
		$sorted = array_slice($sorted, ($page - 1) * $itemsPerPage, $itemsPerPage);



	$portals .= '<ul id="og-grid" class="og-grid">';
		foreach ($sorted as $id => $k) {
			$total += $k['count'];


			if (!isset($last[$k['guid']])) {
				$diff = $k['count'];
			} else {
				$diff = $k['count'] - $last[$k['guid']]['count'];
			}
			if ($diff > 0)
				$diff = '+' . $diff;

			$portals .= '
					<li>
						<a class="portal_img thumbnail" href="#" data-intel="https://ingress.com/intel?ll=' . $k['location']['lat'] . ',' . $k['location']['long'] . '&z=25&pll=' . $k['location']['lat'] . ',' . $k['location']['long'] . '" data-guid="' . $k['guid'] . '" data-timestamp="' . (isset($k['timestamp']) ? $k['timestamp'] : 0) . '" data-largesrc="' . $k['image'] . '" data-title="' . str_replace('"', '', $k['title']) . ' [ ' . $k['count'] . ' ]" data-description="' . str_replace(array('"', "'"), array('', ''), $k['address']) . '" data-count="' . $k['count'] . '">
						<img class="portal_image" src="' . $k['image_small'] . '" alt="' . str_replace('"', '\"', $k['title']) . ' [ ' . $k['count'] . ' ]" />
						<span class="portal_count">[ ' . $k['count'] . ' ] </span>
						' . ($diff != 0 ? '<span class="portal_count_diff' . ($diff > 0 ? ' plus' : ' minus') . '">[ ' . $diff . ' ]</span>' : '') . '
						<span class="portal_title">' . $k['title'] . '</span>
						</a>
					</li>';
		}
	$portals .='</ul>';
	} else {
		$portals = '
			<h3 class="page-header text-center">No results</h3>
		';
	}

		?>


			<?php echo $portals; ?>

	<script>

		Grid.addItems($("#og-grid li"));

		var $new_pagesNum = <?php echo $pages_count; ?>;
		var $original_pagesNum = <?php echo $original_pagesNum; ?>;
		var $script_page = <?php echo $page; ?>;
		$(".pagination").each(function(){
			console.log($(window).width());
			if($(window).width() > 767)
				$(this).children("li").css({display: "inline"});

			$(this).children("li").slice($new_pagesNum+1,$original_pagesNum+1 ).hide();



				$(this).children("li.active").removeClass("active");
				$(this).children("li").eq($script_page).addClass("active");
			if($script_page != $page) {
				$page = $script_page;
			}

		});
		$pagesNum = $new_pagesNum;
	</script>


	<?php
	}
}

function cmp($k1, $k2){
	return strnatcasecmp($k1,$k2);
}
?>