<?php
/*
Plugin Name: Wordpress Hebrew Date
Plugin URI: http://hatul.info/hebdate/
Description: Convert dates in wordpress to Hebrew dates.
Version: 1.2
Author: Hatul
Author URI: http://hatul.info
License: GPL http://www.gnu.org/copyleft/gpl.html
*/

function hebDate($sDate) {
  # Returns string of Hebrew Date by hebdate_lang
  if (strpos($sDate,':')!=false&&get_option('hebdate_sunset')==1) $sDate=sunset($sDate);
  $sGregorianDate = mysql2date('m-d-Y', $sDate);
  list ($mon, $day, $year) = explode('-', $sGregorianDate);
  $juldate=gregoriantojd($mon, $day, $year);
  $sHebDate = jdtojewish($juldate, get_option(hebdate_lang)=='hebrew', CAL_JEWISH_ADD_GERESHAYIM + CAL_JEWISH_ADD_ALAFIM_GERESH);
  if(get_option(hebdate_lang)=='number') return $sHebDate;
  if(get_option(hebdate_lang)=='english'){
    list($tmp,$enday,$enyear)=explode('/',$sHebDate);
    $enmon=jdmonthname($juldate, 4);
    if ($enmon=="AdarI"&&hasLeapYear($juldate)) $enmon='Adar A';
    elseif ($enmon=="AdarI"&&!hasLeapYear($juldate)) $enmon='Adar';
    elseif ($enmon=="AdarII") $enmon='Adar B';
    return $enday.' '.$enmon.' '.$enyear;
  }
  $sHebDate = iconv("windows-1255", "UTF-8", $sHebDate);
  $sHebDate=str_replace("'אדר ב","אדרב", $sHebDate);
  list($sJewDay,$sJewMonth,$sJewYear)=explode(' ',$sHebDate);
  if ($sJewMonth=="חשון") $sJewMonth="מרחשון";
  if ($sJewMonth=="אדר"&&hasLeapYear($juldate)) $sJewMonth='אדר א׳';
  if ($sJewMonth=="אדרב") $sJewMonth="אדר ב׳";
  $sJewMonth="ב".$sJewMonth;
  if (get_option('hebdate_hide_alafim')==1) $sJewYear= str_replace("ה'",'',$sJewYear);
  $sHebDate=$sJewDay.' '.$sJewMonth.' '.$sJewYear;
  $sHebDate=str_replace('"','״',$sHebDate);
  $sHebDate=str_replace('\'','׳',$sHebDate);
  return $sHebDate;
}

function the_hebDate($content) {
  #return the hebrew date of post
  if ((strpos($content,' ')==false&&strpos($content,'-')==false&&strpos($content,'/')==false&&strpos($content,'.')==false)||strpos($content,':')!=false) return $content;
  global $post;
  $date=$post->post_date;
  if($date=='') return $content; //if this draft not return hebrew date
  return format(hebdate_format(),hebDate($date),mysql2date(get_option('date_format'),$date));
}
//formating Hebrew date by $str
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
  //list($date, $time ) = explode( ' ', $today );
  echo(hebdate($today));
}
//include("admin.php");

function hebdate_options() {
  #admin page
	$example='1-4-2007';
	?><div class="wrap">
	<h2><?php _e('Hebrew date options','hebdate')?></h2>
	<p><form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="4HTHWS3LGDDPJ">
		<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
		<img alt="" border="0" src="https://www.paypalobjects.com/he_IL/i/scr/pixel.gif" width="1" height="1">
		</form>
<?php _e('Please donate to me so I can continue developing this plugin','hebdate')?></p>
	<form method="post" action="options.php">
	 <?php settings_fields( 'hebdate_settings' ); ?>
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
	<input type="radio" name="hebdate_format" value="custom" id="hebdate_format_custom_radio" <?php if(get_option('hebdate_format')=='custom') echo('checked="checked"'); ?>/><?php _e('Custom:')?> 
<input type="text" name="hebdate_format_custom" value="<?php echo(hebdate_format());?>" onfocus="hebdate_format_custom_radio.checked=true" size="10" dir="ltr"/> <?php echo(format(hebdate_format(),hebdate($example),mysql2date(get_option('date_format'), $example)));?><br/>
	<p><?php _e('Use "heb" for Hebrew date and "greg" for Gregorian date. Click &#8220;Save Changes&#8221; to update sample output.','hebdate')?><br/>
	<?php printf(__('The Gregorian date format able to change in %s.','hebdate'),'<a href="options-general.php">'.__('General Settings').'</a>');?></p>
	</td>
	</tr>
	<tr valign="top">
	<th></th>
	<td>
	<input type="checkbox" name="hebdate_hide_alafim" value="1" <?php if(get_option('hebdate_hide_alafim')==1) echo('checked="checked"'); ?>/><?php _e('Hide the letter of Alafim','hebdate');?>
	</td></tr>
	<tr valign="top">
	<th scope="row"><?php _e('Hebrew date language','hebdate')?></th>
	<td>
	<input type="radio" name="hebdate_lang" value="hebrew" <?php if(get_option('hebdate_lang')=='hebrew') echo('checked="checked"'); ?>/><?php _e('Hebrew','hebdate')?><br/>
	<input type="radio" name="hebdate_lang" value="english" <?php if(get_option('hebdate_lang')=='english') echo('checked="checked"');?> /><?php _e('English','hebdate')?><br/>
	<input type="radio" name="hebdate_lang" value="number" <?php if(get_option('hebdate_lang')=='number') echo('checked="checked"');?> /><?php _e('Number','hebdate')?><br/>
	</td>
	</tr>
	<tr valign="top">
	<th scope="row"><?php _e('Sunset','hebdate')?></th>
	<td>
	<input type="checkbox" name="hebdate_sunset" value="1" id="sunset" <?php if(get_option('hebdate_sunset')==1) echo('checked="checked"'); ?>/><?php _e('Transfer hebrew date at sunset','hebdate');?>  – 
	<?php _e('latitude','hebdate')?>: <input type="text" name="latitude" value="<?php echo get_option('latitude')?>" onfocus="sunset.checked=true" size="6">
	<?php _e('longitude','hebdate')?>: <input type="text" name="longitude" value="<?php echo get_option('longitude')?>" onfocus="sunset.checked=true" size="6">
	<p><?php printf(__('You can to find the longitude and the latitude via %s, via %s or via %s.','hebdate'),
		'<a href="http://maps.google.com/">'.__('Google maps','hebdate').'</a>',
			'<a href="http://whatsmylatlng.com/">whatsmylatlng</a>',
			'<a href="http://www.batchgeo.com/lookup/">batchgeo</a>')?></p>
	</td>
	<tr valign="top">
	<th scope="row"><?php _e('Current Hebrew date','hebdate')?></th>
	<td>	
	<?php printf(__('if you want to add the hebrew date of today than you need to add %s in theme code where you want. ','hebdate'),'<code>&lrm;&lt;?php today_hebdate() ?&gt;&lrm;</code>')?>
	</td></tr>
	</tr>
	</table>
	<p class="submit">
    	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
   	</p>
	</form>
	</div><?php 
}

//get Julian date and return true if its happen in leap hebrew year
function hasLeapYear($juldate) {
	$hebdate=jdtojewish($juldate);
	list($tmp1,$tmp2,$hebyear)=explode('/',$hebdate);
	if (jewishtojd(6, 1, $hebyear)!=jewishtojd(7, 1, $hebyear))
		return true;
	else return false;
}
//if value is empty init its to default
function init_hebdate(){
  if(get_option('hebdate_lang')=='') update_option('hebdate_lang', 'hebrew' );
  if(hebdate_format()=='') update_option('hebdate_format', 'heb (greg)' );
  if(get_option('latitude')=='') update_option('latitude', '31.776804' );
  if(get_option('longitude')=='') update_option('longitude', '35.222282' );
}
//if the time after the sunset return tommrow
function sunset($date){
	$date=mysql2date("H-i-s-j-n-Y",$date);
	list($hour,$min,$sec,$day,$mon,$year)=explode('-',$date);
	$sunset=date_sunset(mktime($hour,$min,0,$day,$mon,$year),SUNFUNCS_RET_STRING,
	get_option('latitude'),get_option('longitude'));
	list($sunset_h,$sunset_m)=explode(':',$sunset);
	if ($hour>$sunset_h||($hour==$sunset_h&&$min>$sunset_m)){
		$date=gregoriantojd($mon,$day,$year)+1;
		$date=jdtogregorian($date);
		list($mon,$day,$year)=explode('/',$date);
	}
	$date=$hour.':'.$min.':'.$sec.' '.$year.'-'.$mon.'-'.$day;
	return $date;	
}
// add options to menu
function hebdate_admin() {
  add_options_page(__('Hebrew Date Options',hebdate),__('Hebrew Date',hebdate), 'manage_options', 'wordpress-hebrew-date', 'hebdate_options');
  add_action( 'admin_init', 'register_settings' );	
}
// register settings
function register_settings(){
  $settings=array('hebdate_lang','hebdate_format','hebdate_format_custom','hebdate_hide_alafim','hebdate_sunset','latitude','longitude');
  foreach ($settings as $setting)
  	register_setting('hebdate_settings',$setting);
}

add_action('admin_menu', 'hebdate_admin');
load_plugin_textdomain('hebdate', false, dirname( plugin_basename( __FILE__ ) ) );
register_activation_hook(__FILE__, 'init_hebdate');

add_filter('get_comment_date', 'comment_hebDate');
add_filter('the_date', 'the_hebdate');
add_filter('get_the_time', 'the_hebdate');
add_filter('get_the_date', 'the_hebdate');
add_filter('comment_time', 'comment_hebdate');
?>
