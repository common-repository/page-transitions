<?php
$urls = get_option('wpt_queue');
?>
<link href="<?php echo WPT_PLUGIN_URL ?>/templates/styles.css" rel="stylesheet" type="text/css" />
<script src="<?php echo WPT_PLUGIN_URL ?>/templates/js/app-launcher.js"></script>
<script>
	var wpt_site_url = "<?php echo bloginfo('url') ?>";
	var wpt_pass = "<?php echo htmlspecialchars(get_option('wpt_pass')) ?>";
</script>
<div class="wpt_page_content">
	<?php
	if ($urls) {
		$plural = count($urls) > 1;
		?>
		WPT plugin detected that website content has been changed and <b><?php echo count($urls) ?> url<?php echo $plural ? 's' : '' ?></b> 
		require<?php echo $plural ? '' : 's' ?> page transition<?php echo $plural ? 's' : '' ?> to be regenerated
		<br><br>

		<form method="POST">
			<div id="wpt-buttons">
				<span class="loading"><img src="<?php echo WPT_PLUGIN_URL ?>/templates/images/loader2.gif"/> Checking installed software...</span>
				<input type="submit" id="wpt_ignore_but" name="wpt_ignore" value="Disregard this notification" class=""/>
			</div>
			<p style="font-size:13px"><i>If the pre-loader image spins endlessly for you - please run <strong>Page Transitions Generator</strong> AIR application manually</i></p>
		</form>

		<script>
			var launcher;
			jQuery(document).ready(function(){
				launcher = new AppLauncher("#wpt-buttons");
				launcher.setVarName("launcher");
				launcher.embed();
			});
		</script>

	<?php } else { ?>
		There are no notifications yet
	<?php } ?>
</div>