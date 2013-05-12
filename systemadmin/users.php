<?php
require_once dirname(__FILE__) . '/class/setting.php';
$setting = new setting();

if( !$setting->login_cheack() )
  header("Location: login");
	
$result = mysql_fetch_array( mysql_query( "SELECT mode FROM user WHERE id='".$_SESSION['user_id']."'" ) );

if( $result['mode'] != '1' )
	header("Location: home");

if( isset( $_POST['code_id'] ) )
{
	if( $setting->register() )
		$setting->suc();
}

if( isset( $_POST['id'] ) )
{
	if( $setting->delete() )
		$setting->suc();
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>مدیریت کاربران</title>
	<link rel="stylesheet" href="css/style.css" type="text/css">
</head>
<body>
	<div class="loginbackl1"><?php echo $setting->errormsg(); ?></div>
	<div class="loginbackl2">
		<center>
			<table border="0px" width="430px">
				<form name="user" method="post" action="users">
					<input type="hidden" name="code_id" value="<?php echo $setting->randomcode(); ?>">
					<tr>
						<td style="text-align: right;">نام :</td>
						<td style="text-align: left;"><input type="text" name="name" value="" class="textstyle" style="width:200px;direction:rtl;"></td>
					</tr>
					<tr>
						<td style="text-align: right;">نام کاربری :</td>
						<td style="text-align: left;"><input type="text" name="user" value="" class="textstyle" style="width:200px;direction:ltr;"></td>
					</tr>
					<tr>
						<td style="text-align: right;">رمز عبور :</td>
						<td style="text-align: left;"><input type="password" name="pass" value="" class="textstyle" style="width:200px;direction:ltr;"></td>
					</tr>
					<tr>
						<td style="text-align: right; ">نوع کاربر :</td>
						<td style="text-align: left;">
							<input type="radio" name="k" value="2" checked />کاربر عادی
							<input type="radio" name="k" value="1"  />مدیر
						</td>
					</tr>
					<tr>
						<td></td>
						<td style="text-align: left;"><input type="submit" name="send" value="ثبت"  class="button" style="width:100px;"></td>
					</tr>
				</form>
			</table>	
		</center>
	</div><p>
	<div class="loginbackl2">
		<center>
			<table border="1px" width="480px">
				<tr>
					<td style="text-align: right;">نام</td>
					<td style="text-align: right;">نام کاربری</td>
					<td style="text-align: right;">دسترسی</td>
					<td style="text-align: right;">اختیارات</td>
				</tr>
				<?php			
				$db = mysql_query("SELECT * FROM user ORDER BY id DESC");
				while($row = mysql_fetch_array($db))
				{
					echo '
					<form name="user" method="post" action="users" onsubmit="return confirm (';echo"'آیا مطمئنید ؟'";echo')">
					<tr><td style="text-align: right;">'.$row['name'].'</td><td style="text-align: right;">'.$row['username'].'</td>
					<td style="text-align: right;">';
					if( $row['mode'] == '1' )
						echo 'مدیر';
					if( $row['mode'] == '2' )
						echo 'کاربر عادی';
					echo '</td>
					<td style="text-align: center;"><input type="hidden" name="id" value="'.$row['id'].'"><input type="submit" name="send" value="حذف"  class="button" style="width:100px;"></td></tr></form>';
				}
				?>
			</table>
		</center>
	</div><p>
	<div class="loginbackl2">
		<center>
			<a href="home">بازگشت به صفحه اصلی</a>
		</center>
	</div>
</body>
</html>
