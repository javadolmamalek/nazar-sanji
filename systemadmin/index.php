<?php
require_once dirname(__FILE__) . '/class/setting.php';
$setting = new setting();

if( !$setting->login_cheack() )
  header("Location: login");

if( !isset( $_SESSION ) )
	session_start();

$result = mysql_fetch_array( mysql_query( "SELECT name,mode FROM user WHERE id='".$_SESSION['user_id']."'" ) );

$all1 = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM `group`"));
$all2 = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM question"));
$all3 = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM participant"));
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>سیستم نظرسنجی آنلاین</title>
	<link rel="stylesheet" href="css/style.css" type="text/css">
</head>
<body>
	<div class="topback">
		<b>خوش آمدی <?php echo $result['name'] ?> !</b> | <?php if( $result['mode'] == '1' ){ ?><a href="users">کاربران سیستم</a><?php } ?> | <a href="setting">تنظیمات</a> | <a href="login?action=logout">خروج</a>
	</div>
	<p><div class="menuback">
		» صفحه اصلی<p>
		<a href="group">تعریف و مدیریت گروه ها</a><p>
		<a href="create">ساخت نظرسنجی</a><p>
		<a href="manage">مدیریت نظرسنجی ها</a><p>
		<a href="result">تماشای نتایج</a>
	</div>
	<div class="titlebackl1">
		<div class="titlebackl2">
			تعداد کل گروه ها : <?php echo $all1['0'] ?><p>
			تعداد کل نظرسنجی ها : <?php echo $all2['0'] ?><p>
			تعداد کل شرکت کننده ها : <?php echo $all3['0'] ?><p>
		</div>
	</div>
</body>
</html>
