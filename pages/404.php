<?php

function _page($site) {


$html = "<h3>No such file or directory</h3>
<div style='font-size:14px;'>
Sorry, the page you requested <b>".$_SERVER['REQUEST_URI']."</b> doesn't exist, or has been moved. <br>
<br>
If you aren't redirected to the main site in a few seconds, click <a href='/'>here</a>.

<script>

setTimeout ( function () {
document.location.href='/';
}, 5000 );

</script>
</div>

";

return $html;


}
