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
	<title>نتایج</title>
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
		<?php
		if( isset( $_GET['action'] ) )
		{
			if( $_GET['action'] == 'all' )
			{
				if( isset( $_GET['id'] ) )
				{
					$result = mysql_query( "SELECT * FROM question WHERE id='".$_GET['id']."'" );
					$que = mysql_fetch_array($result);
					if(!$result || mysql_num_rows($result) <= 0)
						echo'<div class="titlebackl2"><center>چیزی یافت نشد !<center></div>';
					else
					{
						$group = mysql_fetch_array(mysql_query("SELECT name FROM `group` WHERE id='".$que['group']."'"));
						$participant = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM participant WHERE question_id='".$_GET['id']."'"));
						echo'<div class="titlebackl2">
						سوال نظرسنجی : '.$que['question'].'<p>
						رشته : '.$group['name'].'<p>
						تعداد شرکت کنندگان : '.$participant['0'].' ( <a href="stats?action=select&id='.$_GET['id'].'">نمایش آمار به تفکیک</a> )<p>
						<center><table border="1px" width="500px">
						<tr>
							<td style="text-align: right;">گزینه ها</td>
							<td style="text-align: right;">تعداد انتخاب</td>
							<td style="text-align: right;">درصد انتخاب</td>
						</tr>';
						$stats = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM stats WHERE question_id='".$_GET['id']."'"));
						$selection2 = mysql_query( "SELECT id, selection FROM selection WHERE question_id='".$_GET['id']."'" );
						while($selection = mysql_fetch_array($selection2))
						{
							echo'<tr>
							<td style="text-align: right;">'.$selection['selection'].'</td>';
							$reg = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM stats WHERE selection_id='".$selection['id']."'"));
							echo '<td style="text-align: right;"><b>'.$reg['0'].'</b></td>';
							if ( $stats['0'] != 0 )
								$percent = ( $reg['0']/$stats['0'] ) *100;
							else
								$percent = 0;
							echo'<td style="text-align: right;"><b>'.(int) $percent.' %</b></td></tr>';									
						}
						echo'</table></center></div>';
					}
				}
				else echo'<div class="titlebackl2"><center>چیزی یافت نشد !<center></div>';
			}
			elseif( $_GET['action'] == 'select' )
			{
				if( isset( $_GET['id'] ) )
				{
					$result = mysql_query( "SELECT * FROM question WHERE id='".$_GET['id']."'" );
					$que = mysql_fetch_array($result);
					if(!$result || mysql_num_rows($result) <= 0)
						echo'<div class="titlebackl2"><center>چیزی یافت نشد !<center></div>';
					else
					{
					
						$group = mysql_fetch_array(mysql_query("SELECT name FROM `group` WHERE id='".$que['group']."'"));
						$participant = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM participant WHERE question_id='".$_GET['id']."'"));
						echo'<div class="titlebackl2">
						سوال نظرسنجی : '.$que['question'].'<p>
						رشته : '.$group['name'].'<p>
						تعداد شرکت کنندگان : '.$participant['0'].' ( <a href="stats?action=all&id='.$_GET['id'].'">نمایش آمار کلی</a> )<p>
						<center><table border="1px" width="500px">
						<tr>
						<td style="text-align: right;">نام شرکت کننده</td>';
						$selection2 = mysql_query( "SELECT selection FROM selection WHERE question_id='".$_GET['id']."'" );
						while($selection = mysql_fetch_array($selection2))
							echo'<td style="text-align: right;">'.$selection['selection'].'</td>';									
						echo '</tr>';
						$participant2 = mysql_query( "SELECT name_id FROM participant WHERE question_id='".$_GET['id']."'" );
						while( $participant = mysql_fetch_array( $participant2 ) )
						{
							$num = mysql_fetch_array(mysql_query("SELECT name FROM name WHERE id='".$participant['name_id']."'"));
							echo'<tr>
							<td style="text-align: right;">'.$num['name'].'</td>';
							$selection2 = mysql_query( "SELECT id FROM selection WHERE question_id='".$_GET['id']."'" );
							while($selection = mysql_fetch_array($selection2))
							{
								$participant3 = mysql_query("SELECT * FROM stats WHERE name_id='".$participant['name_id']."' AND selection_id='".$selection['id']."'");
								if($participant3 && mysql_num_rows($participant3) > 0)
									echo '<td style="background:#0000ff"></td>';
								else
									echo '<td></td>';
							}
						}
						echo'</table></center></div>';
					}	
				}
				else echo'<div class="titlebackl2"><center>چیزی یافت نشد !<center></div>';
			}
			else echo'<div class="titlebackl2"><center>چیزی یافت نشد !<center></div>';
		}			
		else echo'<div class="titlebackl2"><center>چیزی یافت نشد !<center></div>';
		?>
		<p>
		<div class="titlebackl2">
			<center><a href="result">بازگشت به جدول نتایج</a></center>
		</div>
	</div>
</body>
</html>
