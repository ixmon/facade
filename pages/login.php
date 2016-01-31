<?php


function _page($site) {

require_once("classes/User.class.php");

$user = new user();



if ($site->gpc("active") ) {
$email    = $site->gpc("email");
$username = $site->gpc("username");
$password = $site->gpc("password");
$captcha  = $site->gpc("captcha");
$captcha_solution  = $site->gpc("captcha_solution");
$id = $user->id($email, $password);

if (!$email) {
$error_email = "required";
$css_email   = "err-lite";
}
elseif (!$password) {
$error_password= "required";
$css_password = "err-lite";
}
elseif (!$id ) {
$error_login = "Invalid Credentials";
$css_login   = "err-lite";
}
else {
// setcookie("uid", $id);
// setcookie("ah", md5("$username:$password:$id") ) ;
$ah = md5("$id:$password:" . site::SECRET_PHRASE);
$success = true;

$js = "
<script type='text/javascript'>

set_cookie('uid', '$id', 365);
set_cookie('ah', '$ah', 365);

location.href='/';
</script>
";


}



}
else {
$js = "
<div id='post-ajax-callback' style='display:none;'>
</div>

";
}



$body = "
<div style='width:400px;margin:0 auto 0 auto;padding-top:50px;min-height:500px;font-size:14px;color:#555;'>
  <h2 style='color:#555;padding-bottom:5px; border-bottom:1px solid #ccc;'>Please Login</h2>
  <form method='post' action='/login' id='login' name='login' >
  <table class='regular-table' style='margin-left:50px;'>
  <tr><td class='b ' >Username</td><td> <input type='text' id='email' name='email' value='$email' > </td><td class='err-lite' >$error_email</td></tr>
  <tr><td class='b ' >Password</td><td> <input type='password' name='password' value='$password'  > </td><td class='err-lite' >$error_password</td></tr>
  <tr><td class='' colspan='2' style='color:#999;'>Remember me on this device &nbsp; <input  type='checkbox' name='persist'  checked /> </td></tr>
  <tr><td class='b err-lite'  colspan='2'> $error_login &nbsp;  <input class='pointer btn btn-info ' style='float:right;' type='submit' name='active' value='Login'  > </td><td>&nbsp;</td></tr>
  </table>
</form>

</div>

";

if (!$success) {
setcookie("uid", "");
setcookie("ah", "" ) ;
}


$html = "
<div id='sub-dialog' class='curvy login' >
$js
$body
</div>
";






return $html;


}

?>
