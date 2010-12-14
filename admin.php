<?php
function hebdate_options() {
  #admin page
	$example='1-4-2007';
	?><div class="wrap">
	<h2><?php _e('Hebrew date options','hebdate')?></h2>
	<form method="post" action="options.php">
	<?php wp_nonce_field('update-options'); ?>
	<table class="form-table">
	<tr valign="top">
	<th scope="row"><?php _e('Hebrew date format','hebdate')?></th>
	<td>
	<?php
	$formats=array(
		"heb (greg)",
		"heb",
		"heb – greg",
		"greg – heb",
		);
	foreach($formats as $format){
	?>
	<input type="radio" name="hebdate_format" value="<?php echo($format) ?>" <?php if(hebdate_format()==$format) echo('checked="checked"'); ?>/><?php echo(format($format,hebdate($example),mysql2date(get_option('date_format'), $example)));?><br/><?php } ?>
	<input type="radio" name="hebdate_format" value="custom" id="hebdate_format_custom_radio" <?php if(get_option('hebdate_format')=='custom') echo('checked="checked"'); ?>/><?php _e('Custom:')?> <input type="text" name="hebdate_format_custom" value="<?php echo(hebdate_format());?>" onfocus="hebdate_format_custom_radio.checked=true" size="10" dir="ltr"/> <?php echo(format(hebdate_format(),hebdate($example),mysql2date(get_option('date_format'), $example)));?><br/>
	<p><?php _e('Use "heb" for Hebrew date and "greg" for Gregorian date. Click &#8220;Save Changes&#8221; to update sample output.','hebdate')?><br/>
	<?php printf(__('The Gregorian date format able to change in %s.','hebdate'),'<a href="options-general.php">'.__('General Settings').'</a>');?></p>
	</td>
	</tr>
	<tr valign="top">
	<th scope="row"><?php _e('Hebrew date language','hebdate')?></th>
	<td>
	<input type="radio" name="hebdate_lang" value="hebrew" <?php if(get_option('hebdate_lang')=='hebrew') echo('checked="checked"'); ?>/><?php _e('Hebrew','hebdate')?><br/>
	<input type="radio" name="hebdate_lang" value="number" <?php if(get_option('hebdate_lang')=='number') echo('checked="checked"');?> /><?php _e('Number','hebdate')?><br/>
	</td>
	</tr>
	</table>
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="hebdate_lang,hebdate_format,hebdate_format_custom" />
	<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
	</form>
	</div>
<?php }
function hebdate_admin() {
  add_options_page(__('Hebrew Date Options',hebdate),__('Hebrew Date',hebdate), 'manage_options', 'wordpress-hebrew-date', 'hebdate_options');
  init();
}
function init(){
  if(get_option('hebdate_lang')=='') update_option('hebdate_lang', 'hebrew' );
  if(hebdate_format()=='') update_option('hebdate_format', 'heb (greg)' );
  if(get_option('latitude')=='') update_option('latitude', '31.776804' );
  if(get_option('longitude')=='') update_option('longitude', '35.222282' );
}
//add_action('admin_menu', 'hebdate_admin');
?>