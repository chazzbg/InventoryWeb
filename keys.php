<?php
$view = isSubmit('view')?getValue('view'):'list';
require 'datafetch.php';
$keys = $player_inventory['PORTAL_LINK_KEY'];
$total = 0;
$portals = '';


foreach ($keys as $guid => $k){
	//if(stripos($k['image'], 'panoramio'))
			$k['image_small'] = $k['image'];
			$k['image'] = str_replace ('/small/', '/medium/', $k['image']);
	
	$sorted[$k['title'].'_'.$guid] = $k;
	$sorted[$k['title'].'_'.$guid]['guid'] = $guid;
}

ksort($sorted);

$itemsPerPage = 30;
$pages_count = ceil(count($sorted)/$itemsPerPage);
if($view == 'list'){
$last = $last['player_inventory']['PORTAL_LINK_KEY'];
foreach ( $sorted as $id=>$k){
	$total += $k['count'];
}
?>
	<div class=" score col-md-12  clearfix">
		<!--		<div class="well well-sm ">-->
		<div class="col-md-3 text-left">
			<h4>Total keys <?php echo $total; ?></h4>

		</div>
		<div class="clearfix visible-sm visible-xs">&nbsp;</div>
		<div class="col-md-5">
			<div class="input-group">
				 <span class="input-group-addon">
			      New  <input type="checkbox" id="filter_new">
			     </span>
				<input type="text" class="form-control" id="filter_value" placeholder="Filter by name or @city" data-filter="name">
			      <div class="input-group-btn">
				      <ul class="dropdown-menu pull-right">
					      <li class="active"><a href="#" id="filter_by_name" >Filter by name</a></li>
					      <li><a href="#" id="filter_by_address">Filter by Address</a></li>
				      </ul>
<!--				      <button class="btn btn-default dropdown-toggle"  data-toggle="dropdown"  type="button"><span class="caret"></span></button>-->

				    <button class="btn btn-default"  id="filter" type="button">Go!</button>
                    <button class="btn btn-default" id="reset" type="button">Reset</button>

			      </div>
			</div>
		</div>
		<div class="clearfix visible-sm visible-xs">&nbsp;</div>

		<div class="col-md-2 text-center">

			<select name="sort" id="sort" class="form-control text-center">
				<option value="none:asc">No sorting</option>
				<option value="name:asc">Name : ascending</option>
				<option value="name:desc">Name : descending</option>
				<option value="count:asc">Count : ascending</option>
				<option value="count:desc">Count : descending</option>
			</select>


		</div>
		<div class="clearfix visible-sm visible-xs">&nbsp;</div>
		<div class="col-md-2 text-right " style="overflow: hidden">
			<a href="keysExport.php" class="btn btn-default text-center col-sm-12 col-xs-12"><span
					class="glyphicon glyphicon-download"></span> KML </a>
		</div>
		<!--		</div>-->
	</div>
	<div class="col-sm-12 text-center">
		<ul class="pagination">
			<li data-page="prev"><a href="#" data-page="prev">&laquo;</a></li>
			<?php
			for($i =1; $i <= $pages_count; $i++)
				echo '
				 <li  data-page="'.$i.'" class=" '.($i ==1 ? 'active' : '').'"><a href="#" '.($i == $page ? 'class="active"':'').' data-page="'.$i.'">'.$i.'</a></li>';
			?>
			<li data-page="next"><a href="#" data-page="next">&raquo;</a></li>
		</ul>
	</div>
<div id="portals_list_view">

</div>
	<div class="col-sm-12 text-center">
		<ul class="pagination">
			<li data-page="prev"><a href="#" data-page="prev">&laquo;</a></li>
			<?php
			for($i =1; $i <= $pages_count; $i++)
				echo '
				 <li  data-page="'.$i.'" class="'.($i ==1 ? 'active' : '').'"><a href="#" '.($i == $page ? 'class="active"':'').' data-page="'.$i.'">'.$i.'</a></li>';
			?>
			<li data-page="next"><a href="#" data-page="next">&raquo;</a></li>
		</ul>
	</div>
	<script src="js/exgrid/grid.js?<?php echo(_VERSION_); ?>"></script>

<script type="text/javascript">
	var $page = 1;
	var $orderBy = 'name';
	var $orderWay= 'asc';
	var $pagesNum = <?php echo $pages_count; ?>;
	var $filter = '';
	var $filter_by= '';
	var $filter_new = false;
	function sendRequest (){
		$("#portals_list_view").fadeTo(200,0.0,function(){
			$.post("ajaxkeys.php",
				{
					"page": $page,
					orderby: $orderBy,
					orderway: $orderWay,
					filter: $filter,
					filterBy: $filter_by,
					original_pagesNum : <?php echo $pages_count; ?>,
					filter_new : $filter_new ? 1 : 0
				},
				function(data, textStatus, jqXHR){
					$("#portals_list_view").html(data).fadeTo(200,1);
				}
			);
		});

	}
$('document').ready(function() {
	$(".pagination").each(function(){
		$(this).children("li").each(function(){
			$(this).click(function(e){
				e.preventDefault();
				$dpage = $(this).data('page');
				if($dpage == $page) return;
				if($dpage =='prev' && $page ==1) return;
				if($dpage =='next' && $page ==$pagesNum) return;
				$(".pagination").each(function(){
					$(this).children("li").eq($page).removeClass("active");
				});


				if($dpage == 'prev'){
					$page -= 1;
					if($page < 1) $page =1;

				} else if($dpage =='next'){
					$page +=1;
					if($page > $pagesNum) {
						$page = $pagesNum;
					}
				} else {
					$page = $dpage;
					$(this).addClass('active');
				}
				$(".pagination").each(function(){
					$(this).children("li").eq($page).addClass("active");
				});


					sendRequest();
			});
		});
	});


	$("#sort").change(function(){
		var val = $(this).val();

		var splited = val.split(":");

		$orderBy = splited[0];
		$orderWay = splited[1];

		sendRequest();

	});

	$("#filter_value").keypress(function(e) {
		if(e.which == 13) {

			$filter = $("#filter_value").val();
			$filter_by = $("#filter_value").data("filter");
			$page =1;
			sendRequest();
		}
	});
	$("#filter").click(function(){
		$filter = $("#filter_value").val();
		$filter_by = $("#filter_value").data("filter");
		$page =1;
		if($filter !='')
		sendRequest();
	});

	$("#filter_by_name").on('click',function(e){

		$("#filter_value").attr("placeholder", "Filter by name").data("filter","name");
		$(this).parent().addClass('active');
		$("#filter_by_address").parent().removeClass('active');
		e.preventDefault();
	});
	$("#filter_by_address").on('click',function(e){

		$("#filter_value").attr("placeholder", "Filter by address").data("filter","address");
		$(this).parent().addClass('active');
		$("#filter_by_name").parent().removeClass('active');
		e.preventDefault();
	});

	$("#filter_new").on('click',function(){
		$filter_new = ($(this).is(':checked'));
		sendRequest();
	});
	$("#reset").click(function(){
		$filter = '';
		$page =1;
		sendRequest();
	});

	sendRequest();
});
</script>
<?php

 } else if($view =='map'){ ?>
 <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAIZy_IkcPcdC-YluFC-CJYFnz_Kb9p_6k&sensor=true">
  </script>
 <script type="text/javascript"  src="js/infobubble.js?<?php echo _VERSION_; ?>"> </script>
<script type="text/javascript" src="js/markerclusterer.js"></script>
	<script type="text/javascript">
		var keys = <?php echo json_encode($keys); ?>;
		google.maps.visualRefresh = true;
		var markers = [];
		var infoWindows = {};
		var position = {coords: {latitude: 0,longitude: 0}};

		function getLocation()
		  {
		  if (navigator.geolocation)
			{
			navigator.geolocation.getCurrentPosition(initialize);
			}
			else{initialize(position,2)}
		  }

      function initialize(position, zooom) {
		  if(zooom ==undefined) zooom = 13;
        var mapOptions = {
          center: new google.maps.LatLng(position.coords.latitude, position.coords.longitude),
          zoom: zooom,
           styles: [{featureType:"all", elementType:"all", stylers:[{visibility:"on"}, {hue:"#131c1c"}, {saturation:"-50"}, {invert_lightness:true}]}, {featureType:"water", elementType:"all", stylers:[{visibility:"on"}, {hue:"#172e2e"}]}, {featureType:"poi", stylers:[{visibility:"off"}]}, {featureType:"transit", elementType:"all", stylers:[{visibility:"off"}]}],
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById("map-canvas"),
            mapOptions);
			
			
	
		for(var k in keys){
			
			var point = new google.maps.LatLng(keys[k].location.lat,keys[k].location.long);
			var marker = createMarker(point,map, keys[k],k);

		}
		
		var markerCluster = new MarkerClusterer(map, markers, {

			gridSize: 20

		});
      }
      google.maps.event.addDomListener(window, 'load', getLocation);
	  
		
		 
		
    
function createMarker(latlng,map, key,guid) {
    var contentString = '<div class="og-loading-wrap"><div class="og-loading"></div></div>';
	    var marker = new google.maps.Marker({
	        position: latlng,
	        map: map,
			icon: 'img/portal_marker.png',
			shadow: 'img/portal_shadow.png',
	        zIndex: Math.round(latlng.lat()*-100000)<<5
	        });

	var infoBubble = new InfoBubble({
		  map:map,
		  position: new google.maps.LatLng(key.location.lat,key.location.long),
		  shadowStyle: 1,
		  padding: 0,
          backgroundColor: '#333',
          borderRadius: 2,
          arrowSize: 10,
          borderWidth: 0,
          borderColor: '#ccc',
          disableAutoPan: false,
		  
          arrowPosition: 50,
          backgroundClassName: 'portal_map_details',
          arrowStyle: 0
		 
	  });
    google.maps.event.addListener(marker, 'click', function() {
		
		
		infoBubble.open(map,marker);
		infoBubble.setContent(contentString);
		
		 getPortalInfo(guid,key.timestamp,infoBubble, key); 
        
		});
	markers.push(marker);
	return marker;
}

	  
    </script>
 <div id="map-canvas" ></div>

<?php } ?>
