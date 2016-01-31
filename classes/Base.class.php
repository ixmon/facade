<?php

class Base {
	
public $POST = array();
public $GET  = array();
public $GPC  = array();

public $months     = array(
"January", "February", "March", "April", "May", "June", "July", "August",
"September", "October", "November", "December"
);

public $num_months = array(
"01", "02", "03", "04", "05", "06", "07", "08",
"09", "10", "11", "12");
	
  public function __construct () {
    
   
    foreach ( $_COOKIE as $k => $v ) { 
    $this->GPC[$k] = $v; 
    }

    foreach ( $_GET as $k => $v ) { 
    $this->GPC[$k] = $v; 
    }

    if ( ! sizeof($_GET) ) {
    // helps with some mod_rewrite situations
      foreach ( explode('&', $_SERVER['REQUEST_URI'] ) as $kv ) {
      list( $k, $v) = explode('=', $kv);
      $this->GPC[$k] = urldecode( $v );
      }
    }

    foreach ( $_POST as $k => $v ) { 
    $this->GPC[$k] = $v; 
    }
  
    foreach ( $this->GPC as $k => $v ) {
    $this->GPC[$k] = urldecode( $v );
    }

  }

  public function gpc ($key, $format='') {

    if ($format == "numeric" && !is_numeric($value)) {
    return false;
    }
    elseif ($format == "alphanumeric" && !$this->isAlphaNumeric($value)) {
    return false;
    }
    elseif ($format == "alphanumeric" && !$this->isAlphaNumeric2($value)) {
    return false;
    }
    elseif ($format == "humanname" && !$this->ishumanname($value)) {
    return false;
    }
    elseif ($format == "email_address" && !$this->isEmail($value)) {
    return false;
    }

  return $value;
  }

  public function menu ( $name, $ar_options, $extra ) {

    $html = "<select name='$name'  $extra  autocomplete='off' >\n";

    foreach ( $ar_options as $v ) {

      $selected ='';
      if ( is_array($v) ) {
      list ($id, $label) = $v;
        if ( 
              $_REQUEST[$name] == $id  
              || in_array($id, $_REQUEST[$name] )
            ) {
        $selected = " selected='selected' ";
        }

      $html .= "<option $selected value='$id'>$label</option>";
      }
      else {
        if ( $_REQUEST[$name] == $v  ) {
        $selected = " selected='selected' ";
        }
      $html .= "<option $selected>$v</option>";
      }

    }
  $html .= "</select>";

  return $html;

  }

  public function preserve_url () {

      foreach ( $this->GPC as $k => $v ) {
      $url .= "$k=" . urlencode($v) . '&';
      }
    return $url;
    }


  public function htmlnormalchars ($string) {
  $string = str_replace('&gt;', '>', $string) ;
  $string = str_replace('&lt;', '<', $string) ;
  $string = str_replace('&quot;', '"', $string) ;
  $string = str_replace('&amp;', '&', $string) ;
  $string = str_replace("&#039;", "'", $string) ;
  $string = str_replace("&#39;", "'", $string) ;
  return $string;
  }



  public function dynamic_color ($str) {
  return  substr( md5($str), "0","6" ) ;
  }


  public function isGoodFileName ($str) {

    if(eregi('^[A-Za-z0-9_\.-]+$', $str)){
  return true;
    }
    return false;

  }

  public function isAlphaNumeric ($str) {

    if(ereg('[^A-Za-z0-9]', $str)){
    return false;
    }
  return true;

  }

  public function isAlphaNumeric2($str) {

    if(ereg('[^A-Za-z0-9\s\-\_]', $str)){
    return false;
    }
  return true;

  }

  public function isHumanName ($str) {

    if(ereg('[^A-Za-z0-9 \.\']', $str)){
    return false;
    }
  return true;

  }

  public function isEmail ($str) {
    if (! eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $str) ) {
    return false;
    }
  return true;

  }

  public function short_string ($label, $limit=27) {

    if (strlen($label) > $limit ) {
    $label = substr($label, 0, $limit);
    $label .= "...";
    }

  return $label;
  }

  public function mkpass () {
  $vowels = 'aeiouy';
  $consonants = 'bdghjlmnpqrstvwxz';
    $consonants .= '0123456789';
  $length = 8;
  /*
    if ($strength & 1) {
    // $consonants .= 'BDGHJLMNPQRSTVWXZ';
    }
    if ($strength & 2) {
    // $vowels .= "AEIOUY";
    }
    if ($strength & 4) {
    }
    if ($strength & 8) {
    $consonants .= '@#$%^';
    }
  */

  $password = '';
  $alt = microtime() % 2;
  srand(time());

    for ($i = 0; $i < $length; $i++) {
      if ($alt == 1) {
      $password .= $consonants[(rand() % strlen($consonants))];
      $alt = 0;
      } else {
      $password .= $vowels[(rand() % strlen($vowels))];
      $alt = 1;
      }
    }
    
  return $password;                                                                                                                                                                         
  }

  public function captcha () {
  // dropped out confusing things like 0 o etc
  $vowels = 'aeuy';
  $consonants = 'bdghjmnpqrstvwxz';
    $consonants .= '23456789';
  $length = 8;
  if (@func_get_arg(0) ) {
  $length = @func_get_arg(0);
  }

  $password = '';
  $alt = microtime() % 2;
  srand(time());

    for ($i = 0; $i < $length; $i++) {
      if ($alt == 1) {
      $password .= $consonants[(rand() % strlen($consonants))];
      $alt = 0;
      } else {
      $password .= $vowels[(rand() % strlen($vowels))];
      $alt = 1;
      }
    }

  return $password;
  }

  public function xor_decrypt ($enc, $key) {


  $enc = base64_decode($enc);
    for($i=0;$i<=strlen($enc);$i++) {
    $plain .= $enc{$i} ^ $key;
    }
  return $plain;
  }

  public function xor_encrypt ($plain, $key) {

  for($i=0;$i<=strlen($plain);$i++) {
  $enc .= $plain{$i} ^ $key;
  }
  return base64_encode($enc);
  }


  public function date () {
    $date = @func_get_arg(0);
    $style = @func_get_arg(1);

    if (!$date) {
      $date = time();
    }
    if (is_numeric($date)) {
      $time = $date;
    }
    else {
      $time = strtotime($date);
    }

    if ($style == "long"){
      return date("l dS F Y h:i:s A", $time);
    }
    elseif ($style == "log"){
      return date("Y_m_d", $time);
    }
    elseif ($style == "datetime"){
      return date("Y-m-d", $time);
    }
    elseif ($style == "day"){
      return date("F j Y ", $time);
    }
    elseif ($style == "slash"){
      return date("n/j/Y ", $time);
    }
    elseif ($style == "estimate"){
      $diff = abs( time() - $time  );
      $intervals = array(
        'second' => 1,
        'minute' => 60,
        'hour'   => 60 * 60,
        'day'    => 60 * 60 * 24,
        'week'   => 60 * 60 * 24 * 7,
        'month'  => 60 * 60 * 24 * 30,
        'year'   => 60 * 60 * 24 * 365,
      );

      arsort($intervals);

        foreach ($intervals as $metric => $value ) {
        // print "$metric => $value <br>";

          if ($diff > $value ) {

            if ($diff >  $value ) {
            $metric .= 's';
            }

            return ceil( $diff / $value ) ."  $metric  ago";
          }

      }
    }

  }



}
