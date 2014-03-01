
	<div  class="footer col-sm-6 col-sm-offset-3">

		<?php
		if(isset($_SESSION['logged']) AND $_SESSION['logged'] ){
			if(isset($last_sync))
			echo 'Last sync: '.$last_sync.' | ';
		?>

		<a href="index.php?forcesync" title="it may take a wile">Force sync</a>
		|
		<a href="index.php?logout">Log out</a>
		<?php } ?>
		<br /><a href="InventoryAuth_v1.0.apk" title="Inventory Authenticaton Code generator">Auth app</a>
		<p class="text-danger">
			This site is no NOT officially affiliated with Niantic or Google inc.<br />
			It comes with no warranty. Use at your own risk!
		</p>
	</div>
</div>
</body>
</html>