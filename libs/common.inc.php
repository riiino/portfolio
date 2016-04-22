<?php 
/* -------------------------------------------------------------
 * 多次元配列内の文字列を整形（再帰的）
 * KVa : 半角ｶﾅ→全角カナ, 濁点付きの文字を一文字に変換, 「全角」英数字→「半角」に変換
 * ------------------------------------------------------------- */
function mb_convert_kana_array($array, $option = 'KVa', $encoding = 'UTF-8'){
  if (is_array($array)){
    foreach($array as $k => $v){
      if (is_array($v)){
        $array[$k] = mb_convert_kana_array($array[$k], $option, $encoding);
      } else {
        $array[$k] = mb_convert_kana($v, $option, $encoding);
      }
    }
  } else {
    $array = mb_convert_kana($array, $option, $encoding);
  }
  return $array;
}

/* -------------------------------------------------------------
 * 多次元配列内の文字列をエスケープ処理（再帰的）
 * ------------------------------------------------------------- */
function htmlspecialchars_encode_array($array, $ignore_keys = NULL){
  if (is_array($array)){
    foreach($array as $k => $v){
      // スキップキー
      if (in_array($k, (array)$ignore_keys, TRUE)){
        continue;
      }
      if (is_array($v)){
        // 配列だったらここで再帰
        $array[$k] = htmlspecialchars_encode_array($array[$k], $ignore_keys);
      } else {
        // 変換
        if (get_magic_quotes_gpc()) $v = stripslashes($v);
        $v = trim($v);
        $array[$k] = htmlspecialchars($v, ENT_QUOTES);
      }
    }
  } else {
  // 変換
    if (get_magic_quotes_gpc()) $array = stripslashes($array);
    $array = trim($array);
    $array = htmlspecialchars($array, ENT_QUOTES);
  }
  return $array;
}

/* -------------------------------------------------------------
 * 多次元配列内の特殊な HTML エンティティを文字に戻す（再帰的）
 * PHP5以降 : htmlspecialchars_decode_array
 * ------------------------------------------------------------- */
function htmlspecialchars_decode_array($array, $ignore_keys = NULL){
  if (is_array($array)){
    foreach($array as $k => $v){
      // スキップキー
      if (in_array($k, (array)$ignore_keys, TRUE)){
        continue;
      }
      if (is_array($v)){
        // 配列だったらここで再帰
        $array[$k] = htmlspecialchars_decode_array($array[$k], $ignore_keys);
      } else {
        // 変換
        $v = htmlspecialchars_decode($v, ENT_QUOTES);
        $v = str_replace("&amp;", "&", $v);
        $array[$k] = $v;
      }
    }
  } else {
  // 変換
    $array = htmlspecialchars_decode($array, ENT_QUOTES);
    $array = str_replace("&amp;", "&", $array);
  }
  return $array;
}

/* -------------------------------------------------------------
 * 二次元配列内の文字列をSQLインジェクション対策（非再帰的）
 * $option : not NULL = return $typesautoPrepare & autoExecute
 * MDB2 :
 * http://pear.plus-server.net/package.database.mdb2.intro-auto.html
 * http://pear.php.net/manual/ja/package.database.mdb2.intro-auto.php
 * ------------------------------------------------------------- */
function mysql_real_escape_string_array($array, $option = NULL){

  $types = array();

  if (is_array($array)){
    foreach($array as $k => $v){
      if (is_array($v)){
        echo "mysql_real_escape_string_array() is error. Array depth OVER";
        exit;
      } else {
        if (!is_numeric($v)) {
          $v = "'" . mysql_real_escape_string($v) . "'";
          $types[$k] = "text";
        }else{
          $types[$k] = "integer";
        }
        $array[$k] = $v;
      }
    }
  } else {
    if (!is_numeric($array)) {
      $array = "'" . mysql_real_escape_string($array) . "'";
      $types = "text";
    }else{
      $types = "integer";
    }
  }

  if ( is_null($option) ) {
    return $array;
  } else {
    return array('values' => $array, 'types' => $types);
  }
}

/* -------------------------------------------------------------
 * メール送信関数(Unicode版) = 多言語対応版 =
 * mailEx($to,$cc,$bcc,$from,$subject,$body,$reply_to,$return_path)
 * ------------------------------------------------------------- */
function mailEx($to,$cc,$bcc,$from,$subject,$body,$reply_to,$return_path){
	$additional_headers   = "";
	$additional_parameter = "";
	$additional_headers .= "From: $from" . "\n";
	
	if ($cc)           { $additional_headers .= "Cc: $cc\n";             }
	if ($bcc)          { $additional_headers .= "Bcc: $bcc\n";           }
	if ( $reply_to )   { $additional_headers .= "Reply-To: $reply_to\n"; }
	
  ini_set("mbstring.internal_encoding","UTF-8");
	mb_language("uni");
  
	if (!$return_path) {
		$ret = mb_send_mail ($to, $subject, $body, $additional_headers);
		
	} else {
		$additional_parameter = "-f $return_path";
		$ret = mb_send_mail ($to, $subject, $body, $additional_headers, $additional_parameter);
		
	}
  	
	mb_language("Japanese");
  mb_internal_encoding("UTF-8");

  return $ret;
	
}
/* -------------------------------------------------------------
 * メール送信関数(ISO-2022-JP版)
 * mailJp($to,$cc,$bcc,$from,$subject,$body,$reply_to,$return_path)
 * ------------------------------------------------------------- */
function mailJp($to,$cc,$bcc,$from,$subject,$body,$reply_to,$return_path){
	$additional_headers   = "";
	$additional_parameter = "";
	$additional_headers .= "From: $from" . "\n";
	
	if ($cc)           { $additional_headers .= "Cc: $cc\n";             }
	if ($bcc)          { $additional_headers .= "Bcc: $bcc\n";           }
	if ( $reply_to )   { $additional_headers .= "Reply-To: $reply_to\n"; }
	
  //ini_set("mbstring.internal_encoding","UTF-8"); // unicode
	//mb_language("uni"); //unicode
	mb_language("Japanese");
  mb_internal_encoding("UTF-8");
  
	if (!$return_path) {
		$ret = mb_send_mail ($to, $subject, $body, $additional_headers);
		
	} else {
		$additional_parameter = "-f $return_path";
		$ret = mb_send_mail ($to, $subject, $body, $additional_headers, $additional_parameter);
		
	}
  	

  return $ret;
	
}

/* -------------------------------------------------------------
 * セレクトボックス生成
 * makeSelectbox($name,$list,$keyword,$default)
 * $name = 
 * $value = 配列
 * $view  = 配列
 * ------------------------------------------------------------- */
function makeSelectbox($name="",$list="",$keyword="",$default="選択してください"){
   
  if(empty($name) || empty($list)) {echo "makeSelectbox() Error";exit;}
   
  $defsel = ($keyword=="") ? "selected='selected'" : "" ;

  $ret  = "<select id='${name}' name='${name}' size='1'>\n";
  if($default!="") $ret .= "<option value='' $defsel>$default</option>\n";
  
  foreach($list as $k => $v){
    if($keyword == $v) {
      $ret .= "<option value='".$v."' selected='selected'>".$v."</option>\n";
    } else {
      $ret.="<option value='".$v."' >".$v."</option>\n";
    }
  }
  $ret .= "</select>\n";
  return $ret;
  
}

/* -------------------------------------------------------------
 * セレクトボックス生成(value値が配列キーVer)
 * makeSelectbox($name,$list,$keyword,$default)
 * $name = 
 * $value = 配列
 * $view  = 配列
 * ------------------------------------------------------------- */
function makeSelectboxKey($name="",$list="",$keyword="",$default="選択してください",$js=""){

  if(!empty($name) AND !empty($list)) {
   
    $defsel = ($keyword=="") ? "selected='selected'" : "" ;
  
    $onchange = "";
    if($js!="") {
      $onchange = "onchange=\"".$js."\"";
    }
  
    $ret  = "<select name='${name}' ${onchange} >\n";
    if($default!="") $ret .= "<option value='' $defsel>$default</option>\n";
    
    foreach($list as $k => $v){
      if($keyword == $k && $keyword != "") {
        $ret .= "<option value='".$k."' selected='selected'>".$v."</option>\n";
      } else {
        $ret.="<option value='".$k."' >".$v."</option>\n";
      }
    }
    $ret .= "</select>\n";
    return $ret;
  
  }
  
}

/* -------------------------------------------------------------
 * セレクトボックス生成(value値が配列キー, OPTGROUP対応Ver)
 * makeSelectboxOptgroupKey($name,$list,$keyword,$default)
 * $name = 
 * $value = 配列
 * $view  = 配列
 * ------------------------------------------------------------- */
function makeSelectboxOptgroupKey($name="",$list="",$label="",$keyword="",$default="選択してください",$js="",$class=""){

  if(empty($name) || empty($list)) {echo "makeSelectboxKey() Error";exit;}
   
  $defsel = ($keyword=="") ? "selected='selected'" : "" ;
  
  $onchange = "";
  if($js!="") {
    $onchange = "onchange=\"".$js."\"";
  }

  $ret  = "<select name='${name}' ${onchange} class='${class}' >\n";
  if($default!="") $ret .= "<option value='' $defsel>$default</option>\n";
  
  foreach($list as $k1 => $v1){
    $ret .= "<optgroup label='".$label[$k1]."'>";
    foreach($v1 as $k2 => $v2){
      if($keyword == $k2 && $keyword != "") {
        $ret .= "<option value='".$k2."' selected='selected'>".$v2."</option>\n";
      } else {
        $ret.="<option value='".$k2."' >".$v2."</option>\n";
      }
    }
    $ret .= "</optgroup>";
  }
  $ret .= "</select>\n";
  return $ret;
  
}
  
/* -------------------------------------------------------------
 * 可逆暗号化関数
 * 指定の文字列を複合化可能な文字列に暗号化します。
 * $keyには任意の文字列、$ivには8文字の文字列を指定。
 * ただし暗号化複合化時には同じ値を利用すること
 * ex) $test1 = reversible_encrypt(CRYPT_KEY, CRYPT_IV, "暗号化したい文字列");
 * ------------------------------------------------------------- */
function reversible_encrypt($key, $iv, $data){

    $base64_data = base64_encode($data);
    $resource = mcrypt_module_open(MCRYPT_BLOWFISH, '',  MCRYPT_MODE_CBC, '');
    mcrypt_generic_init($resource, $key, $iv);
    $encrypted_data = mcrypt_generic($resource, $base64_data);
    mcrypt_generic_deinit($resource);
    //後始末
    mcrypt_module_close($resource);

    $encrypted_data_base64 = base64_encode($encrypted_data);
    return $encrypted_data_base64;
}

/* -------------------------------------------------------------
 * 複合化関数
 * reversible_encryptで暗号化された文字列を複合化します。
 * $key、$ivにはreversible_encryptで利用した文字列と同じものを利用すること。
 * ex) $test2 = reversible_descript(CRYPT_KEY, CRYPT_IV, $test1);
 * ------------------------------------------------------------- */
function reversible_descript($key, $iv, $encrypted_data_base64){

    $encrypted_data = base64_decode($encrypted_data_base64);
    $resource = mcrypt_module_open(MCRYPT_BLOWFISH, '',  MCRYPT_MODE_CBC, '');
    mcrypt_generic_init($resource, $key, $iv);
    $base64_decrypted_data = mdecrypt_generic($resource, $encrypted_data);
    mcrypt_generic_deinit($resource);
    $decrypted_data = base64_decode($base64_decrypted_data);
    //後始末
    mcrypt_module_close($resource);

    return $decrypted_data;
}

/* -------------------------------------------------------------
 * hrefリンク相対→絶対パス変換
 * 指定文字列内の相対パスリンクを絶対パスに一括書き換え
 * ------------------------------------------------------------- */
function append_link($html,$baseurl='') {
  if($baseurl=="") $baseurl = 'http://'.$_SERVER['HTTP_HOST'].'/';
  $html = preg_replace('#(<a\s[^>]*?href\s*=["])(?!http)#i', '$1'.$baseurl, $html) ;
  $html = str_replace($_SERVER['HTTP_HOST'].'//', $_SERVER['HTTP_HOST'].'/', $html) ;
  return $html;
}

/* -------------------------------------------------------------
 * ディレクトリ名作成
 * DB opendate/idパラメータを使用
 * ------------------------------------------------------------- */
function makeDirPath($v,$basedir) {
  $str = array("/","-");
  $dir = $basedir.str_replace($str,"",$v['opendate'])."_id".$v['id']."/";
  return $dir;
}

/* -------------------------------------------------------------
 * ファイル情報の取得
 * DB opendate/idパラメータを使用
 * ------------------------------------------------------------- */
function getFileInfomation($file) {
  //拡張子取得
  $pi = pathinfo($file);
  $info['ext'] = strtoupper($pi['extension']);
  
  //サイズ取得
  $size  = filesize($file);
  $sizes = Array('Byte', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
  $unit  = $sizes[0];
  for ($i=1; (($i < count($sizes)) && ($size >= 1024)); $i++) {
    $size = $size / 1024;
    $unit = $sizes[$i];
  }
  $info['size'] = round($size).$unit;

  list($info['width'], $info['height'], $info['type'], $info['attr']) = getimagesize($file);

  return $info;
}

/* -------------------------------------------------------------
 * 画像リサイズ(汎用性なし)
 * 
 * ------------------------------------------------------------- */
function makeImageWH($width,$height,$no,$mode="width") {

  global $IMG_NEWS_WMAX;

  $max = ($mode == "width") ? $IMG_NEWS_WMAX[$no] : $IMG_NEWS_HMAX[$no];
  if($max > 0) {
      if($width > $max) {
        $height = round(($max / $width) * $height);
        $width  = $max;
      }
  }else{
    echo "img error".$no; exit;
  }
  
  // 横長か縦長か判定(横長なら1,縦長なら0)
  $whflg = ($width > $height) ? 1 : 0 ;

  return array("rwidth" => $width,"rheight" => $height,"whflg" => $whflg);

}

/* -------------------------------------------------------------
 * ディレクトリ削除
 * PHPの場合、ディレクトリ内に一つでもファイルがあると消せない為、
 * 独自に関数を用意する必要がある。
 * ------------------------------------------------------------- */
function removeDir($dir) {
  if ($handle = opendir("$dir")) {
   while (false !== ($item = readdir($handle))) {
     if ($item != "." && $item != "..") {
       if (is_dir("$dir/$item")) {
         removeDir("$dir/$item");
       } else {
         unlink("$dir/$item");
         //echo " removing $dir/$item<br>\n";
       }
     }
   }
   closedir($handle);
   rmdir($dir);
   //echo "removing $dir<br>\n";
  }
}


/* -------------------------------------------------------------
 * FileUploader
 * ------------------------------------------------------------- */
function fileUploader($_FILES,$key,$dir,$id) {
  
  // ファイル移動
  foreach ($_FILES['file']['name'][$key] as $k => $file_name) {
    if ($file_name != "") {
      $ext = array_pop(explode('.',$file_name));
      $tmpfile = TMP_DIR.$key.$k.".".$ext;
      // fujitsu arrows 用
      $upfile  = "/var/www/html/at/".$dir."file".$k."/".$id.".".$ext;
      rename($tmpfile,$upfile);
    }
  }
      
}

/* -------------------------------------------------------------
 * 登録済のファイル取得
 * ------------------------------------------------------------- */
function getRegisteredFile($name,$id) {
  if (!empty($id)) {
    $path = "/var/www/html/at/";
    $dir =  $path.constant($name."_DATA_DIR");
    for ($i=1;$i<=constant($name."_DATA_MAX");$i++) {
      $key            = "file".$i;
      $file_path_name = $dir.$key."/".$id."*";
      $image = glob($file_path_name);
      if (count($image)) $result["file".$i] = str_replace($path,"",$image[0]); 
    }
    
    return $result;
  }
}

/* -------------------------------------------------------------
 * FileUploader
 * ------------------------------------------------------------- */
function fileDelete($_FILES,$files,$key) {
  
  // ファイル削除
  foreach ($_FILES['file']['name'][$key] as $k => $file_name) {
    if ($file_name != "" OR $files[$key][$k]['del']) {
      $file_path_name = "/var/www/html/at/".$files[$key][$k]['now'];
      if (file_exists($file_path_name)) unlink($file_path_name);
    }
  }
      
}

/* 配列のキーをIDで返す
/* ------------------------------------------------------ */
function array_key_id ($array, $id_name) {
	foreach ($array as $v) {
		$id             = $v[$id_name];
		$new_array[$id] = $v;
	}
	return $new_array;
}

/* 配列のキーをIDで返す（1次元配列で返す）
/* ------------------------------------------------------ */
function array_key_id_flatten ($array, $id_name, $value_name) {
	foreach ($array as $v) {
		$id             = $v[$id_name];
		$value          = $v[$value_name];
		$new_array[$id] = $value;
	}
	return $new_array;
}

