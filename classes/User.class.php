<?php


require_once("classes/Site.class.php");
require_once("classes/Data_Grid.class.php");

class User extends Data_Grid {

public $username      = '';
public $email         = '';
public $password      = '';
public $last_mod      = '';
public $creation_date = '';

  public function __construct ( $id='' ) {
  parent::__construct();

    if (is_numeric($id)) {
    $this->load($id);
    }
  
  }

  public function load($id) {
    if ($id) {
    $this->id = $id;
    }
    elseif ($this->is_logged_in()) {
    $this->id = intval($this->scrub($_COOKIE['uid'], 100));
    }

  list($this->username, $this->email, $this->password, $this->last_mod, $this->creation_date ) 
      = $this->row("select username,email,password,last_mod, creation_date from users where id=? limit 1", array( $this->id ));
  }

  public function check_password ($possible_password) {
  $possible_password = $this->scrub($possible_password, 100);
  return $this->scalar("select count(*) from users where id='$this->id' and password='$possible_password' limit 1");
  }

  public function exists ($username) {
  $username = str_replace("'", "", $username);
  $count = intval( $this->scalar("select count(*) from users where username=? limit 1"), array($username) );
  return $count;
  }

  public function email_exists ($email) {
  $email = str_replace("'", "", $email);
  $count = intval( $this->scalar("select count(*) from users where email=? limit 1"), array($email) );
  return $count;
  }

  public function scrub($str, $max_len=32) {
  $str = trim($str);
  $str = substr($str,0, $max_len);
  return $str;
  }

  public function create ( $username, $email, $password ) {
  $username = $this->scrub($username);
  $password = $this->scrub($password);
  $email    = $this->scrub($email);
  
  $this->query("
  insert into users 
  (id, username, email, password, last_mod, creation_date)
  values('', ?, ?, ?, NOW(), NOW() )
  ", array( $username, $email, $password) );
  
  }

  public function id($email, $password) {
  $email = $this->scrub($email);

  $username = $this->scrub($username);
  $password = $this->scrub($password);

  // because @ is always in email, and never in username, this doesn't cut the number of brute force attempts to hack by half
  if (preg_match("/@/", $email) ) {
  $id = $this->scalar("select id from users where email=? and password=? limit 1", array( $email, $password) );
  }
  else {
  $id = $this->scalar("select id from users where username=? and password=? limit 1", array($email, $password));
  }

  if ($id) {
  return $id;
  }

  return false; 
  }

  public function id_hash($id) {
  $id = intval($id);
    if (!$id) {
    return false;
    }
  list ($id, $password) = $this->row("select id, password from users where id=?", array($id) );
  $ah = md5("$id:$password:" . site::SECRET_PHRASE);
  return $ah;

  }

  public function is_logged_in() {
  $id = intval($this->scrub($_COOKIE['uid']));
  $ah = $this->scrub($_COOKIE['ah']);

    if (!$id || !$ah) {
    return false;
    }


    if ($ah == $this->id_hash($id) ) {
    return true;
    }

  return false;
  }



  public function set_email ( $email ) {
  if (!$this->is_logged_in()) { return array(); }
  if (!$this->id) { $this->load(); }

  $email = $this->scrub(urldecode($email), 100);
  if (!$this->email_exists($email) ) {
  $this->query("update users set email=? where id=? limit 1", array($email, $this->id) );
  }
  }

  public function set_password ($password) {
  if (!$this->is_logged_in()) { return array(); }
  if (!$this->id) { $this->load(); }

  $password = $this->scrub(urldecode($password), 100);

  $this->query("update users set password=? where id=? limit 1", array( $password, $this->id ) );
  }



}

/*
$u = new User();
$u->create ('test2', 'test2@davdirv.eomc', 'password2');

print_r($u);
*/


?>
