<?php
/*
Plugin Name: Wordpress Hebrew Date
Plugin URI: http://hatul.info/hebdate/
Description: Convert dates in wordpress to Hebrew dates.
Version: 0.1b
Author: Hatul
Author URI: http://hatul.info
License: GPL http://www.gnu.org/copyleft/gpl.html
*/

function hebDate($sDate) {
# Date format of $sGregorianDate should be 'm-d-Y'
# Returns string, somthing like:
# ג' בתשרי ה'תשס"ז
$sGregorianDate = mysql2date('m-d-Y', $sDate);
list ($sJewMonth, $sJewDay, $sJewYear) = split('-', $sGregorianDate);
$sHebDate = jdtojewish(gregoriantojd($sJewMonth, $sJewDay, $sJewYear), true,
CAL_JEWISH_ADD_GERESHAYIM + CAL_JEWISH_ADD_ALAFIM_GERESH);
$sHebDate = iconv("windows-1255", "UTF-8", $sHebDate);
list ($sJewDay,$sJewMonth, $sJewYear) = split(' ', $sHebDate);
if ($sJewMonth=="חשון") $sJewMonth="מרחשון";
$sJewMonth="ב".$sJewMonth;
$sHebDate=$sJewDay.' '.$sJewMonth.' '.$sJewYear;
return $sHebDate;
}

function the_hebDate() {
#return the hebrew date of post
global $post;
$date=$post->post_date;
$ans=hebDate($date).' ('.mysql2date('d/m/Y',$date).')';
return $ans;
}

function comment_hebDate() {
#return the hebrew date of comment
global $comment;
$date=$comment->comment_date;
$ans=hebDate($date).' ('.mysql2date('d/m/Y',$date).')';
return $ans;
}

function today_hebDate(){
echo(hebdate(date('m-d-Y')));
}

add_filter('get_comment_date', 'comment_hebDate');
add_filter('the_date', 'the_hebdate');
?>