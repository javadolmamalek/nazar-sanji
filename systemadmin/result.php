<?php
require_once dirname(__FILE__) . '/class/setting.php';
$setting = new setting();

if( !$setting->login_cheack() )
  header("Location: login");
	
if( !isset( $_SESSION ) )
	session_start();
	
$result = mysql_fetch_array( mysql_query( "SELECT name,mode FROM user WHERE id='".$_SESSION['user_id']."'" ) );
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>نتایج نظرسنجی ها</title>
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
		<a href="manage">مدیریت نظرسنجی ها</a><p>
		» تماشای نتایج<p>
	</div>
	<div class="titlebackl1">
		<div class="titlebackl2">
			<center>
				<table border="1px" width="500px">
					<tr>
						<td style="text-align: right;">کد</td>
						<td style="text-align: right;">سوال</td>
						<td style="text-align: right;">گروه</td>
						<td style="text-align: right;">نظرات</td>
						<td style="text-align: right;">آمار</td>
					</tr>
					<?php
					function gro( $var )
					{
						$res = mysql_fetch_array( mysql_query("SELECT name FROM `group` WHERE id=".$var."") );
						return $res['name'];
					}
					function par( $var )
					{
						$res = mysql_fetch_array( mysql_query("SELECT COUNT(*) FROM participant WHERE question_id=".$var."") );
						return $res['0'];
					}
						function cheack( $var )
						{
							$res = mysql_fetch_array( mysql_query("SELECT vaz FROM nazar WHERE id=".$var."") );
							if( $res['vaz'] == 1 )
								return 'checked';
							else
								return '';
						}
					$db = mysql_query("SELECT * FROM question ORDER BY id DESC");
					if(!$db || mysql_num_rows($db) <= 0)
						echo '<td style="text-align: right;">خالیست!</td><td style="text-align: right;">خالیست!</td><td style="text-align: right;">خالیست!</td><td style="text-align: right;">خالیست!</td><td style="text-align: right;">خالیست!</td>';
					while($row = mysql_fetch_array($db))
						{
							echo '
							<tr><td style="text-align: right;">'.$row['id'].'</td>
							<td style="text-align: right;">'.$row['question'].'</td>
							<td style="text-align: right;">'.gro($row['group']).'</td>
							<td style="text-align: right;">'.par($row['id']).'</td>
							<td style="text-align: right;"><a href="stats?action=all&id='.$row['id'].'">کلی</a>|<a href="stats?action=select&id='.$row['id'].'">تفکیکی</a>';
						}
					?>
				</table>
			</center>
		</div>
	</div>
</body>
</html>
