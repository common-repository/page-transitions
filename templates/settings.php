<style>
#wpt_txt_colorpicker, #wpt_box_colorpicker, #wpt_bg_colorpicker {display: none}
</style>
<script src="<?php echo WPT_PLUGIN_URL ?>/templates/js/messages.js" type="text/javascript"></script>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery("#wpt_txt_colorpicker").farbtastic("#wpt_txt_color");
		jQuery("#wpt_box_colorpicker").farbtastic("#wpt_box_color");
		jQuery("#wpt_bg_colorpicker").farbtastic("#wpt_bg_color");
		
		jQuery("#wpt_txt_color, #wpt_box_color, #wpt_bg_color").focus(
			function(){
				jQuery("#" + this.id + "picker").slideDown()
			}
		).blur(
			function(){
				jQuery("#" + this.id + "picker").slideUp()
			}	
		);
	});
</script>
<link href="<?php echo WPT_PLUGIN_URL ?>/templates/styles.css" rel="stylesheet" type="text/css" />
<div class="wpt_page_content">
	<div class="wpt_success"></div>

	<!--General-->
	<h2>General</h2>
	<form method="POST">
		<?php if (get_option('wpt_off')) { ?>
			<b class="wpt_red">Page transitions are turned off</b>
			<input type="submit" name="wpt_transitions_on" value="Turn ON Page Transitions" />
		<?php } else { ?>
			<input type="submit" name="wpt_transitions_off" value="Turn OFF Page Transitions" onclick="return confirm('Are you sure you want to turn off the page transitions?')" />
		<?php } ?>
	</form>
	<div class="wpt_button_hr"></div>

	<!--SELECT WISHED TRANSITIONS-->
	<h2>Page Transitions</h2>
	Select page transitions which you want to be used on your website<br><br>
	<form method="POST">
		<?php
		global $wpt_transitions;
		for ($i = 0; $i < count($wpt_transitions); $i++) {
			?>
			<input type="checkbox" name="wpt_used_transitions[]" value="<?php print $wpt_transitions[$i][0] ?>" <?php print wpt_is_transition_on($wpt_transitions[$i][0]) ? 'checked' : '' ?> />
			<?php print $wpt_transitions[$i][1] ?>
			<br/>
		<?php } ?>
		<br />
		<input type="submit" name="save_transitions" value="Save Changes"/>
	</form>
	<div class="wpt_button_hr"></div>

	<!--SCREENSHOT SETTINGS-->	
	<h2>Miscellaneous Settings</h2>	
	<form method="POST">

		<div class="wpt_field_title">Website width</div>
		<input type="text" name="wpt_page_w" value="<?php echo intval(get_option('wpt_page_w')) ?>" class="wpt_small_field"/><br>

		<div class="wpt_field_title">Page screenshot height</div>
		<input type="text" name="wpt_page_h" value="<?php echo intval(get_option('wpt_page_h')) ?>" class="wpt_small_field"/><br>

		<div class="wpt_field_title">Image quality</div>
		<select name="wpt_quality" class="wpt_small_field">
			<?php
			$quality = intval(get_option('wpt_quality'));
			for ($i = 100; $i >= 50; $i-=5) {
				print '<option value="' . $i . '" ' . ($i == $quality ? 'selected' : '') . '>' . $i . '</option>';
			}
			?>
		</select>
		<br />
		<input type="submit" name="save_settings" value="Save Changes"/>
	</form>
	<div class="wpt_button_hr"></div>

	<!--COLORS-->
	<h2>Styling</h2>

	<form method="POST">
		
		<h4>Preloader</h4>
		
		<div class="wpt_field_title">Font</div>
		<?php 
		$font = get_option('wpt_font');
		$font = $font? $font: 'Arial';
		?>
		<input type="text" id="wpt_font" name="wpt_font" value="<?php echo htmlspecialchars($font) ?>" /><br>
		
		<div class="wpt_field_title">Bold</div>
		<input type="checkbox" value="1" name="wpt_bold" <?php echo get_option('wpt_bold') == '0'? '': 'checked' ?>/><br>
		
		<div class="wpt_field_title">Text size</div>
		<select name="wpt_font_size">
		<?php
		$selected = intval(get_option('wpt_font_size'));
		$selected = $selected? $selected: 14;
		for($i = 7; $i < 21; $i++){
			print '<option value="'.$i.'" '.($selected == $i? 'selected': '').'>'.$i.'</option>';
		}
		?>
		</select> px<br>
		
		<div class="wpt_field_title">Text color</div>
		<?php 
		$color = get_option('wpt_txt_color');
		$color = $color? $color: '#ffffff';
		?>
		<input type="text" id="wpt_txt_color" name="wpt_txt_color" value="<?php echo htmlspecialchars($color) ?>" /><br>
		<div id="wpt_txt_colorpicker"></div>
		
		<div class="wpt_field_title">Box color</div>
		<?php 
		$color = get_option('wpt_box_color');
		$color = $color? $color: '#181C18';
		?>
		<input type="text" id="wpt_box_color" name="wpt_box_color" value="<?php echo htmlspecialchars($color) ?>" /><br>
		<div id="wpt_box_colorpicker"></div>
		
		<h4>Page</h4>
		<div class="wpt_field_title">Background color</div>
		<?php 
		$color = get_option('wpt_bg_color');
		$color = $color? $color: '#ffffff';
		?>
		<input type="text" id="wpt_bg_color" name="wpt_bg_color" value="<?php echo htmlspecialchars($color) ?>" /><br>
		<div id="wpt_bg_colorpicker"></div>
				
		<br>
		<input type="submit" name="wpt_save_styles" value="Save changes"/>
	</form>
	<div class="wpt_button_hr"></div>
	
	<!--AIR APP PASSWORD-->
	<h2>AIR application</h2>

	<form method="POST">
		<div class="wpt_field_title_small">WP URL:</div><strong><?php bloginfo('url') ?></strong><br>
		<div class="wpt_field_title_small">Password:</div><strong><?php echo htmlspecialchars(get_option('wpt_pass')) ?></strong><br>
		<input type="submit" name="new_pass" value="Generate new password"/>
	</form>

	<?php
	if ($success) {
		?>
		<script>wpt_success("Settings have been saved successfully", true)</script>		
		<?php
	}
	?>
</div>