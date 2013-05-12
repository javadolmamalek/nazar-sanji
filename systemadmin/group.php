<?php
require_once dirname(__FILE__) . '/class/group.php';
$group = new group();

require_once dirname(__FILE__) . '/class/setting.php';
$setting = new setting();

if( !$setting->login_cheack() )
  header("Location: login");

if( !isset( $_SESSION ) )
	session_start();

$result = mysql_fetch_array( mysql_query( "SELECT name,mode FROM user WHERE id='".$_SESSION['user_id']."'" ) );

if( isset( $_POST['code_id'] ) )
{
	if( $group->register() )
		$group->suc();
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>مدیریت گروه ها</title>
	<link rel="stylesheet" href="css/style.css" type="text/css">
</head>
<body>
	<div class="topback">
		<b>خوش آمدی <?php echo $result['name'] ?> !</b> | <?php if( $result['mode'] == '1' ){ ?><a href="users">کاربران سیستم</a><?php } ?> | <a href="setting">تنظیمات</a> | <a href="login?action=logout">خروج</a>
	</div>
	<p><div class="menuback">
		<a href="home">صفحه اصلی</a><p>
		» تعریف و مدیریت گروه ها<p>
		<a href="create">ساخت نظرسنجی</a><p>
		<a href="manage">مدیریت نظرسنجی ها</a><p>
		<a href="result">تماشای نتایج</a><p>
	</div>
	<div class="titlebackl1">
		<div class="titlebackl2">
		<?php echo $group->errormsg(); ?>
		عنوان گروه را وارد کنید و روی دکمه ی ایجاد کلیک کنید تا گروه ساخته شود :
		<p>
			<center>
				<table border="0px" width="430px">
					<form name="group" method="post" action="group">
						<input type="hidden" name="code_id" value="<?php echo $group->randomcode(); ?>">
						<tr>
							<td style="text-align: right;">عنوان گروه :</td>
							<td style="text-align: left;"><input type="text" name="name" value=""  class="textstyle" style="width:200px;direction:rtl;"></td>
						</tr>
						<tr>
							<td></td>
							<td style="text-align: left;"><input type="submit" name="send" value="ایجاد"  class="button" style="width:100px;"></td>
						</tr>
					</form>
				</table>
			</center>
		</div><p>
		<div class="titlebackl2">
			<center>
				<table border="1px" width="480px">
					<tr>
						<td style="text-align: right;">کد گروه</td>
						<td style="text-align: right;">عنوان گروه</td>
						<td style="text-align: right;">اختیارات</td>
					</tr>
					<?php			
					$db = mysql_query("SELECT * FROM `group` ORDER BY id DESC");
					if(!$db || mysql_num_rows($db) <= 0)
						echo '<td style="text-align: right;">خالیست!</td><td style="text-align: right;">خالیست!</td><td style="text-align: right;">خالیست!</td>';
					while($row = mysql_fetch_array($db))
						{
							echo '<tr><td style="text-align: right;">'.$row['id'].'</td><td style="text-align: right;">'.$row['name'].'</td><td style="text-align: right;"><a href="action?ac=edit&cat=group&id='.$row['id'].'">ویرایش</a>|<a href="action?ac=delete&cat=group&id='.$row['id'].'">حذف</a></td></tr>';
						}
					?>
				</table>
			</center>
		</div><p>
	</div>
</body>
</html>
