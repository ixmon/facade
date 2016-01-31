<?php


ini_set("error_reporting", E_ERROR);
date_default_timezone_set("America/New_York");

$pages= array(
  ''                        => "splash.php",
  '/about'                  => "about.php",
  '/images'                 => "image.php",
  '/ajax/test'              => "test.php",
  '/login'                  => "login.php",

);

$no_auth = array(
  'images',
);

require_once("classes/Site.class.php");
require_once("classes/User.class.php");

$site = new site();
$user = new User();


  if ( ! $user->is_logged_in() && ! in_array( $site->uri_parts[0] , $no_auth) ) {
  $site->parse_uri ("/login" );
  }



$page = $pages[ $site->uri_path ];
  if ( $site->is_image() ) {
  $page = "image.php";
  }


  if ( $page ) {
  $site->html = $site->load_page( $page );
  }
  else {
  $site->html = $site->load_page( '404.php');
  $site->status = 404;
  }

  if ($site->status == 404) {
  header("HTTP/1.1 404 Not Found");
  }
  else {
  header("HTTP/1.1 200 OK");
  }



  if ( $site->is_ajax() || $site->is_image()  ) { 
  $contents = $site->html;
  }
  else {
  $contents = $site->render();
  }

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

// print $contents;
$site->print_gzip($contents);
exit;

function xdebug ($str) {
die ($str);

}

?>
