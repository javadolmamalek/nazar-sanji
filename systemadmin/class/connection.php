<?php

/*
-------------------------------------------------------------
اطلاعات مربوط به اتصال دیتابیس
-------------------------------------------------------------
*/

define("DB_DATABASE","project"); //نام دیتابیس
define("DB_HOST","localhost"); // آدرس دیتابیس
define("DB_USER","root"); // نام کاربری دیتابیس
define("DB_PASS",""); // رمز عبور دیتابیس
$con = mysql_connect(DB_HOST,DB_USER,DB_PASS);
mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET utf8"); 
mysql_query("SET SESSION collation_connection = 'utf8_persian_ci'");
mysql_select_db(DB_DATABASE,$con);

?>
