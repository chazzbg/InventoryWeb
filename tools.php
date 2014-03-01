<?php
		if(getValue('tool')=='redeem'){

			require('tool_redeem.php');
		}
		if(getValue('tool') == 'recycle'){
			require('tool_recycle.php');
		}
		if(getValue('tool')=='agent'){
			require('tool_agent.php');
		}
		if(getValue('tool')=='score'){
			require('tool_score.php');
		}
	?>
