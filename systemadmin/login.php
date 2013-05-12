<?php
require_once dirname(__FILE__) . '/class/setting.php';
$setting = new setting();

if( !isset( $_SESSION ) )
  	session_start();
if( isset( $_GET['action']	) )
{
	if( $_GET['action']=='logout' )
		session_destroy();
}

if( $setting->login_cheack() )
	header("Location: home");

if( isset( $_POST['login_id'] ) )
	{	
		if( $setting->login() ) 
			header("Location: home");
	}		
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>ورود به سامانه</title>
	<link rel="stylesheet" href="css/style.css" type="text/css">
</head>
<body>
	<div class="loginbackl1"><?php echo $setting->errormsg(); ?></div>
	<div class="loginbackl2">
		<center>
			<form name="login" method="post" action="login">
				<input type="hidden" name="login_id" value="<?php echo $setting->randomcode(); ?>">
				<table border="0px" width="400px">
					<tr>
						<td>نام کاربری :</td>
						<td><input type="text" name="username" value=""  class="textstyle" style="width:200px;direction:ltr;"></td>
					</tr>
					<tr>
						<td>رمز عبور :</td>
						<td><input type="password" name="password" value=""  class="textstyle" style="width:200px;direction:ltr;"></td>
					</tr>
					<tr>
						<td></td>
						<td><input type="submit" name="send" value="ورود"  class="textstyle" style="width:100px;"></td>
					</tr>
				</table>
			</form>
		</center>
	</div>
</body>
</html>
