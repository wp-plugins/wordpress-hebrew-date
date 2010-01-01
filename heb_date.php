<?php
/*
Plugin Name: Wordpress Hebrew Date
Plugin URI: http://hatul.info/hebdate/
Description: Convert dates in wordpress to Hebrew dates.
Version: 0.3.2
Author: Hatul
Author URI: http://hatul.info
License: GPL http://www.gnu.org/copyleft/gpl.html
*/

function hebDate($sDate) {
  # Date format of $sDate should be 'm-d-Y'
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

function the_hebDate($content) {
  #return the hebrew date of post
   if (strpos($content,' ')==false&&strpos($content,'-')==false&&strpos($content,'/')==false) return $content;
  global $post;
  $date=$post->post_date;
  if($date=='') return $content; //if this draft not return hebrew date
  $ans=hebDate($date).' ('.$content.')';
  return $ans;
}

function comment_hebDate($content) {
  #return the hebrew date of comment
  if (strpos($content,':')!=false) return $content;
  global $comment;
  $date=$comment->comment_date;
  $ans=hebDate($date).' ('.$content.')';
  return $ans;
}

function today_hebDate(){
  #print hebrew date of today
  $today=current_time('mysql');
  list($date, $time ) = split( ' ', $today );
  echo(hebdate($date));
}

add_filter('get_comment_date', 'comment_hebDate');
add_filter('the_date', 'the_hebdate');
add_filter('get_the_time', 'the_hebdate');
?>
