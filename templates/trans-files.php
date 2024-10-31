<script src="<?php echo WPT_PLUGIN_URL ?>/templates/js/messages.js" type="text/javascript"></script>
<link href="<?php echo WPT_PLUGIN_URL ?>/templates/styles.css" rel="stylesheet" type="text/css" />
<div class="wpt_page_content">
	<div class="wpt_success"></div>
	<div class="wpt_error"></div>

	<h2>Website</h2>
	<form method="POST">
		<input type="submit" name="reset_transitions" value="Reset All Page Transitions"/>
		<input type="submit" name="remove_ah_files" value="Remove All Page Transitions Files"/>
	</form>
	<div class="wpt_button_hr"></div>

	<!--Posts and Pages-->
	<h2>Posts and Pages</h2>
	<form method="POST">
		<input type="submit" value="Update home page transition" name="wpt_reset_home"/>
		<input type="submit" value="Update page transitions for all posts and pages" name="wpt_reset_all_posts"/>
	</form>
	<div class="wpt_button_hr"></div>

	<!--One category-->
	<h2>Categories</h2>
	<form method="POST">
		<table>
			<tr>
				<td>Select category to update</td>
				<td>
					<?php
					$args = array(
						'show_option_all' => 'All',
						'orderby' => 'name',
						'hierarchical' => 1,
						'selected' => $_POST['wpt_cat'],
						'name' => 'wpt_cat');
					wp_dropdown_categories($args);
					?>
				</td>
			</tr>
			<tr>
				<td>There are</td>
				<td><input type="text" size="3" name="wpt_posts_per_page" value="<?php echo intval(get_option('posts_per_page')) ?>"/> posts per page</td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="Generate category page transitions" name="wpt_reset_cat"/></td>
			</tr>
		</table>
	</form>
	<div class="wpt_button_hr"></div>

	<!--Tags-->
	<h2>Tags</h2>
	<form method="POST">
		<table>
			<tr>
				<td>Select tags to update:</td>
				<td>
					<select name="wpt_tag">
						<option value="-1">All</option> 
						<?php
						$tags = get_tags();
						$cur = intval($_POST['wpt_tag']);
						foreach ($tags as $tag) {
							print '<option value="'.$tag->term_id.'" '.($cur == $tag->term_id? 'selected': '').'>'.($tag->name).'</option>';							
						}						
						?>
					</select>
				</td>
			</tr>			
			<tr>
				<td colspan="2"><input type="submit" value="Generate page transitions for the tags" name="wpt_reset_tags"/></td>
			</tr>
		</table>
	</form>
	<div class="wpt_button_hr"></div>

	<!--Archives-->
	<h2>Archives</h2>
	<form method="POST">
		<table>
			<tr>
				<td>Select archives to update:</td>
				<td>
					<select name="wpt_archives">
						<option value="-1">All</option> 
						<?php wp_get_archives('format=option'); ?>
					</select>
				</td>
			</tr>			
			<tr>
				<td colspan="2"><input type="submit" value="Generate page transitions for the archives" name="wpt_reset_archives"/></td>
			</tr>
		</table>
	</form>
	<div class="wpt_button_hr"></div>
	
	<!--Authors-->
	<h2>Authors</h2>
	<form method="POST">
		<table>
			<tr>
				<td>Select author to update:</td>
				<td>
					<?php
					$args = array(
						'show_option_all' => 'All',
						'selected' => $_POST['wpt_author'],
						'name' => 'wpt_author');
					wp_dropdown_users($args);
					?>
				</td>
			</tr>			
			<tr>
				<td colspan="2"><input type="submit" value="Generate author page transitions" name="wpt_reset_author"/></td>
			</tr>
		</table>
	</form>
	<div class="wpt_button_hr"></div>

	<!--Custom url-->
	<h2>Custom URL</h2>
	<form method="POST">
		<table>
			<tr>
				<td>Please enter URL to update:</td>
				<td><input type="text" name="wpt_custom_url"/></td>
			</tr>
			<tr>
				<td>It has</td>
				<td><input type="text" size="3" name="wpt_num_pages" value="1"/> pages <i style="font-size:13px">(leave 1 or blank if the page does not have pagination)</i></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="Generate page transitions for this URL" name="wpt_reset_url"/></td>
			</tr>
		</table>
	</form>

	<?php
	if ($wpt_success_msg) {
		?>
		<script>wpt_success("<?php echo $wpt_success_msg ?>", true)</script>
		<?php
	} else if ($wpt_error_msg) {
		?>
		<script>wpt_error("<?php echo htmlspecialchars($wpt_error_msg) ?>")</script>
		<?php
	}
	?>
</div>