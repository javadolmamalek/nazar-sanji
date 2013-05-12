<?php

/*
-------------------------------------------------------------
این کلاس مراحل ساخت کاربر جدید ، تغیراتی که کاربران می خواهند بدهند و مراحل ورود و بر قراری سشن ها را انجام می دهد
جدول های مورد استفاده در این کلاس:
user
-------------------------------------------------------------
*/

require_once dirname(__FILE__) . '/connection.php';

class setting
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
	
	//مراحل ساخت کاربر جدید
	public function register()
	{
		if( !$this->formecodecheck( $_POST['code_id'] ) )
			return $this->errorcode( '<div class="errorstyle">فرم منقضی شده است! لطفا مجددا ارسال کنید.</div><p>' );
			
		$fvar = array();
		
		if( !$this->formvar( $fvar ) )
			return false;
			
		if ( !$this->filed_check( $fvar ) )
			return false;
			
		if ( !$this->user_cheack( $fvar ) )
			return $this->errorcode( '<div class="errorstyle">این نام کاربری در سیستم وجود دارد!</div><p>' );
			
		if( !$this->savetodb( $fvar ) )
			return $this->errorcode( '<div class="errorstyle">مشکلی در سیستم به وجود آمد، لطفا مجدد تلاش کنید.</div><p>' );
			
		return true;
	}
	
	//تابع حذف کاربر
	public function delete()
	{
		if( $_POST['id'] == '1')
			return $this->errorcode( '<div class="errorstyle">مدیر کل سیستم را نمی توان پاک کرد.</div><p>' );
		
		if( !isset( $_SESSION ) )
			session_start();
		
		if( $_POST['id'] == $_SESSION['user_id'] )
			return $this->errorcode( '<div class="errorstyle">شما خودتان را نمی توانید پاک کنید !</div><p>' );
		
		$del = "DELETE FROM user WHERE id='".$_POST['id']."'";
		if( mysql_query( $del ) )
			return true;
		return $this->errorcode( '<div class="errorstyle">مشکلی در سیستم به وجود آمد، لطفا مجدد تلاش کنید.</div><p>' );
	}
	
	//تابع مراحل فرم تنظیمات
	public function edit()
	{
		if( !$this->formecodecheck( $_POST['code_id'] ) )
			return $this->errorcode( '<div class="errorstyle">فرم منقضی شده است! لطفا مجددا ارسال کنید.</div><p>' );
			
		$fvar = array();
		
		if( !$this->formvar2( $fvar ) )
			return false;
			
		if ( !$this->filed_check2( $fvar ) )
			return false;
			
		if ( !$this->pass_cheack( $fvar ) )
			return false;
			
		if( !$this->savetodb2( $fvar ) )
			return $this->errorcode( '<div class="errorstyle">مشکلی در سیستم به وجود آمد، لطفا مجدد تلاش کنید.</div><p>' );
			
		return true;
	}
	
	//این تابع مراحل ورود را بررسی و انجام می دهد
	public function login()
	{
		if( empty( $_POST['username'] ) )
			return $this->errorcode( '<div class="errorstyle">نام کاربری را وارد نکرده اید !</div><p>' );
		if( empty( $_POST['password'] ) )
			return $this->errorcode( '<div class="errorstyle">رمزعبور را وارد نکرده اید !</div><p>' );
		if( !$this->formecodecheck( $_POST['login_id'] ) )
			return $this->errorcode( '<div class="errorstyle">فرم منقضی شده است لطفا دوباره وارد شوید !</div><p>' );
		if( !$this->login_sesion() )
			return false;
		$_SESSION[$this->login_key()] = $_SESSION['user_name'];
		return true;
	}
	
	//این تابع بررسی می کند و اگر سشن برقرار بود مقدار صحیح را بر می گرداند
	public function login_cheack()
	{
		if( !isset( $_SESSION ) )
			session_start();
		$sessionvar = $this->login_key();
		if( empty ( $sessionvar ) )
			return false;
		if( empty( $_SESSION[$sessionvar] ) )
            return false;
		return true;
	}
	
	//تابع نمایش پیغام موفقیت آمیز
	public function suc()
	{
		return $this->errorcode( '<div class="sendstyle">عملیات با موفقیت انجام شد</div><p>' );
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
	
	//تابع ساخت آرایه ای از اطلاعات ورودی کاربر جدید
	private function formvar( &$fvar )
	{
		$fvar['name'] = trim( $this->Sanitize( $_POST['name'] ) );
		$fvar['user'] = trim( $this->Sanitize( $_POST['user'] ) );
		$fvar['pass'] = trim( $this->Sanitize( $_POST['pass'] ) );
		$fvar['k'] = trim( $this->Sanitize( $_POST['k'] ) );
		return true;
	}
	
	//تابع مربوط به بررسی اطلاعات و خالی نبودن فیلد کاربر جدید
	private function filed_check( &$fvar )
	{
		if( empty( $fvar['name'] ) )
			return $this->errorcode( '<div class="errorstyle">نام را وارد نکرده بودید!</div><p>' );
		if( empty( $fvar['user'] ) )
			return $this->errorcode( '<div class="errorstyle">نام کاربری را وارد نکرده بودید!</div><p>' );
		if( empty( $fvar['pass'] ) )
			return $this->errorcode( '<div class="errorstyle">رمز عبور را وارد نکرده بودید!</div><p>' );
		return true;
	}
	
	//تابع بررسی تکراری نبودن یوزرنیم
	private function user_cheack( &$fvar )
	{
		$field_val = $this->sql_filter( $fvar['user'] );
		$result = mysql_query( "SELECT * FROM user WHERE username='".$field_val."'" );
		if($result && mysql_num_rows($result) > 0)
			return false;
		return true;
	}
	
	//تابع وارد کردن اطلاعات کاربر جدید به دیتابیس
	private function savetodb( &$fvar )
	{
		$insert_query = 'INSERT INTO user(
			name,
			username,
			password,
			mode
		)
		VALUES
		(
			"' . $this->sql_filter( $fvar['name'] ) . '",
			"' . $this->sql_filter( $fvar['user'] ) . '",
			"' . $this->sql_filter( md5( $fvar['pass'] ) ) . '",
			"' . $this->sql_filter( $fvar['k'] ) . '"
		)';
		if( !mysql_query( $insert_query ) )
			return false;
		return true;
	}
	
	//تابع ساخت آرایه ای از اطلاعات ورودی تنظیمات
	private function formvar2( &$fvar )
	{
		$fvar['name'] = trim( $this->Sanitize( $_POST['name'] ) );
		$fvar['pass1'] = trim( $this->Sanitize( $_POST['pass1'] ) );
		$fvar['pass2'] = trim( $this->Sanitize( $_POST['pass2'] ) );
		$fvar['pass3'] = trim( $this->Sanitize( $_POST['pass3'] ) );
		return true;
	}
	
	//تابع مربوط به بررسی اطلاعات و خالی نبودن فیلد تنظیمات
	private function filed_check2( &$fvar )
	{
		if( empty( $fvar['name'] ) )
			return $this->errorcode( '<div class="errorstyle">نام نباید خالی باشد</div><p>' );
		if( !empty( $fvar['pass1'] ) or !empty( $fvar['pass2'] ) or !empty( $fvar['pass3'] ) )
		{
			if( empty( $fvar['pass1'] ) )
				return $this->errorcode( '<div class="errorstyle">رمز عبور فعلی خود را وارد نکرده بودید!</div><p>' );
			if( empty( $fvar['pass2'] ) )
				return $this->errorcode( '<div class="errorstyle">رمز جدید را وارد نکرده بودید!</div><p>' );
			if( empty( $fvar['pass3'] ) )
				return $this->errorcode( '<div class="errorstyle">تکرار رمز جدید را وارد نکرده بودید!</div><p>' );
			return true;
		}
		return true;
	}
	
	//تابع بررسی صحت پسوردهای ورودی در فرم تنظیمات
	private function pass_cheack( &$fvar )
	{
		if( !empty($fvar['pass1'] ) )
		{
			if( $fvar['pass2'] == $fvar['pass3'] )
			{
				if( !isset( $_SESSION ) )
					session_start();
				$result = mysql_fetch_array( mysql_query( "SELECT password FROM user WHERE id='".$_SESSION['user_id']."'" ) );
				if( md5($fvar['pass1']) == $result['password'] )
					return true;
				return $this->errorcode( '<div class="errorstyle">رمز فعلیتان صحیح نیست!</div><p>' );
			}
			return $this->errorcode( '<div class="errorstyle">رمزهای جدید با هم برابر نیستند!</div><p>' );
		}
		return true;
	}
	
	//ذخیره اطلاعات تنظیمات در دیتابیس
	private function savetodb2( &$fvar )
	{
		if( !isset( $_SESSION ) )
			session_start();
		if( !empty($fvar['pass1'] ) )
			$insert_query = "UPDATE user SET name = '".$this->sql_filter( $fvar['name'] )."', password = '".$this->sql_filter( md5( $fvar['pass3'] ) )."' WHERE id = ".$_SESSION['user_id'];
		else
			$insert_query = "UPDATE user SET name = '".$this->sql_filter( $fvar['name'] )."' WHERE id = ".$_SESSION['user_id'];
		if( !mysql_query( $insert_query ) )
			return false;
		return true;
	}
	
	// تابع ساخت سشن هایی که برای ورود لازم است
	private function login_sesion()
	{
		if( !isset( $_SESSION ) )
			session_start();
		$username = $this->sql_filter( trim( $this->Sanitize( $_POST['username'] ) ) );
		$pwdmd5 = $this->sql_filter( trim( $this->Sanitize( md5( $_POST['password'] ) ) ) );
		$result = mysql_query( "SELECT id, username FROM user WHERE username='".$username."' AND password='".$pwdmd5."'" );
		if(!$result || mysql_num_rows($result) <= 0)
			return $this->errorcode( '<div class="errorstyle">نام کاربری یا رمز عبور اشتباه است !</div><p>' );
		$row = mysql_fetch_assoc($result);
		$_SESSION['user_id']  = $row['id'];
		$_SESSION['user_name'] = $row['username'];
		return true;
	}
	
	//این تابع یک کد 10 رقمی بر مبنای آی دی کاربر می سازد
	private function login_key()
	{
		if( !isset( $_SESSION ) )
			session_start();
		if( !empty( $_SESSION['user_id'] ) )
		{
			$retvar = md5( $_SESSION['user_id'] );
			$retvar = 'usr_'.substr($retvar,0,10);
			return $retvar;
		}
		return '';
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
