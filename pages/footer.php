<?php

function _page_footer ( $site ) {

require_once("classes/User.class.php");

$user = new User();

$html ="
<p style='clear:both;'></p>
";

if ( $user->is_logged_in() ) {
}

$html .="
</body></html>
";

return $html;

}

?>
