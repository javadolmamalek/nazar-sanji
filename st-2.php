<?php
require_once dirname(__FILE__) . '/systemadmin/class/users.php';
$users = new users();

if( isset( $_POST['que_id'] ) )
{
  if( $users->register2() )
		$users->suc();
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>شرکت در نظر سنجی</title>
	<link rel="stylesheet" href="css/style.css" type="text/css">
</head>
<body>
	<div class="border">
		<p><center><a href="./">بازگشت به صفحه اصلی</a></center><p>
	</div>
	<p>
	<div class="border">
		<p>
		<?php echo $users->errormsg(); ?>
		لطفا در هر کدام از نظرسنجی ها که مایلید شرکت کنید . تا زمانی که به نظر سنجی های مورد نظرتان پاسخ نداده اید به صفحه ی اصلی باز نگردید .<p>
	</div>
	<p>
<?php
if( isset( $_GET['id'] ) )
{
	if( isset( $_GET['code'] ) )
	{
	
	
		$result = mysql_query( "SELECT * FROM name WHERE id='".$_GET['id']."'" );
		$row = mysql_fetch_array($result);
		if(!$result || mysql_num_rows($result) <= 0)
			header("Location: st-1");
		if( md5( $row['name'] ) != $_GET['code'] )
			header("Location: st-1");
			
		else
		{
			$que = mysql_query( "SELECT * FROM question WHERE `group`='".$row['group']."' ORDER BY id DESC");
			while($res = mysql_fetch_array($que))
			{
				$que2 = mysql_query( "SELECT * FROM participant WHERE name_id='".$row['id']."' AND question_id='".$res['id']."'" );
				if($que2 && mysql_num_rows($que2) > 0)
					echo '<div class="border"><p><div style="color:#ff0000">شما به نظر سنجی شماره '.$res['id'].' جواب داده اید.</div><p></div><p>';
				elseif( $res['status'] == 0 )
					echo '';
				else
				{
					echo '<div class="border"><p>
							<form name="question" method="post" action="">
							<input type="hidden" name="que_id" value="'.$res['id'].'">
							<input type="hidden" name="nam_id" value="'.$row['id'].'">
							نظرسنجی شماره '.$res['id'].' : '.$res['question'].'
							<p>';
							$sel = mysql_query( "SELECT * FROM selection WHERE question_id='".$res['id']."'" );
							if( $res['type'] == 1 )
							{
								while($sel1 = mysql_fetch_array($sel))
								{
									echo '<input type="radio" name="q-'.$res['id'].'" value="'.$sel1['id'].'" />'.$sel1['selection'].'<br>';
								}
							}
							elseif( $res['type'] == 2 )
							{
								while($sel1 = mysql_fetch_array($sel))
								{
									echo '<input type="checkbox" name="q-'.$sel1['id'].'" value="'.$sel1['id'].'"> '.$sel1['selection'].'<br>';
								}
							}
							else
								echo '';
							echo'<p><input type="submit" name="send" value="ارسال"  class="buttom" style="width:100px;"></form>
					</div><p>';
				}
			}
		}
	}
	else header("Location: st-1");
}
else header("Location: st-1");
?>
	<div class="border">
		<p><center><a href="./">بازگشت به صفحه اصلی</a></center><p>
	</div>
</body>
</html>
