<?php

/*
-------------------------------------------------------------
این کلاس مراحل ساخت نظرسنجی جدید و افزودن گزینه ها را انجام می دهد.
جدول های مورد استفاده در این کلاس:
question
selection
-------------------------------------------------------------
*/

require_once dirname(__FILE__) . '/connection.php';

class create
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
	
	//ثبت سوال و اطلاعات نظرسنجی
	public function register1()
	{
		if( !$this->formecodecheck( $_POST['code_id'] ) )
			return $this->errorcode( '<div class="errorstyle">فرم منقضی شده است! لطفا مجددا ارسال کنید.</div><p>' );
			
		$fvar = array();
		
		if( !$this->formvar1( $fvar ) )
			return false;
			
		if ( !$this->filed_check1( $fvar ) )
			return false;
			
		if( !$this->savetodb1( $fvar ) )
			return $this->errorcode( '<div class="errorstyle">مشکلی در سیستم به وجود آمد، لطفا مجدد تلاش کنید.</div><p>' );
			
		return true;
	}
	
	//ثبت گزینه ها
	public function register2()
	{
		if( !$this->formecodecheck( $_POST['selection_id'] ) )
			return $this->errorcode( '<div class="errorstyle">فرم منقضی شده است! لطفا مجددا ارسال کنید.</div><p>' );
			
		$fvar = array();
		
		if( !$this->formvar2( $fvar ) )
			return false;
			
		if ( !$this->filed_check2( $fvar ) )
			return false;
			
		if ( !$this->scheack( $fvar ) )
			return $this->errorcode( '<div class="errorstyle">این گزینه قبلن ثبت شده است.</div><p>' );
			
		if( !$this->savetodb2( $fvar ) )
			return $this->errorcode( '<div class="errorstyle">مشکلی در سیستم به وجود آمد، لطفا مجدد تلاش کنید.</div><p>' );
			
		return true;
	}

	//تابع بررسی اینکه آیا نظرسنجی ساخته شده یا نه
	public function qcheack()
	{
		if( isset( $_GET['id'] ) )
		{
			$result = mysql_query( "SELECT id FROM question WHERE id='".$_GET['id']."'" );
				if(!$result || mysql_num_rows($result) <= 0)
					return false;
			return true;
		}
		return false;
	}
	
	//تابع ارسال پیام ثبت شدن گزینه
	public function suc()
	{
		return $this->errorcode( '<div class="sendstyle">گزینه با موفقیت افزوده شد</div><p>' );
	}
	
	//تابع خروجی اطلاعات مربوط به نظر سنجی
	public function qinfo ($var)
	{
		$row = mysql_fetch_array( mysql_query("SELECT question, type, `group` FROM question WHERE id='".$_GET['id']."'") );
		$row2 = mysql_fetch_array( mysql_query("SELECT name FROM `group` WHERE id=".$row['group']."") );
		if( $var == 'q')
			return $row['question'];
		if( $var == 'g')
			return $row2['name'];
		if( $var == 't')
		{
			if( $row['type'] == 1 )
				return 'تک انتخابی';
			if( $row['type'] == 2 )
				return 'چند انتخابی';
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
	
	//تابع بررسی مساوی بودن کد رندمی با سشن ساخته شده
	private function formecodecheck( $var )
	{
		if( !isset( $_SESSION ) )
			session_start();
		if ( md5( $var ) == $_SESSION['random_code'] )
			return true;
		return false;
	}
	
	//تابع ساخت آرایه ای از اطلاعات ورودی سوال نظرسنجی
	private function formvar1( &$fvar )
	{
		$fvar['g'] = trim( $this->Sanitize( $_POST['g'] ) );
		$fvar['t'] = trim( $this->Sanitize( $_POST['t'] ) );
		$fvar['q'] = trim( $this->Sanitize( $_POST['q'] ) );
		return true;
	}
	
	//تابع بررسی خالی نبودن فیلد سوال نظرسنجی
	private function filed_check1( &$fvar )
	{
		if( empty( $fvar['q'] ) )
			return $this->errorcode( '<div class="errorstyle">سوال نظرسنجی را وارد کنید!</div><p>' );
		return true;
	}
	
	//تابع وارد کردن اطلاعات سوال نظرسنجی در دیتابیس
	private function savetodb1( &$fvar )
	{
		$insert_query = 'INSERT INTO question(
			`group`,
			type,
			question
		)
		VALUES
		(
			"' . $this->sql_filter( $fvar['g'] ) . '",
			"' . $this->sql_filter( $fvar['t'] ) . '",
			"' . $this->sql_filter( $fvar['q'] ) . '"
		)';
		if( !mysql_query( $insert_query ) )
			return false;
		if( !isset( $_SESSION ) )
			session_start();
		$_SESSION['question_code'] = mysql_insert_id();
		return true;
	}
	
	//تابع ساخت آرایه ای از اطلاعات ورودی گزینه
	private function formvar2( &$fvar )
	{
		$fvar['s'] = trim( $this->Sanitize( $_POST['s'] ) );
		$fvar['id'] = trim( $this->Sanitize( $_GET['id'] ) );
		return true;
	}
	
	//تابع بررسی خالی نبودن فیلد گزینه
	private function filed_check2( &$fvar )
	{
		if( empty( $fvar['s'] ) )
			return $this->errorcode( '<div class="errorstyle">گزینه را وارد کنید!</div><p>' );
		return true;
	}
	
	//تابع بررسی تکراری نبودن گزینه
	private function scheack( &$fvar )
	{
		$result = mysql_query( "SELECT * FROM selection WHERE question_id='".$fvar['id']."' AND selection='".$fvar['s']."'" );
		if($result && mysql_num_rows($result) > 0)
			return false;
		return true;
	}
	
	
	//تابع وارد کردن اطلاعات گزینه در دیتابیس
	private function savetodb2( &$fvar )
	{
		$insert_query = 'INSERT INTO selection(
			question_id,
			selection
		)
		VALUES
		(
			"' . $this->sql_filter( $fvar['id'] ) . '",
			"' . $this->sql_filter( $fvar['s'] ) . '"
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
