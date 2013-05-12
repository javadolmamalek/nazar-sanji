<?php
require_once dirname(__FILE__) . '/class/setting.php';
$setting = new setting();

if( !$setting->login_cheack() )
  header("Location: login");
	
if( !isset( $_SESSION ) )
	session_start();

if( isset( $_POST['code_id'] ) )
{
	if( $setting->edit() )
		$setting->suc();
}

$result = mysql_fetch_array( mysql_query( "SELECT name FROM user WHERE id='".$_SESSION['user_id']."'" ) );
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>تنظیمات</title>
	<link rel="stylesheet" href="css/style.css" type="text/css">
</head>
<body>
	<div class="loginbackl1"><?php echo $setting->errormsg(); ?></div>
	<div class="loginbackl2">
		<center>
			<table border="0px" width="430px">
				<form name="setting" method="post" action="setting">
					<input type="hidden" name="code_id" value="<?php echo $setting->randomcode(); ?>">
					<tr>
						<td style="text-align: right;">نام :</td>
						<td style="text-align: left;"><input type="text" name="name" value="<?php echo $result['name'] ?>" class="textstyle" style="width:200px;direction:rtl;"></td>
					</tr>
					<tr>
						<td style="text-align: right;">رمز عبور فعلی :</td>
						<td style="text-align: left;"><input type="password" name="pass1" value="" class="textstyle" style="width:200px;direction:ltr;"></td>
					</tr>
					<tr>
						<td style="text-align: right;">رمز عبور جدید :</td>
						<td style="text-align: left;"><input type="password" name="pass2" value="" class="textstyle" style="width:200px;direction:ltr;"></td>
					</tr>
					<tr>
						<td style="text-align: right;">تکرار رمز عبور جدید :</td>
						<td style="text-align: left;"><input type="password" name="pass3" value="" class="textstyle" style="width:200px;direction:ltr;"></td>
					</tr>
					<tr>
						<td></td>
						<td style="text-align: left;"><input type="submit" name="send" value="ذخیره"  class="button" style="width:100px;"></td>
					</tr>
				</form>
			</table>	
		</center>
	</div>
	<p>
	<div class="loginbackl2">
		<center>
			<a href="home">بازگشت به صفحه اصلی</a>
		</center>
	</div>
</body>
</html>
