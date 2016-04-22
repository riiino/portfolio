<?php 
/* -------------------------------------------------------------
 * URI->DIRセレクタ
 * 外部関数：htmlspecialchars_encode_array()
 * ------------------------------------------------------------- */
class UriSelector{

  public $tplFile, $queryStr;

  function __construct($bu = _URI_){
  
    // $tplFile
    $reqUri        = $_SERVER['REQUEST_URI'];
    $reqUriArray   = explode('?', $reqUri);
    $reqDirBase    = $reqUriArray[0];
    $this->tplFile = str_replace($bu,'','http://'.$_SERVER['HTTP_HOST'].$reqDirBase);
    list($this->tplFile) = explode(".",$this->tplFile);
    $this->tplFile       = ($this->tplFile!="") ? $this->tplFile.".tpl":"index.tpl";
    $this->tplFile       = str_replace('/.tpl','/index.tpl',$this->tplFile);
    if(!file_exists('templates/'.$this->tplFile)) $this->tplFile = '';
    
    // $queryStr
    if(isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING']!=''){
      $queryParamArray  = array();
      $queryStrArray    = explode('&',$_SERVER['QUERY_STRING']);
      if(is_array($queryStrArray)){
        foreach($queryStrArray as $v){
          list($name,$value)      = explode('=',$v);
          $queryParamArray[$name] = $value;
        }
      }
      $this->queryStr = htmlspecialchars_encode_array($queryParamArray);
    }else{
      $this->queryStr = '';
    }
    
  }
}