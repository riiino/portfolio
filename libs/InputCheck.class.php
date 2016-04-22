<?php 
/* -------------------------------------------------------------
 * フォームデータ バリデーション
 *
 * ------------------------------------------------------------- */
class InputCheck{

  /* 必須チェック */
  function freegrep($v,$label="",$flg=0,$str){
  //return $this->test;
    if($flg==1 && $v==""){
      return $label."(必須)が入力されていません.<br />\n";
    }else{
      return (ereg("([^".$str."]+)",$v)) ? $label."に、[ ".$str."] 以外の文字が入力されています.<br />\n" : "";
    }
  }

  /* 0-9a-zA-Z._-チェック */
  function char($v,$label="",$flg=0,$max="",$min=""){
  //return $this->test;
    if($flg==1 && $v==""){
      return $label."(必須)が入力されていません.<br />\n";
    }elseif($max != "" && is_numeric($max)){
      $v = html_entity_decode($v);
      $strlen = mb_strlen($v);
      return ($strlen > $max) ? $label."の文字数が【".$strlen."文字】です.【".$max."文字】以内にして下さい.<br />\n" : ""; 
    }elseif($min != "" && is_numeric($min)){
      $v = html_entity_decode($v);
      $strlen = mb_strlen($v);
      return ($strlen < $min) ? $label."の文字数が【".$strlen."文字】です.【".$min."文字】以上にして下さい.<br />\n" : ""; 
    }else{
      return (ereg("([^0-9a-zA-Z._-]+)",$v)) ? $label."に、[0-9a-zA-Z._-] 以外の文字が入力されています.<br />\n" : "";
    }
  }

  /* 必須チェック */
  function must($v,$label="",$flg=0,$max=""){
  //return $this->test;
    if($flg==1 && $v==""){
      return $label."(必須)が入力されていません.<br />\n";
    }elseif(is_numeric($max)){
      $v = html_entity_decode($v);
      $strlen = mb_strlen($v);
      return ($strlen > $max) ? $label."の文字数が【".$strlen."文字】です.【".$max."文字】以内にして下さい.<br />\n" : "";  
    }else{
      return "";
    }
  }
  /* 数字チェック */
  function num($v, $label="", $flg=0){
  //return $this->test;
    if($flg==1 && $v==""){
      return $label."(必須)が入力されていません.<br />\n";
    }else{
      return (ereg("([^0-9]+)",$v)) ? $label."に、半角数字以外の文字が入力されています.<br />\n" : "";
    }
  }
  /* 半角英字チェック */
  function alpha($v, $label="", $flg=0){
    if($flg==1 && $v==""){
      return $label."(必須)が入力されていません.<br />\n";
    }else{
      return (ereg("([^A-Za-z]+)",$v)) ? $label."に、半角英数字以外の文字が入力されています.<br />\n" : "";
    }
  }
  /* 半角英数字チェック */
  function alphanum($v, $label="", $flg=0){
    if($flg==1 && $v==""){
      return $label."(必須)が入力されていません.<br />\n";
    }else{
      return (ereg("([^0-9A-Za-z]+)",$v)) ? $label."に、半角英数字以外の文字が入力されています.<br />\n" : "";
    }
  }
  /* 半角英数字記号チェック */
  function alphanumsymbol($v, $label="", $flg=0){
    if($flg==1 && $v==""){
      return $label."(必須)が入力されていません.<br />\n";
    }else{
      return (ereg("([^0-9A-Za-z_-]+)",$v)) ? $label."に、半角英数字以外の文字が入力されています.<br />\n" : "";
    }
  }
  /* 郵便番号チェック */
  function zipcode($v, $label="", $flg=0){
    if($flg==1 && ( $v=="" || $v=="-")){
      return $label."(必須)が入力されていません.<br />\n";
    }elseif($flg==0 && $v=="-"){
      return "";
    }else{
    //echo $v;
      return (!preg_match("/^\d{3}\-\d{4}$/",$v)) ? $label."が、正しく入力されていません.<br />\n" : "";
    }
  }
  /*  TEL,FAX チェック */
  function telfax($v, $label="", $flg=0){
    if($flg==1 && ( $v=="" || $v=="--")){
      return $label."(必須)が入力されていません.<br />\n";
    }elseif($flg==0 && ( $v=="" || $v=="--")){
      return "";
    }else{
      return (!preg_match("/\d{2,4}-\d{2,4}-\d{4}/",$v)) ? $label."が、正しく入力されていません.<br />\n" : "";
    }
  }
  
  /*  日付 チェック */
  function date($v, $label="", $flg=0){
    if($flg==1 && $v==""){
      return $label."(必須)が入力されていません.<br />\n";
    }elseif($flg==0 && $v==""){
      return "";
    }else{
      $v = str_replace("-","/",$v);
      $ymd = explode("/",$v);
      return (!checkdate(intval($ymd[1]),intval($ymd[2]),intval($ymd[0]))) ? $label."が、正しく入力されていません.<br />\n" : "";
    }
  }

  /*  URL チェック */
  function url($v, $label="", $flg=0){
    if($flg==1 && $v==""){
      return $label."(必須)が入力されていません.<br />\n";
    }elseif($flg==0 && $v==""){
      return "";
    }else{
      return (!preg_match("/^(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/",$v)) ? $label."が、正しく入力されていません.<br />\n" : "";
    }
  }

  /*  EMAIL チェック */
  function email($v, $label="Eメールアドレス", $flg=0){
    if($flg==1 && $v==""){
      return $label."(必須)が入力されていません.<br />\n";
    }else{
      //if(preg_match('/^(?:[^(\040)<>@,;:".\\\\\[\]\000-\037\x80-\xff]+(?![^(\040)<>@,;:".\\\\\[\]\000-\037\x80-\xff])|"[^\\\\\x80-\xff\n\015"]*(?:\\\\[^\x80-\xff][^\\\\\x80-\xff\n\015"]*)*")(?:\.(?:[^(\040)<>@,;:".\\\\\[\]\000-\037\x80-\xff]+(?![^(\040)<>@,;:".\\\\\[\]\000-\037\x80-\xff])|"[^\\\\\x80-\xff\n\015"]*(?:\\\\[^\x80-\xff][^\\\\\x80-\xff\n\015"]*)*"))*@(?:[^(\040)<>@,;:".\\\\\[\]\000-\037\x80-\xff]+(?![^(\040)<>@,;:".\\\\\[\]\000-\037\x80-\xff])|\[(?:[^\\\\\x80-\xff\n\015\[\]]|\\\\[^\x80-\xff])*\])(?:\.(?:[^(\040)<>@,;:".\\\\\[\]\000-\037\x80-\xff]+(?![^(\040)<>@,;:".\\\\\[\]\000-\037\x80-\xff])|\[(?:[^\\\\\x80-\xff\n\015\[\]]|\\\\[^\x80-\xff])*\]))*$/', $v)){
      if(preg_match('/^[0-9a-zA-Z\.\!\#\$\%\&\'\*\+\-\/\=\?\^\_\`\{\|\}\~\(\)\<\>\[\]\:\;\@\,\"]+@[0-9a-zA-Z\.\-\_]+/', $v)){
       return "";
      }else{
        return $label."の形式が正しくありません.<br />\n";
      }
    }
  }
  
  function email_comparison($v1, $v2, $label1="メールアドレス", $label2="メールアドレス(確認)", $flg){
    $email1 = $this->email($v1,$label1,$flg);
    $email2 = $this->email($v2,$label2,$flg);
    if($email1=="" && $email2==""){
      if($v1 == $v2){
        return "";
      }else{
        return $label1."が一致しませんでした.<br />\n";
      }
    }else{
      return $email1.$email2;
    }
  
  }
  /* FILES チェック */
  function file_check($files, $key, $file_labels, $file_modes, $file_flg, $pa=NULL) {
    foreach ($files['file']['name'][$key] as $k => $file_name) {
  	  if ($file_flg[$k]==1 && $file_name=="" && $pa['file'][$key][$k]=="") {
  		  $msgErr .= $file_labels[$k]."(必須)がアップされていません.<br />";
  	  } elseif ($file_name != "") {
        $extArray  = explode(",",constant($file_modes[$k]."_EXT"));
        $file_name = urlencode($file_name); //日本語ファイル禁止を別途
        $ext       = array_pop(explode('.',$file_name));
        $size      = $files['file']['size'][$key][$k];
        $maxsize   = intval(constant($file_modes[$k]."_MAXSIZE"));
        $maxsiseMB = intval(constant($file_modes[$k]."_MAXSIZE")/(100*100*100));
        $tmpfile   = TMP_DIR.$key.$k.".".strtolower($ext); // ex. file1
        if(array_search($ext,$extArray) === FALSE) {
          $msgErr .=  $file_labels[$k]."の拡張子は、[ ".constant($file_modes[$k]."_EXT")." ] にしてください.<br />";
        }elseif($size > $maxsize){
          $msgErr .=  $file_labels[$k]."のファイル容量は、[ ".$maxsiseMB."MB ] 以下にしてください.<br />";
        }elseif(!move_uploaded_file($files['file']["tmp_name"][$key][$k],$tmpfile)){
          $msgErr .=  $file_labels[$k]."の仮アップロードに失敗しました.<br />";
        }
  	  }
    }
    return $msgErr;
  }
}


/* -------------------------------------------------------------
 * ファイルアップロードクラス
 *
 * ------------------------------------------------------------- */
class FileUploadController{

  public $maxnum;
  public $prefix = "file";
  public $files  = array();

  function __construct($n = 1){
    $this->maxnum = $n;
    $this->start();

  }
  function start(){
    /*
    if(isset($_FILES)){
      foreach($_FILES as $k => $v) {
        //echo $k."------>";print_r($v);
        $files[$k]['name'] = $this->check($v,"教員写真",0,"IMG");
        $files[$k] = $this->check($v,"教員写真",0,"IMG");
      }
    }else{
    echo "none";
    }
     */
  }
  /* FILES チェック */
  function check($v, $label="", $flg=0, $mode=NULL){

    if($mode!=NULL){

      $extArray  = explode(",",constant($mode."_EXT"));
      $filename  = urlencode($v['name']); //日本語ファイル禁止を別途
      $ext       = array_pop(explode('.',$filename));
      $size      = $v['size'];
      $maxsize   = intval(constant($mode."_MAXSIZE"));
      $maxsiseMB = intval(constant($mode."_MAXSIZE")/(100*100*100));
      
      $tmpfile   = TMP_DIR.$v['key'].".".strtolower($ext); // ex. file1

      //echo $v["tmp_name"]."->".$tmpfile."<br />";
      //echo $size."->".constant($mode."_MAXSIZE")."->".(constant($mode."_MAXSIZE")/(100*100*100))."<br />";

      if(array_search($ext,$extArray) === FALSE) {
        return $label."の拡張子は、[ ".constant($mode."_EXT")." ] にしてください。<br />\n";

      //}elseif(ereg("([^0-9a-zA-Z._-]+)",$filename)){
      //  return $label."のファイル名に、[0-9a-zA-Z._-] 以外の文字が入力されています。<br />\n";

      }elseif($size > $maxsize){
        return $label."のファイル容量は、[ ".$maxsiseMB."MB ] 以下にしてください。<br />\n";

      }elseif(!move_uploaded_file($v["tmp_name"],$tmpfile)){
        echo "[NG] ".$v["tmp_name"]."->".$tmpfile."<br />";
        return $label."の仮アップロードに失敗しました。<br />\n";

      }else{
        return "";

      }

    }else{
      return $label."にて、ファイルチェックエラーが発生しました。<br />\n";
    }
  }



}
