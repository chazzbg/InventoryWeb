<?php 
			set_time_limit(0);
			$items = array(API::ITEM_RESONATOR,API::ITEM_BURSTER, API::ITEM_SHIELD,API::ITEM_FORCE_AMP, API::ITEM_LINK_AMP, API::ITEM_HEATSINK, API::ITEM_MULTIHACK, API::ITEM_TURRET);
			$errors = array();
			$recycled = 0;
			if(isSubmit('recycle')){
				if(getValue('type') ==""){
					$errors[] = 'You must select item to recycle!';
				} else if(!isset($items[getValue('type')])){
					$errors[] = 'Invalid recycle item!';
				}
				
				if(in_array(getValue('type'),array(0,1)) AND ((int)getValue('level') <1 OR (int)  getValue('level')>8))
					$errors[] = 'Invalid item level!';
				if(in_array(getValue('type'),array(2,3,4,5,6,7)) AND ((int)getValue('rarity') <1 OR (int)  getValue('rarity')>3))
					$errors[] = 'Invalid item rarity!';
				
				if((int) getValue('count')<=0){
					$errors[] = 'You must specify how much items to recycle!';
				}
				
				
				if(!count($errors)){
					$api = new API();
					$api->setSACSID($_SESSION['sacsid']);
					if($api->handshake()){
						$collected = $api->getItemsGuids($items[(int)  getValue('type')], (getValue('type')<2? (int)  getValue('level'): (int)  getValue('rarity')), (int)  getValue('count'));
						foreach ($collected as $itemGuid){
							if($api->recycleItem($itemGuid))
								$recycled++;
							sleep(1);
						}
						
						if((int)  getValue('count') == $recycled){

							echo '<div class="alert alert-info col-md-6 col-md-offset-3"> <p class="text-center">Selected items were recycled!</p></div>';
						} else {
							$errors[] = 'Not all of the selected items were recycled!';
						}
					} else {
						$errors[] = 'Expired Auth code!';
					}
					
					
				}
				
				if(count($errors))
					showErrors ($errors);
			}
	?>
<div class="row">
	<div class="col-md-4 col-md-offset-4">
		<div class="row">
			<div class="col-sm-md-12">
				<p>Do not recycle many items at once, it will take a lot of time!</p>
				<p>Between each recycle is added a 1 second wait to emulate a normal scanner behavior!</p>
				<p>Recycled items won't come back!</p>
			</div>
		</div>
		<form role="form" method="post">
			<div class="form-group">
				<label>Item to recycle</label>
				<select name="type" id="type" class="form-control">

					<?php
					foreach ($items as $k => $i){
						echo '
							<option value="'.$k.'" '.(getValue('type',-1)==$k? 'selected' :'').'>'.friendly_name($i).'</option>';
					}
					?>
				</select>
			</div>
			<div class="form-group" id="items_level">
				<label>Item level</label>
				<select name="level" class="form-control">
					<option value="1" class="level_1" <?php if(getValue('level') == 1) echo 'selected' ; ?> >Level 1</option>
					<option value="2" class="level_2" <?php if(getValue('level') == 2) echo 'selected' ; ?> >Level 2</option>
					<option value="3" class="level_3" <?php if(getValue('level') == 3) echo 'selected' ; ?> >Level 3</option>
					<option value="4" class="level_4" <?php if(getValue('level') == 4) echo 'selected' ; ?> >Level 4</option>
					<option value="5" class="level_5" <?php if(getValue('level') == 5) echo 'selected' ; ?> >Level 5</option>
					<option value="6" class="level_6" <?php if(getValue('level') == 6) echo 'selected' ; ?> >Level 6</option>
					<option value="7" class="level_7" <?php if(getValue('level') == 7) echo 'selected' ; ?> >Level 7</option>
					<option value="8" class="level_8" <?php if(getValue('level') == 8) echo 'selected' ; ?> >Level 8</option>
				</select>
			</div>
			<div class="form-group" id="items_rarity">
				<label>Item rarity</label>
				<select name="rarity" class="form-control">
					<option value="1" class="common" <?php if(getValue('rarity') == 1) echo 'selected' ; ?> >Common</option>
					<option value="2" class="rare" <?php if(getValue('rarity') == 2) echo 'selected' ; ?> >Rare</option>
					<option value="3" class="very_rare" <?php if(getValue('rarity') == 3) echo 'selected' ; ?> >Very rare</option>
				</select>
			</div>
			<div class="form-group">
				<label for="count">How many to recycle</label>

				<input type="text"  class="form-control" id="count" placeholder="Count" name="count" value="<?php echo getValue('count'); ?>" >

			</div>
			<div class="form-group">

				<button type="submit" name="recycle" class="btn btn-default col-md-12 col-xs-12">Recycle!</button>

			</div>
		</form>
	</div>
</div>


	<script type="text/javascript">
		$("#type").change(function (){
			if($(this).val() == 0 || $(this).val() ==1){
				$("#items_level").slideDown(500);
				$("#items_rarity").slideUp(500);
			} else {
				$("#items_level").slideUp(500);
				$("#items_rarity").slideDown(500);
			}
				
			
		});
		$("#type").change();


	</script>
