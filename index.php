<?php
require_once dirname(__FILE__) . '/systemadmin/class/users.php';
$users = new users();

if( isset( $_POST['reg-code'] ) )
{
  if( $users->register() )
	{
		if( !isset( $_SESSION ) )
			session_start();
		$row = mysql_fetch_array( mysql_query("SELECT name FROM name WHERE id='".$_SESSION['qu_code']."'") );
		$add = 'st-2?id='.$_SESSION['qu_code'].'&code='.md5( $row['name'] );
		header("Location: $add");
	}
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
		<p>
		<form name="user" method="post" action="st-1">
			<input type="hidden" name="reg-code" value="<?php echo $users->randomcode(); ?>">
			<?php echo $users->errormsg(); ?>
			برای شرکت در نظرسنجی کافیست نام خود را وارد و گروه مورد نظرتان را انتخاب کنید . لازم نیست حتمن از نام واقعی استفاده کنید ، می توانید نام مستعار بنویسید.
			<p>
			<center>
				<table border="0px" width="530px">
					<tr>
						<td style="text-align: right;">نام :</td>
						<td style="text-align: left;"><input type="text" name="name" value=""  class="textstyle" style="width:200px;direction:ltr;"></td>
					</tr>
					<tr>
						<td style="text-align: right;">گروه :</td>
						<td style="text-align: left;">
						<select name="group" class="textstyle" style="width:206px;direction:rtl;">
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
						<td></td>
						<td style="text-align: left;"><input type="submit" name="send" value="ورود"  class="buttom" style="width:100px;"></td>
					</tr>
				</table>
			</center>
		</form>
	</div>
</body>
</html>
