<?php

/*
-------------------------------------------------------------
این کلاس مراحل افزودن گروه به دیتابیس را طی می کند.
جدول های مورد استفاده در این کلاس:
group
-------------------------------------------------------------
*/

require_once dirname(__FILE__) . '/connection.php';

class group
{
  //متغیرها
	var $error_message;

	/*
	-----------------------------------------------------
	توابع عمومی
	-----------------------------------------------------
	*/

	//تابع ساخت کد رندمی
	public function randomcode()
	{
		if( !isset( $_SESSION ) )
			session_start();
		$code = mt_rand();
		$_SESSION['random_code'] = md5( $code );
		return $code;
	}
	
	//ثبت گروه
	public function register()
	{
		if( !$this->formecodecheck( $_POST['code_id'] ) )
			return $this->errorcode( '<div class="errorstyle">فرم منقضی شده است! لطفا مجددا ارسال کنید.</div><p>' );
			
		$fvar = array();
		
		if( !$this->formvar( $fvar ) )
			return false;
			
		if ( !$this->filed_check( $fvar ) )
			return false;
			
		if ( !$this->groupcheck( $fvar ) )
			return $this->errorcode( '<div class="errorstyle">گروهی با این عنوان قبلن ثبت شده بود!</div><p>' );
			
		if( !$this->savetodb( $fvar ) )
			return $this->errorcode( '<div class="errorstyle">مشکلی در سیستم به وجود آمد، لطفا مجدد تلاش کنید.</div><p>' );
			
		return true;
	}
	
	//تابع ارسال پیام موفقیت آمیز
	public function suc()
	{
		return $this->errorcode( '<div class="sendstyle">گروه با موفقیت ساخته شد!</div><p>' );
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
	
	//تابع بررسی مساوی بودن کد رندمی با سشن ساخته شده
	private function formecodecheck( $var )
	{
		if( !isset( $_SESSION ) )
			session_start();
		if ( md5( $var ) == $_SESSION['random_code'] )
			return true;
		return false;
	}
	
	//تابع ساخت آرایه ای از اطلاعات ورودی فرم
	private function formvar( &$fvar )
	{
		$fvar['name'] = trim( $this->Sanitize( $_POST['name'] ) );
		return true;
	}
	
	//تابع بررسی خالی نبودن فیلد
	private function filed_check( &$fvar )
	{
		if( empty( $fvar['name'] ) )
			return $this->errorcode( '<div class="errorstyle">عنوان گروه را باید وارد کنید</div><p>' );
		return true;
	}
	
	//بررسی تکراری نبودن عنوان گروه
	private function groupcheck( &$fvar )
	{
		$result = mysql_query( "SELECT * FROM `group` WHERE name='".$fvar['name']."'" );
		if($result && mysql_num_rows($result) > 0)
			return false;
		return true;
	}
	
	//تابع وارد کردن اطلاعات رشته به دیتابیس
	private function savetodb( &$fvar )
	{
		$insert_query = 'INSERT INTO `group`(
			name
		)
		VALUES
		(
			"' . $this->sql_filter( $fvar['name'] ) . '"
		)';
		if( !mysql_query( $insert_query ) )
			return false;
		return true;
	}
	
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
