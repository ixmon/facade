<?php


function _page_header ($site) {
require_once('classes/User.class.php');
// $GLOBALS['user'] = new user();
$user = new User();
$user->load();


$html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'><head>
<meta charset='utf-8' /> 
    <title>".$site->title."</title>
      <link rel='stylesheet' href='/dist/build.css'>
    <script type='text/javascript' src='/dist/build.js' /></script>
</head>

<body id='bod'>

      <h1 onClick='location.href=\"/\";'/> </h1>

";

return $html;

}

?>