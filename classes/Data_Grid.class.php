<?php

/*
$grid = new Data_Grid();
$grid->query("insert into users values('', ?, ?, ?, NOW(), NOW() )", array( 'test', 'test@davdrive.com', 'password') );

// print_r( $grid->rows('select username, password from users') );
print_r( $grid->cone('select username, password from users') );
print_r( $grid->cones('select * from users') );

// print_r($grid->rows("show tables like ? ", array('%user%')));
*/



class Data_Grid {

  public  $mysql       = array();
  private $user        = 'japh';
  private $password    = 'japh';
  private $database    = 'somedb';
  private $host        = '';
  private $driver      = 'mysql';
  private $grid        = array();
  private $fetch_mode  = "numeric";
  public static $link  = null;


  public function __construct ( $args = array()  ) {

    if (!$this->link) {
      try {
      $this->link = new PDO(
        "$this->driver:host=$this->host;dbname=$this->database;charset=utf8", 
          $this->user, 
          $this->password
        );
      }
      catch ( PDOException $e ) {
      die($e->getMessage());
      }
    }

  }


  public function cone ( $query, $query_args=array() ) {
  $last_mode = $this->fetch_mode ;
  $this->fetch_mode = "associative";
  $this->query($query, $query_args );
  $this->fetch_mode = $last_mode;
  
  $cone = array();

    foreach ($this->grid[0] as $k => $v) {
      if ( is_numeric($k) ) {
      continue;
      }
    $cone[$k] = $v; 
    }

  return $cone;
  }

  public function cones ( $query, $query_args=array() ) {
  $last_mode = $this->fetch_mode ;
  $this->fetch_mode = "associative";
  $this->query($query, $query_args );
  $this->fetch_mode = $last_mode;
  
  $cones = array();

    foreach ( $this->grid  as $row ) {
    $cone = array();
      foreach ( $row as $k => $v ) {
        if ( is_numeric($k) ) {
        continue;
        }
        $cone[$k] = $v; 
      }
    $cones[] = $cone;
    }

  return $cones;
  }



  public function row ( $query, $query_args=array() ) {
  $this->query($query, $query_args );
  return $this->grid[0];
  }

  public function rows ( $query, $query_args=array() ) {
  $this->query($query, $query_args);
  return $this->grid;
  }

  public function cols ( $query, $query_args=array() ) {
  $cols = array();
  $this->query( $query, $query_args );

    foreach ( $this->grid as $row ) {
      for( $i=0; $i<sizeof($row); $i++ ) {
      $cols[$i][] = $row[$i];
      }
    }

    return $cols;
  }

  public function col ($query, $query_args=array() ) {
  $col = array();
  $this->query( $query, $query_args );

    foreach ( $this->grid as $row ) {
    $col[] = $row[0];
    }

  return $col;
  }

  public function scalar ( $query, $query_args=array() ) {
  $this->query($query, $query_args );
    
  return $this->grid[0][0];
  }

  public function debug () {

  $error = "<h3>Mysql:</h3><br>\n";
  $error .= $this->mysql['query'] . "\n";
  $error .= print_r( $this->mysql['query_args'], true) . "\n";
  $error .= $this->error;
  return $error;
  }






  protected function __mysql_query( $query, $query_args=array() ) {

    if ( ! $this->link ) {
    die("can't connect to DB");
    }

    try {
    $result = $this->link->prepare( $query );

      if ($this->fetch_mode == "associative") {
      $result->setFetchMode(PDO::FETCH_COLUMN);
      }
      else {
      $result->setFetchMode(PDO::FETCH_NUM);
      }

    }
    catch ( PDOException $e ) {
    print $this->debug();
    die($e->getMessage());
    }

    if ( ! $result->execute( $query_args ) ) {
    $this->error = print_r($result->errorInfo(), true);
    die($this->debug());
    }


   $this->grid = array();

      foreach ($result as $row) {
      $this->grid[] =  $row;
      }

  }

  public function query ($query, $query_args =array()) {
  $this->grid = array();

  switch($this->driver) {

    case "mysql":
    $this->mysql['query'] = $query ;
    $this->mysql['query_args'] = $query_args ;
    $this->__mysql_query($query, $query_args);
    break;
    }

  }

}

?>
