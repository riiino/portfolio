<?php
/* --------------------------------------------------------------------------
 * Log ログクラス
 * Version: 1.0
 * Update:  2012.03.12
 * Auther:  s.tsuchiya
 * --------------------------------------------------------------------------
 * 必要PHPライブラリ：MDB2, php-mcrypt
 * 外部使用関数：htmlspecialchars_encode_array(), reversible_encrypt(), reversible_descript()
 * --------------------------------------------------------------------------
 * ※ 事前にMySQLへmdb_logを作成しておく必要あり. mdb_log.sql
 *
 * -------------------------------------------------------------------------- */
 
class Log
{
  
  public  $options       = "";
  private $mdb2          = "";
  
  public  $logType       = "text"; // text or mysql
  public  $logOutFile    = "/var/log/php_mysql_error.log"; // /var/log/php_mysql_error.log or mdb_log
  public  $logTitle      = "ERROR"; // /var/log/php_mysql_error.log or mdb_log
  public  $logMsg        = ""; // /var/log/php_mysql_error.log or mdb_log
  public  $logLine       = ""; // /var/log/php_mysql_error.log or mdb_log
  public  $logPhpFile    = ""; // /var/log/php_mysql_error.log or mdb_log
  


  function __construct($options){

    global $mdb2;

    $this->mdb2    = $mdb2;
    $this->options = $options;

  }
  
  
  function set(){
    


  }
  
  function output() {
    $ouput = "[".date('Y-m-d H:i:s')."] {$title}: {$msg} in {$file} on line {$line} \n", 3, $options['logfile'];
    error_log($ouput);
  }


  function fn_error_page($msg, $file, $line=NULL, $options=NULL) {
 
  if($options['logfile'] == "") $options['logfile']  = "/var/log/php_mysql_error.log";

  header("Location: /system_error");
  exit;

  }

  
 
}
