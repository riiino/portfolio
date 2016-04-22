<?php
/* --------------------------------------------------------------------------
 * AuthEx ユーザー認証クラス (MDB2+独自暗号化)
 * Version: 1.1
 * Update:  2009.07.14
 * Auther:  s.tsuchiya
 * --------------------------------------------------------------------------
 * 必要PHPライブラリ：MDB2, php-mcrypt
 * 外部使用関数：htmlspecialchars_encode_array(), reversible_encrypt(), reversible_descript()
 * --------------------------------------------------------------------------
 * ※ 事前にMySQLへmdb_usersを作成しておく必要あり. mdb_user.sql
 *
 * -------------------------------------------------------------------------- */
 
class AuthMdbEx
{
  
  var $mdb2         = "";
  var $authParams   = "";
  
  var $mode          = "";
  var $okAuthMsg     = "";
  var $errAuthMsg    = "";
  var $membername    = "";
  var $memberpass    = "";
  var $pa            = "";
  var $mdbauthflg    = ""; // 暗号化ID＆PASS
  var $returnflg     = 0; // 最終戻り値 0:未ログイン, 1:ログイン中, 2:期限切れ 4月～6月までの更新期間中
  var $expireMode    = 0; // 有効期限チェックの必要有無
  
  public $memberinfo    = "";
  
  function AuthMdbEx($mdb2,$authParams){
    
    $this->mdb2       = $mdb2;
    $this->authParams = $authParams;
  }
  
  
  function start(){
    
    // return = 0:未ログイン, 1:ログイン中, (2:期限切れ yyyy/3/31)
    //print_r($_SESSION);
    // ログインアウトがGETされた場合 ===============================================
    if (isset($_GET['memberaction']) && $_GET['memberaction']=="logout"){
    $this->logout();
    $refreshurl = str_replace("?".$_SERVER['QUERY_STRING'],"",$_SERVER['REQUEST_URI']);
    header("Location: ".$refreshurl);
    
    
    // ログイン情報がPOSTされた場合 ===============================================
    }elseif (isset($_POST['membername']) && isset($_POST['memberpass'])) {
      //ini_set('error_reporting', E_ALL); ini_set('display_errors', '1');
      //print_r($_POST);
      if($this->inputCheck()){
        if($this->dbCheck()){
          //echo 'ok';exit;
          $this->makeMdbauthflg();
          $this->checkExpireDate();
          $this->postSession();
          
          if(isset($_POST['saveflg']) && $_POST['saveflg']==1){
            $this->postCookie();
          }else{
            $this->delCookie();
          }
          return $this->returnflg;
        }else{
          return 0;
        }
      }else{
        return 0;
      }
    
    
    // SESSIONにログイン情報がある場合 ===============================================
    }elseif(isset($_SESSION['mdb']['mdbauthflg']) && $_SESSION['mdb']['mdbauthflg']!="") {
      $this->checkExpireDate();
      return $this->returnflg;
    
    
    // COOKIEにログイン情報がある場合 =================================================
    }elseif(isset($_COOKIE['mdbauthflg']) && $_COOKIE['mdbauthflg']!=""){
    
      if($this->getCookie()){
        if($this->dbCheck()){
          $this->checkExpireDate();
          $this->postSession();
          return $this->returnflg;
        }else{
          return 0;
        }
      }else{
        $this->delCookie();
        return 0;
      }
    
    
    // ログイン情報がない場合 =================================================
    }else{
      return 0;
    }
  }
  
  
  function dbCheck(){
    $sql = "SELECT * FROM ".$this->authParams['table']." 
            WHERE ".$this->authParams['usernamecol']."='".$this->membername."' AND "
            .$this->authParams['passwordcol']."='".md5($this->memberpass)."'"
            ;
    $row = $this->mdb2->queryAll($sql);
    if(PEAR::isError($row)) die($row->getMessage());
    if(count($row)){
      //print_r($row);exit;
      $this->memberinfo = $row[0];
      return TRUE;
    }else{
      $this->errAuthMsg = "会員情報が見つかりませんでした.<br />\n";
      return FALSE;
    }
  }
  
  
  function inputCheck(){
    $pa = htmlspecialchars_encode_array($_POST);
    if(!empty($pa['membername']) && !empty($pa['memberpass'])){
      if (!preg_match("/^[a-zA-Z0-9]+$/", $pa['membername'])){
        $this->errAuthMsg = "会員IDは、半角英数字で入力して下さい.<br />\n";
      }
      if (!preg_match("/^[a-zA-Z0-9]+$/", $pa['memberpass'])){
        $this->errAuthMsg = "パスワードは、半角英数字で入力して下さい.<br />\n";
      }
    }else{
      $this->errAuthMsg = "会員IDとパスワードを入力して下さい.<br />\n";
    }
    if($this->errAuthMsg==""){
      $this->membername = $pa['membername'];
      //$this->memberpass = $pa['memberpass'];
      $this->memberpass = reversible_encrypt(CRYPT_KEY, CRYPT_IV, $pa['memberpass']);
      return true;
    }else{
      return false;
    }
  }
  
  
  function makeMdbauthflg(){
    $crypt_rev           = $this->membername."_-_".$this->memberpass;
    $this->mdbauthflg = reversible_encrypt(CRYPT_KEY, CRYPT_IV, $crypt_rev);
  }
  
  function getMdbauthflg(){
    $mdbauthflg_re = reversible_descript(CRYPT_KEY, CRYPT_IV, $_SESSION['mdb']['mdbauthflg']);
    //$param = explode("_-_",$memberauthflg_re);
    return $mdbauthflg_re;
  }

  function checkExpireDate(){
    if($this->expireMode) {
      if($this->memberinfo['expire_date'] != "") {
        $expire_date = $this->memberinfo['expire_date'];
      }elseif($_SESSION['mdb']['expire_date'] != ""){
        $expire_date = str_replace(".","-",$_SESSION['mdb']['expire_date']);
      }
      $now_date    = date('Y-m-d');
      $nowm        = date('n');

      if($expire_date >= $now_date){
        $this->returnflg = 1;
      }elseif($nowm >= 4 && $nowm <= 6){
        $this->returnflg = 2;
      }else{
        $this->logout();
        $this->returnflg = 0;
      }
    }else{
      $this->returnflg = 1;
    }
    //echo "<span style='color:white;font-size:10px;'>".$this->returnflg."...".$expire_date."</span>";
  }

  function postSession(){
    $_SESSION['mdb']['mdbauthflg'] = $this->mdbauthflg;
    $_SESSION['mdb']['id'] = $this->memberinfo['id'];
    $_SESSION['mdb']['memberid'] = $this->memberinfo['memberid'];
    $_SESSION['mdb']['membername'] = $this->memberinfo['name'];
    $_SESSION['mdb']['authmode'] = $this->memberinfo['authmode'];
    $_SESSION['mdb']['expire_date'] = str_replace("-",".",$this->memberinfo['expire_date']);
    //print_r($_SESSION);exit;
  }
  
  function postCookie(){
    setcookie("mdbauthflg", $this->mdbauthflg , time()+60*60*24*30*12, "/","",0) ;
  }
  
  
  function delCookie(){
    setcookie("mdbauthflg", "" , time()-3600, "/","",0) ;
  }
  
  
  function getCookie(){
    $this->mdbauthflg = $_COOKIE['mdbauthflg'];
    $mdbauthflg_re = reversible_descript(CRYPT_KEY, CRYPT_IV, $_COOKIE['mdbauthflg']);
    list($this->membername,$this->memberpass) = explode("_-_",$mdbauthflg_re); 
    if($this->membername=="" || $this->memberpass==""){
      return false;
    }else{
      return true;
    }
  }
  
  
  function logout(){
    $_SESSION['mdb'] = array();
    $PHPSESSID="";
    setcookie("mdbauthflg", "" , time()-3600, "/","",0) ;
    session_destroy();
  }
  
  function makePassword($length) {
    
    $newpassword = "";
    srand((double)microtime() * 54234853);  //乱数表のシードを決定
    $pwelemstr = "abcdefghkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ";  //パスワード文字列の配列を作成
    $pwelem = preg_split("//", $pwelemstr, 0, PREG_SPLIT_NO_EMPTY);
    for($i=0; $i<$length; $i++ ) {
      $newpassword .= $pwelem[array_rand($pwelem, 1)];
    }
    return $newpassword;
  }
  
  function debug(){
  
  }
}
