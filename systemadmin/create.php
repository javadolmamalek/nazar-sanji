<?php
require_once dirname(__FILE__) . '/class/create.php';
$create = new create();

require_once dirname(__FILE__) . '/class/setting.php';
$setting = new setting();

if( !$setting->login_cheack() )
  header("Location: login");
	
if( !isset( $_SESSION ) )
	session_start();
	
$result = mysql_fetch_array( mysql_query( "SELECT name,mode FROM user WHERE id='".$_SESSION['user_id']."'" ) );

if( isset( $_POST['code_id'] ) )
{
	if( $create->register1() )
	{
		if( !isset( $_SESSION ) )
			session_start();
		$row = mysql_fetch_array( mysql_query("SELECT id FROM question WHERE id=".$_SESSION['question_code']."") );
		$add = 'create?id='.$row['id'];
		$_SESSION['question_code'] = '';
		header("Location: $add");
	}
}

if( isset( $_POST['selection_id'] ) )
{
	if( $create->register2() )
		$create->suc();
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>ساخت نظرسنجی</title>
	<link rel="stylesheet" href="css/style.css" type="text/css">
</head>
<body>
	<div class="topback">
		<b>خوش آمدی <?php echo $result['name'] ?> !</b> | <?php if( $result['mode'] == '1' ){ ?><a href="users">کاربران سیستم</a><?php } ?> | <a href="setting">تنظیمات</a> | <a href="login?action=logout">خروج</a>
	</div><p>
	<div class="menuback">
		<a href="home">صفحه اصلی</a><p>
		<a href="group">تعریف و مدیریت گروه ها</a><p>
		» ساخت نظرسنجی<p>
		<a href="manage">مدیریت نظرسنجی ها</a><p>
		<a href="result">تماشای نتایج</a>
	</div>
	<div class="titlebackl1">
		<?php
		$db = mysql_query("SELECT * FROM `group`");
		if(!$db || mysql_num_rows($db) <= 0){
		?>
		<div class="titlebackl2">
			اول باید گروه تعیین کنید تا زمانی که گروهی تعیین نکرده اید نمی توانید نظر سنجی بسازید !
		</div>
		<?php
		}
		elseif( $create->qcheack() )
		{
		?>
		<div class="titlebackl2">
		<?php echo $create->errormsg(); ?>
			<div style="color:#ff0000">
				سوال نظر سنجی : <?php echo $create->qinfo('q') ?><p>
				نوع نظر سنجی : <?php echo $create->qinfo('t') ?><p>
				گروه : <?php echo $create->qinfo('g') ?>
			</div>
			<p>
			گزینه های نظر سنجی را مشخص و به نظرسنجی بیفزایید :
			<p>
			<center>
				<table border="0px" width="430px">
					<form name="selection" method="post" action="">
						<input type="hidden" name="selection_id" value="<?php echo $create->randomcode(); ?>">
						<tr>
							<td style="text-align: right;">گزینه :</td>
							<td style="text-align: left;"><input type="text" name="s" value=""  class="textstyle" style="width:200px;direction:rtl;"></td>
						</tr>
						<tr>
							<td></td>
							<td style="text-align: left;"><input type="submit" name="send" value="ثبت گزینه"  class="button" style="width:100px;"></td>
						</tr>
					</form>
				</table>
				<p>
				<table border="1px" width="480px">
					<tr>
						<td style="text-align: right;">گزینه</td>
						<td style="text-align: right;">اختیارات</td>
					</tr>
					<?php			
					$db = mysql_query("SELECT * FROM selection WHERE question_id=".$_GET['id']." ORDER BY id DESC");
					if(!$db || mysql_num_rows($db) <= 0)
						echo '<td style="text-align: right;">خالیست!</td><td style="text-align: right;">خالیست!</td>';
					while($row = mysql_fetch_array($db))
						{
							echo '<tr><td style="text-align: right;">'.$row['selection'].'</td><td style="text-align: right;"><a href="action?ac=edit&cat=selection&id='.$row['id'].'">ویرایش</a>|<a href="action?ac=delete&cat=selection&id='.$row['id'].'">حذف</a></td></tr>';
						}
					?>
				</table>
			</center>
		</div><p>
		<div class="titlebackl2">
			<center><a href="create">ساخت یک نظرسنجی تازه</a></center>
		</div>
		<?php
		}
		else
		{
		?>
		<div class="titlebackl2">
		<?php echo $create->errormsg(); ?>
		برای ساخت نظر سنجی سوال ، نوع و گروه را مشخص و وارد مرحله ی بعد بشوید. در مرحله ی بعد گزینه ها را وارد خواهید کرد :
		<p>
			<center>
				<table border="0px" width="430px">
					<form name="create" method="post" action="create">
						<input type="hidden" name="code_id" value="<?php echo $create->randomcode(); ?>">
						<tr>
							<td style="text-align: right;">گروه :</td>
							<td style="text-align: left;">
								<select name="g" class="textstyle" style="width:206px;direction:rtl;">
									<?php			
									$db = mysql_query("SELECT * FROM `group` ORDER BY id DESC");
									while($row = mysql_fetch_array($db))
										{
											echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td style="text-align: right; ">نوع نظرسنجی :</td>
							<td style="text-align: left;">
								<input type="radio" name="t" value="1" checked />تک انتخابی
								<input type="radio" name="t" value="2"  />چند انتخابی
							</td>
						</tr>
						<tr>
							<td style="text-align: right;">سوال نظرسنجی :</td>
							<td style="text-align: left;"><input type="text" name="q" value=""  class="textstyle" style="width:200px;direction:rtl;"></td>
						</tr>
						<tr>
							<td></td>
							<td style="text-align: left;"><input type="submit" name="send" value="مرحله بعد"  class="button" style="width:100px;"></td>
						</tr>
					</form>
				</table>
			</center>
		</div>
		<?php
		}
		?>
	</div>
</body>
</html>
