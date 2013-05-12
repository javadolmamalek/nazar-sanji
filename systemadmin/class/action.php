<?php

/*
-------------------------------------------------------------
این کلاس همه ی کنش های مربوط به حذف و ویرایش را انجام می دهد .
جدول های مورد استفاده در این کلاس:
group
participant
question
selection
stats
-------------------------------------------------------------
*/

require_once dirname(__FILE__) . '/connection.php';

class action
{
  //متغیرها
	var $error_message;

	/*
	-----------------------------------------------------
	توابع عمومی
	-----------------------------------------------------
	*/

	//تابع مربوط به ادیت کردن اطلاعات
	public function edit( $var )
	{
		if( $var == 'group' )
		{
			if( empty( $_POST['name'] ) )
				return $this->errorcode( '<div class="errorstyle">عنوان گروه خالی است!</div><p>' );
			$result = mysql_query( "SELECT * FROM `group` WHERE name='".$this->sql_filter( trim( $this->Sanitize( $_POST['name'] ) ) )."'" );
			if($result && mysql_num_rows($result) > 0)
				return $this->errorcode( '<div class="errorstyle">شما یا تغییری ایجاد نکرده اید و یا عنوان رشته ای که وارد کرده اید قبلن ثبت شده است!</div><p>' );
			$insert_query = "UPDATE `group` SET name = '".$this->sql_filter( trim( $this->Sanitize( $_POST['name'] ) ) )."' WHERE id = ".$_POST['id'];
			if( !mysql_query( $insert_query ) )
				return $this->errorcode( '<div class="errorstyle">مشکلی در سیستم به وجود آمد، لطفا مجدد تلاش کنید.</div><p>' );
			return true;
		}
		
		if( $var == 'selection' )
		{
			if( empty( $_POST['selection'] ) )
				return $this->errorcode( '<div class="errorstyle">گزینه را وارد کنید.</div><p>' );
			$result = mysql_query( "SELECT * FROM selection WHERE selection='".$this->sql_filter( trim( $this->Sanitize( $_POST['selection'] ) ) )."' AND question_id='".$this->sql_filter( trim( $this->Sanitize( $_POST['q-id'] ) ) )."'" );
			if($result && mysql_num_rows($result) > 0)
				return $this->errorcode( '<div class="errorstyle">شما یا تغییری ایجاد نکرده اید و یا گزینه تکراری است.</div><p>' );
			$insert_query = "UPDATE selection SET selection = '".$this->sql_filter( trim( $this->Sanitize( $_POST['selection'] ) ) )."' WHERE id = ".$_POST['id'];
			if( !mysql_query( $insert_query ) )
				return false;
			return true;
		}
		
		if( $var == 'question' )
		{
			if( empty( $_POST['q'] ) )
				return $this->errorcode( '<div class="errorstyle">سوال را وارد کنید</div><p>' );
			$insert_query = "UPDATE question SET `group` = '".$this->sql_filter( trim( $this->Sanitize( $_POST['g'] ) ) )."', type = '".$this->sql_filter( trim( $this->Sanitize( $_POST['t'] ) ) )."', question = '".$this->sql_filter( trim( $this->Sanitize( $_POST['q'] ) ) )."' WHERE id = ".$_POST['id'];
			if( !mysql_query( $insert_query ) )
				return false;
			return true;
		}
		
		return false;
	}
	
	//تابع مربوط به حذف اطلاعات
	public function delete( $var )
	{
		if( $var == 'group' )
		{
			$del1 = "DELETE FROM `group` WHERE id='".$_POST['id']."'";
			if( mysql_query( $del1 ) )
			{
				$db = mysql_query("SELECT id FROM question WHERE `group` = '".$_POST['id']."'");
				while($row = mysql_fetch_array($db))
				{
					$del2 = "DELETE FROM selection WHERE question_id='".$row['id']."'";
					if( mysql_query( $del2 ) )
					{
						$del3 = "DELETE FROM stats WHERE question_id='".$row['id']."'";
						if( mysql_query( $del3 ) )
							mysql_query( "DELETE FROM participant WHERE question_id='".$row['id']."'" );
					}
				}
				$del4 = "DELETE FROM question WHERE `group`='".$_POST['id']."'";
				if( mysql_query( $del4 ) )
					return true;
				return false;
			}
			return false;
		}
		
		if( $var == 'selection' )
		{
			$del1 = "DELETE FROM selection WHERE id='".$_POST['id']."'";
			if( mysql_query( $del1 ) )
			{
				$del2 = "DELETE FROM stats WHERE selection_id='".$_POST['id']."'";
					if( mysql_query( $del2 ) )
					{
						return true;
					}
					return false;
			}
			return false;
		}
		
		if( $var == 'question' )
		{
			$del1 = "DELETE FROM question WHERE id='".$_POST['id']."'";
			if( mysql_query( $del1 ) )
			{
				$del2 = "DELETE FROM selection WHERE question_id='".$_POST['id']."'";
				if( mysql_query( $del2 ) )
				{
					$del3 = "DELETE FROM stats WHERE question_id='".$_POST['id']."'";
					if( mysql_query( $del3 ) )
						$del4 = "DELETE FROM participant WHERE question_id='".$_POST['id']."'";
						if( mysql_query( $del4 ) )
							return true;
						return false;
				}
				return false;
			}
			return false;
		}
		
		return false;
	}
	
	//تابع نمایش پیغام
	public function errormsg()
	{
		if( empty( $this->error_message ) )
			return '';
		$errormsg = $this->error_message;
		return $errormsg;
	}
	
	/*
	-----------------------------------------------------
	توابع خصوصی
	-----------------------------------------------------
	*/
	
	//تابع دادن پیغام خطا به متغیر
	private function errorcode( $err )
    {
        $this->error_message .= $err."\r\n";
    }
	
	//توابع فیلتر کردن اطلاعات ورودی به دیتابیس
	private function Sanitize( $str,$remove_nl=true )
	{
		$str = $this->StripSlashes( $str );
		if( $remove_nl )
		{
			$injections = array( '/(\n+)/i', '/(\r+)/i', '/(\t+)/i', '/(%0A+)/i', '/(%0D+)/i', '/(%08+)/i', '/(%09+)/i' );
			$str = preg_replace($injections,'',$str);
		}
		return $str;
	}
	
	private function StripSlashes( $str )
	{
		if( get_magic_quotes_gpc() )
			$str = stripslashes( $str );
		return $str;
	}
	
	private function sql_filter( $var )
	{
		if( function_exists( "mysql_real_escape_string" ) )
			$ret_str = mysql_real_escape_string( $var );
        else
			$ret_str = addslashes( $var );
		return $ret_str;
	}
}
?>
