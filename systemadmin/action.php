<?php
require_once dirname(__FILE__) . '/class/action.php';
$action = new action();

require_once dirname(__FILE__) . '/class/setting.php';
$setting = new setting();

if( !$setting->login_cheack() )
  header("Location: login");
	
if( isset( $_POST['id'] ) )
{
	if( $_POST['ac'] == 'edit' )
	{
		if( $_POST['cat'] == 'group' )
		{
			if( $action->edit('group'))
				header("Location: group");
		}
		
		elseif( $_POST['cat'] == 'selection' )
		{
			$result = mysql_query("SELECT * FROM selection WHERE id='".$_GET['id']."'");
			if($result && mysql_num_rows($result) > 0)
			{
				$res = mysql_fetch_array( $result );
				$add = 'create?id='.$res['question_id'];
			}
			if( $action->edit('selection') )
			{
				header("Location: $add");
			}
		}
		
		elseif( $_POST['cat'] == 'question' )
		{
			if( $action->edit('question') )
				header("Location: manage");
		}
		
	}
	
	elseif( $_POST['ac'] == 'delete' )
	{
		if( $_POST['cat'] == 'group' )
		{
			if( $action->delete('group') )
				header("Location: group");
		}
		
		elseif( $_POST['cat'] == 'selection' )
		{
			$result = mysql_query("SELECT * FROM selection WHERE id=".$_GET['id']."");
			if($result && mysql_num_rows($result) > 0)
			{
				$res = mysql_fetch_array( $result );
				$add = 'create?id='.$res['question_id'];
			}
			if( $action->delete('selection') )
			{
				header("Location: $add");
			}
		}
		
		elseif( $_POST['cat'] == 'question' )
		{
			if( $action->delete('question') ) 
				header("Location: manage");
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>کنش ها</title>
	<link rel="stylesheet" href="css/style.css" type="text/css">
</head>
<body>
	<div class="loginbackl1"><?php echo $action->errormsg(); ?></div>
	<div class="loginbackl2">
		<center>
			<?php
				if( isset( $_GET['ac'] ) )
					{
						if( $_GET['ac'] == 'edit' )
						{
							if( isset( $_GET['cat'] ) )
							{
								if( $_GET['cat'] == 'group' )
								{
									if( isset( $_GET['id'] ) )
									{
										$result = mysql_query("SELECT * FROM `group` WHERE id='".$_GET['id']."'");
										if(!$result || mysql_num_rows($result) <= 0)
											echo 'کنش غیر معتبر';
										else
										{
											$res = mysql_fetch_array( $result );
											echo '<table border="0px" width="430px">
												<form name="group" method="post" action="">
													<input type="hidden" name="id" value="'.$_GET['id'].'">
													<input type="hidden" name="ac" value="edit">
													<input type="hidden" name="cat" value="group">
													<tr>
														<td style="text-align: right;">عنوان گروه :</td>
														<td style="text-align: left;"><input type="text" name="name" value="'.$res['name'].'" class="textstyle" style="width:200px;direction:rtl;"></td>
													</tr>
													<tr>
														<td></td>
														<td style="text-align: left;"><input type="submit" name="send" value="ثبت تغییرات"  class="button" style="width:100px;"> | <a href="group">بازگشت</a></td>
													</tr>
												</form>
											</table>';
										}
									}
									else echo 'کنش غیر معتبر';
								}
								
								elseif( $_GET['cat'] == 'selection' )
								{
									if( isset( $_GET['id'] ) )
									{
										$result = mysql_query("SELECT * FROM selection WHERE id=".$_GET['id']."");
										if(!$result || mysql_num_rows($result) <= 0)
											echo 'کنش غیر معتبر';
										else
										{
											$res = mysql_fetch_array( $result );
											echo '<table border="0px" width="430px">
												<form name="selection" method="post" action="">
													<input type="hidden" name="id" value="'.$_GET['id'].'">
													<input type="hidden" name="q-id" value="'.$res['question_id'].'">
													<input type="hidden" name="ac" value="edit">
													<input type="hidden" name="cat" value="selection">
													<tr>
														<td style="text-align: right;">گزینه :</td>
														<td style="text-align: left;"><input type="text" name="selection" value="'.$res['selection'].'" class="textstyle" style="width:200px;direction:rtl;"></td>
													</tr>
													<tr>
														<td></td>
														<td style="text-align: left;"><input type="submit" name="send" value="ثبت تغییرات"  class="button" style="width:100px;"> | <a href="create?id='.$res['question_id'].'">بازگشت</a></td>
													</tr>
												</form>
											</table>';
										}
									}
									else echo 'کنش غیر معتبر';
								}
								
								elseif( $_GET['cat'] == 'question' )
								{
									if( isset( $_GET['id'] ) )
									{
										$result = mysql_query("SELECT * FROM question WHERE id=".$_GET['id']."");
										if(!$result || mysql_num_rows($result) <= 0)
											echo 'کنش غیر معتبر';
										else
										{
											$res = mysql_fetch_array( $result );
											echo '<table border="0px" width="430px">
													<form name="create" method="post" action="">
														<input type="hidden" name="id" value="'.$_GET['id'].'">
														<input type="hidden" name="ac" value="edit">
														<input type="hidden" name="cat" value="question">
														<tr>
															<td style="text-align: right;">گروه :</td>
															<td style="text-align: left;">
															<select name="g" class="textstyle" style="width:206px;direction:rtl;">';			
															$db = mysql_query("SELECT * FROM `group` ORDER BY id DESC");
															while($row = mysql_fetch_array($db))
															{
																echo '<option value="'.$row['id'].'"';
																if ( $row['id'] == $res['group'] )
																	echo'selected';
																echo '>'.$row['name'].'</option>';
															}
															echo'</select>
															</td>
														</tr>
														<tr>
															<td style="text-align: right; ">نوع نظرسنجی :</td>
															<td style="text-align: left;">
																<input type="radio" name="t" value="1" ';
																 if ( $res['type'] == '1' )
																	echo'checked';
																echo '/>تک انتخابی
																<input type="radio" name="t" value="2" ';
																 if ( $res['type'] == '2' )
																	echo'checked';
																echo '/>چند انتخابی
															</td>
														</tr>
														<tr>
															<td style="text-align: right;">سوال نظرسنجی :</td>
															<td style="text-align: left;"><input type="text" name="q" value="'.$res['question'].'"  class="textstyle" style="width:200px;direction:rtl;"></td>
														</tr>
														<tr>
															<td></td>
															<td style="text-align: left;"><input type="submit" name="send" value="ثبت تغییرات"  class="button" style="width:100px;"> | <a href="manage">بازگشت</a></td>
														</tr>
													</form>
												</table>';
										}
									}
									else echo 'کنش غیر معتبر';
								}
								else echo 'کنش غیر معتبر';
							}
							else echo 'کنش غیر معتبر';
						}

						elseif( $_GET['ac'] == 'delete' )
						{
							if( isset( $_GET['cat'] ) )
							{
								if( $_GET['cat'] == 'group' )
								{
									if( isset( $_GET['id'] ) )
									{
										$result = mysql_query("SELECT * FROM `group` WHERE id=".$_GET['id']."");
										if(!$result || mysql_num_rows($result) <= 0)
											echo 'کنش غیر معتبر';
										else
										{
											$res = mysql_fetch_array( $result );
											echo 'آیا برا حذف این گروه مطمئن هستید ؟
											<p>'.$res['name'].'<p>
											نکته ی بسیار مهم اینست که با حذف این گروه تمام نظرسنجی ها و آمار مربوط به آن حذف می شود و دیگر قابل بازگشت نخواهد بود .
											<p>
											<form name="group" method="post" action="">
												<input type="hidden" name="id" value="'.$_GET['id'].'">
												<input type="hidden" name="ac" value="delete">
												<input type="hidden" name="cat" value="group">
												<input type="submit" name="delete" value="بله"  class="button" style="width:100px;">  | <a href="group">خیر</a>
											</form>';
										}
									}
									else echo 'کنش غیر معتبر';
								}
								
								elseif( $_GET['cat'] == 'selection' )
								{
									if( isset( $_GET['id'] ) )
									{
										$result = mysql_query("SELECT * FROM selection WHERE id=".$_GET['id']."");
										if(!$result || mysql_num_rows($result) <= 0)
											echo 'کنش غیر معتبر';
										else
										{
											$res = mysql_fetch_array( $result );
											echo 'آیا برای حذف این گزینه مطمئن هستید ؟
											<p>'.$res['selection'].'<p>
											نکته ی بسیار مهم اینست که با حذف این گزینه تمام آمار مربوط به آن حذف می شود و دیگر قابل بازگشت نخواهد بود .
											<p>
											<form name="selection" method="post" action="">
												<input type="hidden" name="id" value="'.$_GET['id'].'">
												<input type="hidden" name="ac" value="delete">
												<input type="hidden" name="cat" value="selection">
												<input type="submit" name="delete" value="بله"  class="button" style="width:100px;"> | <a href="create?id='.$res['question_id'].'">بازگشت</a>
											</form>';
										}
									}
									else echo 'کنش غیر معتبر';
								}
								
								elseif( $_GET['cat'] == 'question' )
								{
									if( isset( $_GET['id'] ) )
									{
										$result = mysql_query("SELECT * FROM question WHERE id=".$_GET['id']."");
										if(!$result || mysql_num_rows($result) <= 0)
											echo 'کنش غیر معتبر';
										else
										{
											$res = mysql_fetch_array( $result );
											echo 'آیا برای حذف این نظرسنجی مطمئن هستید ؟
											<p>'.$res['question'].'<p>
											نکته ی بسیار مهم اینست که با حذف این نظرسنجی تمام آمار مربوط به آن حذف می شود و دیگر قابل بازگشت نخواهد بود .
											<p>
											<form name="question" method="post" action="">
												<input type="hidden" name="id" value="'.$_GET['id'].'">
												<input type="hidden" name="ac" value="delete">
												<input type="hidden" name="cat" value="question">
												<input type="submit" name="delete" value="بله"  class="button" style="width:100px;">  | <a href="manage">بازگشت</a>
											</form>';
										}
									}
									else echo 'کنش غیر معتبر';
								}
							}
							else echo 'کنش غیر معتبر';
						}
						else echo 'کنش غیر معتبر';
					}
				else echo 'کنش غیر معتبر';
			?>
		</center>
	</div>
</body>
</html>
