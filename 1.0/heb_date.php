<?php
/*
Plugin Name: Wordpress Hebrew Date
Plugin URI: http://hatul.info/hebdate/
Description: Convert dates in wordpress to Hebrew dates.
Version: 1.0
Author: Hatul
Author URI: http://hatul.info
License: GPL http://www.gnu.org/copyleft/gpl.html
*/

function hebDate($sDate) {
  # Date format of $sDate should be 'm-d-Y'
  # Returns string, something like:
  # ג' בתשרי ה'תשס"ז
  $sGregorianDate = mysql2date('m-d-Y', $sDate);
  list ($sJewMonth, $sJewDay, $sJewYear) = split('-', $sGregorianDate);
  $lang=get_option(hebdate_lang)==hebrew;
  $sHebDate = jdtojewish(gregoriantojd($sJewMonth, $sJewDay, $sJewYear), $lang, CAL_JEWISH_ADD_GERESHAYIM + CAL_JEWISH_ADD_ALAFIM_GERESH);
  if(!$lang) return $sHebDate;
  $sHebDate = iconv("windows-1255", "UTF-8", $sHebDate);
  list ($sJewDay,$sJewMonth, $sJewYear) = split(' ', $sHebDate);
  if ($sJewMonth=="חשון") $sJewMonth="מרחשון";
  $sJewMonth="ב".$sJewMonth;
  $sHebDate=$sJewDay.' '.$sJewMonth.' '.$sJewYear;
  $sHebDate=str_replace('"','״',$sHebDate);
  $sHebDate=str_replace('\'','׳',$sHebDate);
  return $sHebDate;
}

function the_hebDate($content) {
  #return the hebrew date of post
  if (strpos($content,' ')==false&&strpos($content,'-')==false&&strpos($content,'/')==false) return $content;
  global $post;
  $date=$post->post_date;
  if($date=='') return $content; //if this draft not return hebrew date
  return format(hebdate_format(),hebDate($date),mysql2date(get_option('date_format'),$date));
}

function format($str,$heb,$greg){
  $str=str_replace('heb',$heb,$str);
  $str=str_replace('greg',$greg,$str);
  return $str;
}

function hebdate_format(){
  #return hebdate_format. if it is custom return hebdate_format_custom.
  $format=get_option('hebdate_format');
  if($format!='custom') return $format;
  else return get_option('hebdate_format_custom');
}

function comment_hebDate($content) {
  #return the hebrew date of comment
  if (strpos($content,':')!=false) return $content;
  global $comment;
  $date=$comment->comment_date;
  return format(hebdate_format(),hebDate($date),mysql2date(get_option('date_format'),$date));
}

function today_hebDate(){
  #print hebrew date of today
  $today=current_time('mysql');
  list($date, $time ) = split( ' ', $today );
  echo(hebdate($date));
}

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
	</div><?php
}

function init(){
  if(get_option('hebdate_lang')=='') update_option('hebdate_lang', 'hebrew' );
  if(hebdate_format()=='') update_option('hebdate_format', 'heb (greg)' );

}

function hebdate_admin() {
  add_options_page(__('Hebrew Date Options',hebdate),__('Hebrew Date',hebdate), 'manage_options', 'wordpress-hebrew-date', 'hebdate_options');
  init();
}

add_action('admin_menu', 'hebdate_admin');
load_plugin_textdomain('hebdate', false, dirname( plugin_basename( __FILE__ ) ) );

add_filter('get_comment_date', 'comment_hebDate');
add_filter('the_date', 'the_hebdate');
add_filter('get_the_time', 'the_hebdate');
add_filter('get_the_date', 'the_hebdate');
add_filter('comment_time', 'comment_hebdate');
?>
