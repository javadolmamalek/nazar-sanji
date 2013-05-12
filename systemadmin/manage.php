<?php
require_once dirname(__FILE__) . '/class/setting.php';
$setting = new setting();

if( !$setting->login_cheack() )
  header("Location: login");
	
if( !isset( $_SESSION ) )
	session_start();
	
$result = mysql_fetch_array( mysql_query( "SELECT name,mode FROM user WHERE id='".$_SESSION['user_id']."'" ) );

$err='';

if( isset( $_POST['save'] ) )
{
	$db = mysql_query("SELECT * FROM question ORDER BY id DESC");
	while($row = mysql_fetch_array($db))
	{
		$name = 'st-'.$row['id'];
		if ( isset( $_POST[$name] ) )
			mysql_query("UPDATE question SET status = '1' WHERE id = ".$row['id']."");
		else
			mysql_query("UPDATE question SET status = '0' WHERE id = ".$row['id']."");
	}
	$err = '<div class="sendstyle">نظرسنجی ها با موفقیت فعال/غیرفعال شدند</div><p>';
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>مدیریت نظرسنجی ها</title>
	<link rel="stylesheet" href="css/style.css" type="text/css">
</head>
<body>
	<div class="topback">
		<b>خوش آمدی <?php echo $result['name'] ?> !</b> | <?php if( $result['mode'] == '1' ){ ?><a href="users">کاربران سیستم</a><?php } ?> | <a href="setting">تنظیمات</a> | <a href="login?action=logout">خروج</a>
	</div>
	<p><div class="menuback">
		<a href="home">صفحه اصلی</a><p>
		<a href="group">تعریف و مدیریت گروه ها</a><p>
		<a href="create">ساخت نظرسنجی</a><p>
		» مدیریت نظرسنجی ها<p>
		<a href="result">تماشای نتایج</a>
	</div>
	<div class="titlebackl1">
		<div class="titlebackl2">
		<?php echo $err ?>
		در این قسمت لیست نظر سنجی ها را می بینید. برای فعال یا غیر فعال کردن نظرسنجی ای خاص از قسمت وضعیت استفاده کنید. با غیر فعال کردن یک نظرسنجی آمار آن از بین نمی رود و همیشه در دسترس است و تنها در صورتی از بین می رود که نظر سنجی را پاک کنید. پس از مشخص کردن وضعیت نظرسنجی باید حتمن روی گزینه ی تایید کلیک کنید تا تغییرات اعمال شوند.
		<p>
		برای تغییر سوال و یا گروه و یا حالت نظر سنجی از گزینه ی ویرایش استفاده کنید. همچنین برای تغییر گزینه های یک نظرسنجی از قسمت گزینه استفاده کنید.
		<p>
			<center>
				<form name="save" method="post" action="manage">
					<table border="1px" width="500px">
						<tr>
							<td style="text-align: right;">وضعیت</td>
							<td style="text-align: right;">سوال</td>
							<td style="text-align: right;">گروه</td>
							<td style="text-align: right;">اختیارات</td>
						</tr>
						<?php
						function gro( $var )
						{
							$res = mysql_fetch_array( mysql_query("SELECT name FROM `group` WHERE id=".$var."") );
							return $res['name'];
						}
						function cheack( $var )
						{
							$res = mysql_fetch_array( mysql_query("SELECT status FROM question WHERE id=".$var."") );
							if( $res['status'] == 1 )
								return 'checked';
							else
								return '';
						}
						$db = mysql_query("SELECT * FROM question ORDER BY id DESC");
						if(!$db || mysql_num_rows($db) <= 0)
							echo '<td style="text-align: right;">خالیست!</td><td style="text-align: right;">خالیست!</td><td style="text-align: right;">خالیست!</td><td style="text-align: right;">خالیست!</td>';
						while($row = mysql_fetch_array($db))
							{
								echo '<tr><td style="text-align: right;"><input type="checkbox" name="st-'.$row['id'].'" '.cheack($row['id']).' value="'.$row['id'].'"></td>
								<td style="text-align: right;">'.$row['question'].'</td>
								<td style="text-align: right;">'.gro($row['group']).'</td>
								<td style="text-align: right;"><a href="action?ac=edit&cat=question&id='.$row['id'].'">ویرایش</a>|<a href="action?ac=delete&cat=question&id='.$row['id'].'">حذف</a>|<a href="create?id='.$row['id'].'">گزینه</a>|<a href="stats?action=all&id='.$row['id'].'">آمار</a></td></tr>';
							}
						?>
					</table><p>
					<input type="submit" name="save" value="تایید"  class="button" style="width:100px;">
				</form>
			</center>
		</div><p>
	</div>
</body>
</html>
