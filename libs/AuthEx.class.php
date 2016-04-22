<?php
/* --------------------------------------------------------------------------
 * AuthEx ユーザー認証クラス (暗号化なし)
 * Version: 1.1
 * Update:  2012.07.12
 * Auther:  s.tsuchiya
 * --------------------------------------------------------------------------
 * 必要PHPライブラリ：MDB2
 * 外部使用関数：htmlspecialchars_encode_array(), reversible_encrypt(), reversible_descript()
 * --------------------------------------------------------------------------
 * ※ 事前にMySQLへmdb_usersを作成しておく必要あり. mdb_user.sql
 *
 * -------------------------------------------------------------------------- */
 
class AuthEx
{
  
  public $errAuthMsg;
  
  private $memberinfo;
  private $memberauthflg; // 暗号化EMAIL＆PASS
  private $authParams;
  private $oci8;
  private $user;
  private $password;
  private $ic;
  
  
  function AuthEx($oci8,$authParams){
    
    $this->oci8       = $oci8;
    $this->authParams = $authParams;
    $this->ic = new InputCheck;
    
  }
  
  function startAuthcode($d) {
    $_POST['email']    = $d['email'];
    $_POST['password'] = $d['password'];
    $authFlg = $this->start();
    return $authFlg;
  }
  
  function start(){
  
    // ログインアウトがGETされた場合 ===============================================
    if (isset($_GET['memberaction']) && $_GET['memberaction']=="logout"){
      $this->logout();
      $refreshurl = str_replace("?".$_SERVER['QUERY_STRING'],"",$_SERVER['REQUEST_URI']);
      header("Location: ".$refreshurl);
      exit;
    
    // ログイン情報がPOSTされた場合 ===============================================
    }elseif (isset($_POST['email']) && isset($_POST['password'])) {
      
      $pa = htmlspecialchars_encode_array($_POST);
      
      $this->user     = $pa['email'];
      $this->password = $pa['password'];
    
      if($this->inputCheck()){
        if($this->dbCheck()){
          $this->makeMemberauthflg();
          $this->postSession();
          
          if(isset($_POST['saveflg']) && $_POST['saveflg']==1){
            $this->postCookie();
          }else{
            $this->delCookie();
          }
          return true;
        }else{
          return false;
        }
      }else{
        return false;
      }
    
    
    // SESSIONにログイン情報がある場合 ===============================================
    }elseif(isset($_SESSION['info']['memberauthflg']) && $_SESSION['info']['memberauthflg']!="") {
      return true;
    // COOKIEにログイン情報がある場合 =================================================
    }elseif(isset($_COOKIE['memberauthflg']) && $_COOKIE['memberauthflg']!=""){
      if($this->getCookie()){
        if($this->dbCheck()){
          $this->postSession();
          return true;
        }else{
          return false;
        }
      }else{
        $this->delCookie();
        return false;
      }
    
    
    // ログイン情報がない場合 =================================================
    }else{
      return false;
    }
  }
  
  
  function dbCheck(){
    $sql = "SELECT * FROM ".$this->authParams['table']." 
            WHERE ".$this->authParams['usernamecol']."='".$this->user."' AND "
            .$this->authParams['passwordcol']."='".$this->password."'";
    $row = $this->oci8->queryRow($sql);
    if(PEAR::isError($row)) die($row->getMessage());
    
    if(count($row)){
      // entry_flg 2 or null のみ許可
      // 2013-06-06 entry_flg に関係なくログインできるよう変更
      //if ($row['entry_flg'] !== 0 AND ($row['entry_flg'] == null OR $row['entry_flg'] == 2)) {
        $this->memberinfo = $row;
        
        // 過去機種対応
        $sql_history = "SELECT * FROM atfsp.t_user_history WHERE id = ".$row['id'];
        $row_history = $this->oci8->queryAll($sql_history);
        if(PEAR::isError($row_history)) die($row_history->getMessage());
        if (count($row_history)) {
          foreach ($row_history as $v) {
            $this->memberinfo['history'][] = $v['model_id'];
          }
        }
        
        return true;
      //} else {
        //$this->errAuthMsg = "会員情報が見つかりませんでした。<br />\n";
        //return false;
      //}
    }else{
      $this->errAuthMsg = "会員情報が見つかりませんでした.<br />\n";
      return false;
    }
  }
  
  
  function inputCheck(){
    if(!empty($this->user) && !empty($this->password)){
      $this->errAuthMsg = $this->ic->email($this->user,'E-MAIL',1);
    }else{
      $this->errAuthMsg = "E-MAILとパスワードを入力して下さい.<br />\n";
    }
    if($this->errAuthMsg==""){
      return true;
    }else{
      return false;
    }
  }
  
  
  function makeMemberauthflg(){
    $crypt_rev           = $this->user."_-_".$this->password;
    $this->memberauthflg = reversible_encrypt(CRYPT_KEY, CRYPT_IV, $crypt_rev);
  }
  
  function getMemberauthflg(){
    $memberauthflg_re = reversible_descript(CRYPT_KEY, CRYPT_IV, $_SESSION['info']['memberauthflg']);
    return $memberauthflg_re;
  }

  function postSession(){
    $_SESSION['info']['memberauthflg'] = $this->memberauthflg;
    $_SESSION['info']['id']            = $this->memberinfo['id'];
    $_SESSION['info']['nick_name']     = $this->memberinfo['nick_name'];
    $_SESSION['info']['email']         = $this->memberinfo['email'];
    $_SESSION['info']['model_id']      = $this->memberinfo['model_id'];
    $_SESSION['info']['history']       = $this->memberinfo['history'];
    //$_SESSION['info']['password']      = $this->memberinfo['password'];
  }
  
  function postCookie(){
    setcookie("memberauthflg", $this->memberauthflg , time()+60*60*24*30*12, "/","",0) ;
  }
  
  
  function delCookie(){
    setcookie("memberauthflg", "" , time()-3600, "/","",0) ;
  }
  
  
  function getCookie(){
    $this->memberauthflg = $_COOKIE['memberauthflg'];
    $memberauthflg_re = reversible_descript(CRYPT_KEY, CRYPT_IV, $_COOKIE['memberauthflg']);
    list($this->user,$this->password) = explode("_-_",$memberauthflg_re); 
    if($this->user=="" || $this->password==""){
      return false;
    }else{
      return true;
    }
  }
  
  
  function logout(){
    $_SESSION = array();
    $PHPSESSID="";
    setcookie("memberauthflg", "" , time()-3600, "/","",0) ;
    session_destroy();
  }
}
