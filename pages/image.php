<?

function _page($site) {

$cache_key = md5($_SERVER['REQUEST_URI']) . ".png";

list ($uri, $external_file)  = explode("?", $_SERVER['REQUEST_URI']);

$parts = explode("/", $uri);

array_shift($parts);
array_shift($parts);

$geometry = array_shift($parts);
$filters = $parts;


$end = sizeof($parts)-1;
$file = preg_replace("/[^a-z0-9\-\.]+$/", "", $parts[$end]);



$external_file = preg_replace("|^/|", "", $external_file);
$external_file = preg_replace("/[^a-z0-9\-\.\/]+$/", "", $external_file);

if ($external_file) {
$input_file = $external_file;

  if ( filemtime("cache/$cache_key") < filemtime($external_file) ) {
  unlink("cache/$cache_key");
  }

}
else {
$input_file = "media/images/$file";
}

if ($file == "render.svg") {
$is_svg = true;
}

if ( !file_exists($input_file)  ) {
header("HTTP/1.1 404 Not Found");
print "File not found $input_file";
exit;
}

if (!preg_match("/^\d+x\d+$/i", $geometry) ) {
header("HTTP/1.1 404 Not Found");
print "Invalid parameter geometry=$geometry";
exit;
}




/*
if ($is_svg) {
header("Content-type: image/svg+xml");
print <<<EOT
<?xml version="1.0" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<svg viewBox = "0 0 1100 400" version = "1.1">
    <desc>
        Filter example
    </desc>
    <filter id = "i1">
        <feDiffuseLighting result = "diffOut" in = "SourceGraphic" diffuseConstant = "1.2" lighting-color = "white">
            <fePointLight x = "400" y = "400" z = "150" pointsAtX = "0" pointsAtY = "0" pointsAtZ = "0"/>
        </feDiffuseLighting>
        <feComposite in = "SourceGraphic" in2 = "diffOut" operator = "arithmetic" k1 = "1" k2 = "0" k3 = "0" k4 = "0"/>
    </filter>
    <g stroke = "tomato" fill = "peru" filter = "url(#i1)">
        <rect x = "10%" y = "10%" width = "40%" height = "40%"/>
        <rect x = "55%" y = "10%" width = "40%" height = "40%"/>
        <rect x = "10%" y = "55%" width = "40%" height = "40%"/>
        <rect x = "55%" y = "55%" width = "40%" height = "40%"/>
    </g>
</svg>
EOT;
exit;
}
*/


    # $gray = " -colorspace HSB";
  if ( !file_exists("cache/$cache_key")  ) {
    if (in_array("gray", $filters) ) {
    $gray = " -colorspace gray ";
    }
    if (in_array("invert", $filters) ) {
    $negate = " -negate ";
    }
    foreach ($filters as $filter) {
      if (preg_match("/^shift\-(\d+)\-(\d+)\-(\d+)/", $filter, $matches) ) {
      list ($undef, $contrast, $saturation, $hue) = $matches;
      $modulate = " -modulate $contrast $saturation $hue";
      }

      if (preg_match("/^hue\-([a-f0-9]+)\-([a-f0-9]+)\-(\d+)/", $filter, $matches) ) {
      list ($undef, $from, $to, $fuzz) = $matches;
      $hue = " -fuzz $fuzz%  -fill '#$to'  -opaque '#$from' ";
      }

      if (preg_match("/^sharpen\-(\d+)/", $filter, $matches) ) {
      list ($undef, $factor) = $matches;
      $sharpen = " -sharpen 0x$factor ";
      }

      if (preg_match("/^blur\-(\d+)x(\d+)/", $filter, $matches) ) {
      list ($undef, $x, $y) = $matches;
      $sharpen = " -blur $x"."x".$y;
      }

      if (preg_match("/^equalize/", $filter, $matches) ) {
      $eq = " -equalize ";
      }

      if (preg_match("/^trim/", $filter, $matches) ) {
      $trim  = " -trim ";
      }

      if (preg_match("/^crop\-(\d+)x(\d+)-(\d+)-(\d+)/", $filter, $matches) ) {
      list ($undef, $x, $y, $a, $b) = $matches;
      $crop = " -crop $x"."x".$y."+$a+$b";
      }

    }
  $cmd    = "convert -geometry $geometry $crop $trim $eq $sharpen $hue $modulate $negate $gray '$input_file' 'cache/$cache_key'";
  $output = `$cmd`;
  }


/*
*/
$data = file_get_contents("cache/$cache_key");
//--- caching
// is this working ?
 $last_modified  = filemtime("cache/$cache_key");
  $modified_since = ( isset( $_SERVER["HTTP_IF_MODIFIED_SINCE"] ) ? strtotime( $_SERVER["HTTP_IF_MODIFIED_SINCE"] ) : false );
  $etagHeader     = ( isset( $_SERVER["HTTP_IF_NONE_MATCH"] ) ? trim( $_SERVER["HTTP_IF_NONE_MATCH"] ) : false );

  // This is the actual output from this file (in your case the xml data)
  // generate the etag from your output
  $etag     = sprintf( '"%s-%s"', $last_modified, md5( $data) );

  //set last-modified header
  header( "Last-Modified: ".gmdate( "D, d M Y H:i:s", $last_modified )." GMT" );
  //set etag-header
  header( "Etag: ".$etag );

  // if last modified date is same as "HTTP_IF_MODIFIED_SINCE", send 304 then exit
  if ( (int)$modified_since === (int)$last_modified && $etag === $etagHeader ) {
    header( "HTTP/1.1 304 Not Modified" );
    exit;
  }
  else {
header("HTTP/1.1 200 OK");
}
header("Content-type: image/png");

//--- caching
print $data;
// header("Location: /cache/$cacke_key");


// fputs( fopen("cache/debug", "a"), print_r("$cmd $output", true) );
exit;


}

?>
