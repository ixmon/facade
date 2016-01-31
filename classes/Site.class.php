<?php

require_once("classes/Base.class.php");

class Site extends Base {

public $html      = '';
public $status    = 200;
public $title     = "Some Title";
public $uri_parts = array();
public $uri_path  = '';
public $preloaded_images = array();

const SECRET_PHRASE =   '215eQ(*JNUP{}#@VmIijdkjf8ybbbgx222'; 


  public function __construct () {
  parent::__construct();
  $this->parse_uri();
  }

  public function load_page($page) {
  require_once("pages/$page");
  return _page($this) ;
  }

  public function render () {
  require_once("pages/header.php"); 
  require_once("pages/footer.php"); 

  $this->html = _page_header($this) . $this->html . _page_footer($this);

  return $this->html;
  }



  public function print_gzip($contents) {

    function gzip_PrintFourChars($Val) { 
      for ($i = 0; $i < 4; $i ++) {
      echo chr($Val % 256);
      $Val = floor($Val / 256);
      }
    }

    if( ! preg_match('/gzip, deflate/i',$_SERVER[HTTP_ACCEPT_ENCODING] )){
    echo $contents;
    exit;    
    }

  header("Content-Encoding: gzip");

  echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
  $Size = strlen($contents);
  $Crc = crc32($contents);
  $contents = gzcompress($contents, 9);
  $contents = substr($contents, 0, strlen($contents) - 4); 
  echo $contents;

  gzip_PrintFourChars($Crc);
  gzip_PrintFourChars($Size);
  }

  public function is_ajax () {

    if ( preg_match("|^/ajax|", $_SERVER['REQUEST_URI']) ) {
    return true;
    }

  return false;
  }

  public function is_image() {

    if ( preg_match("|^/images|", $_SERVER['REQUEST_URI']) ) {
    return true;
    }

  return false;
  }

  public function parse_uri ( $request_uri='' ) {

    if ( ! $request_uri ) { 
    $request_uri = $_SERVER['REQUEST_URI'];
    }

  $uri = preg_replace("/\?.*/", "",  $request_uri );
  $this->uri_parts = preg_split("|/|", $uri);
  array_shift($this->uri_parts);

  $path = preg_replace("/\?.*/", "", $request_uri );
  $this->uri_path = preg_replace("|/$|", "", $path);

  }


}
?>
