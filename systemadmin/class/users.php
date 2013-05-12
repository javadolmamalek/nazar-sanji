<?php

/*
-------------------------------------------------------------
این کلاس مراحل ثبت کاربر و نظرسنجی هایی که در آن شرکت کرده را انجام می دهد.
جدول های مورد استفاده در این کلاس:
name
participant
question
selection
stats
-------------------------------------------------------------
*/

require_once dirname(__FILE__) . '/connection.php';

class users
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
	
	//تابع ساخت کاربر جدید و هدایتش به سمت صفحه نظرسنجی
	public function register()
	{
		if( !$this->formecodecheck( $_POST['reg-code'] ) )
			return $this->errorcode( '<div class="errorstyle">فرم منقضی شده است! لطفا مجددا ارسال کنید.</div><p>' );
			
		$fvar = array();
		
		if( !$this->formvar( $fvar ) )
			return false;
			
		if ( !$this->filed_check( $fvar ) )
			return false;
			
		if( $this->exist( $fvar ) )
			return $this->errorcode( '<div class="errorstyle">این نام در سیستم ثبت شده است . لطفا نام دیگری انتخاب کنید</div><p>' );
			
		if( !$this->savetodb( $fvar ) )
			return $this->errorcode( '<div class="errorstyle">مشکلی در سیستم به وجود آمد، لطفا مجدد تلاش کنید.</div><p>' );
			
		return true;
	}
	
	
	//تابع ثبت پاسخ نظرسنجی
	public function register2()
	{
		if( !$this->pcheck() )
			return $this->errorcode( '<div class="errorstyle">شما در این نظرسنجی قبلا شرکت کرده اید.</div><p>' );
			
		if( !$this->formecheck() )
			return $this->errorcode( '<div class="errorstyle">حتمن باید گزینه ای را انتخاب کنید.</div><p>' );
			
		if( !$this->savetodb2() )
			return $this->errorcode( '<div class="errorstyle">مشکلی در سیستم به وجود آمد، لطفا مجدد تلاش کنید.</div><p>' );
			
		return true;
	}
	
	//تابع نمایش پیام ثبت موفقیت آمیز نظرسنجی
	public function suc()
	{
		return $this->errorcode( '<div class="sendstyle">نظر شما با موفقیت ثبت شد</div><p>' );
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
	
	//تابع ساخت آرایه ای از اطلاعات ورودی کاربر نظردهنده
	private function formvar( &$fvar )
	{
		$fvar['name'] = trim( $this->Sanitize( $_POST['name'] ) );
		$fvar['group'] = trim( $this->Sanitize( $_POST['group'] ) );
		return true;
	}
	
	//تابع مربوط به بررسی خالی نبودن نام کاربر
	private function filed_check( &$fvar )
	{
		if( empty( $fvar['name'] ) )
			return $this->errorcode( '<div class="errorstyle">نامتان را وارد نکرده اید!</div><p>' );
		return true;
	}
	
	//تابع بررسی وجود نداشتن نام کاربری
	private function exist ( &$fvar )
	{
		if( !isset( $_SESSION ) )
			session_start();
		$result = mysql_query( "SELECT * FROM name WHERE name='".$fvar['name']."' AND `group`='".$fvar['group']."'" );
		if($result && mysql_num_rows($result) > 0)
			return true;
		return false;
	}
	
	//تابع وارد کردن اطلاعات نظردهنده به دیتابیس
	private function savetodb( &$fvar )
	{
		$insert_query = 'INSERT INTO name(
			name,
			`group`
		)
		VALUES
		(
			"' . $this->sql_filter( $fvar['name'] ) . '",
			"' . $this->sql_filter( $fvar['group'] ) . '"
		)';
		if( !mysql_query( $insert_query ) )
			return false;
		$_SESSION['qu_code'] = mysql_insert_id();
		return true;
	}
	
	//بررسی اینکه کاربر قبلن در نظرسنجی شرکت کرده یا نه
	private function pcheck ()
	{
		$result = mysql_query( "SELECT * FROM participant WHERE name_id='".$_POST['nam_id']."' AND question_id='".$_POST['que_id']."'" );
		if($result && mysql_num_rows($result) > 0)
			return false;
		return true;
	}
	
	//تابع بررسی خالی نبودن گزینه ها
	private function formecheck()
	{
		$result = mysql_fetch_array( mysql_query( "SELECT type FROM question WHERE id='".$_POST['que_id']."'" ) );
		if( $result['type'] == 1 )
		{
			$v = 'q-'.$_POST['que_id'];
			if( isset( $_POST[$v] ) )
				return true;
			return false;
		}
		if( $result['type'] == 2 )
		{
			$db = mysql_query("SELECT * FROM selection WHERE question_id='".$_POST['que_id']."'");
			while($row = mysql_fetch_array($db))
			{
				$v = 'q-'.$row['id'];
				if( isset( $_POST[$v] ) )
					return true;
			}
			return false;
		}
		return false;
	}
	
	//تابع وارد کردن گزینه های انتخابی به دیتابیس
	private function savetodb2()
	{
		$result = mysql_fetch_array( mysql_query( "SELECT type FROM question WHERE id='".$_POST['que_id']."'" ) );
		if( $result['type'] == 1 )
		{
			$v = 'q-'.$_POST['que_id'];
			$insert_query = 'INSERT INTO stats(
				selection_id,
				question_id,
				name_id
			)
			VALUES
			(
				"' . trim( $this->Sanitize( $this->sql_filter( $_POST[$v] ) ) ) . '",
				"' . trim( $this->Sanitize( $this->sql_filter( $_POST['que_id'] ) ) ) . '",
				"' . trim( $this->Sanitize( $this->sql_filter( $_POST['nam_id'] ) ) ) . '"
			)';
			if( !mysql_query( $insert_query ) )
				return false;
			
			$insert_query2 = 'INSERT INTO participant(
				name_id,
				question_id
			)
			VALUES
			(
				"' . trim( $this->Sanitize( $this->sql_filter( $_POST['nam_id'] ) ) ) . '",
				"' . trim( $this->Sanitize( $this->sql_filter( $_POST['que_id'] ) ) ) . '"
			)';
			if( !mysql_query( $insert_query2 ) )
				return false;
				
			return true;
		}
		if( $result['type'] == 2 )
		{
			$db = mysql_query("SELECT * FROM selection WHERE question_id='".$_POST['que_id']."'");
			while($row = mysql_fetch_array($db))
			{
				$v = 'q-'.$row['id'];
				if( isset( $_POST[$v] ) )
				{
					$insert_query = 'INSERT INTO stats(
					selection_id,
					question_id,
					name_id
					)
					VALUES
					(
						"' . trim( $this->Sanitize( $this->sql_filter( $_POST[$v] ) ) ) . '",
						"' . trim( $this->Sanitize( $this->sql_filter( $_POST['que_id'] ) ) ) . '",
						"' . trim( $this->Sanitize( $this->sql_filter( $_POST['nam_id'] ) ) ) . '"
					)';
				if( !mysql_query( $insert_query ) )
					return false;
				}
			}
			
			$insert_query2 = 'INSERT INTO participant(
				name_id,
				question_id
			)
			VALUES
			(
				"' . trim( $this->Sanitize( $this->sql_filter( $_POST['nam_id'] ) ) ) . '",
				"' . trim( $this->Sanitize( $this->sql_filter( $_POST['que_id'] ) ) ) . '"
			)';
			if( !mysql_query( $insert_query2 ) )
				return false;
				
			return true;
		}
		return false;
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
