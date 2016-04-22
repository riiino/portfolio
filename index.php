<?php
session_start();

#外部読み込みファイル群
include ('web-config.php');

/* initial
 * ------------------------------------------------------------------ */
$meta          = array();
$mored         = array();
$pa            = array();

/* オブジェクト生成
 * ------------------------------------------------------------------ */
$smObj    = new Smarty;
$selObj   = new UriSelector(_URI_);

/* Smarty
 * ------------------------------------------------------------------ */
$smObj->template_dir = dirname(__FILE__).'/templates/';
$smObj->compile_dir  = dirname(__FILE__).'/templates_c/';

/* エスケープ処理
 * ------------------------------------------------------------------ */
if ($_SERVER["REQUEST_METHOD"] == "POST") {$pa = htmlspecialchars_encode_array($_POST);}
else {$pa = htmlspecialchars_encode_array($_GET);}

/* ページコントローラ
 * ------------------------------------------------------------------ */
if(isset($selObj->tplFile) && $selObj->tplFile!=""){
  $incfile = 'includes/'.str_replace("tpl","php",$selObj->tplFile);
	if(file_exists($incfile))	include ($incfile);
}

/* 共通処理
 * ------------------------------------------------------------------ */

/*
 * assign ----------------------------------------------------------- */
$smObj->assign('meta',$meta);
$smObj->assign('mored',$mored);

/*
 * display ---------------------------------------------------------- */
if($selObj->tplFile != '') {
  $smObj->display($selObj->tplFile);
} else {
  $smObj->display('common/404.tpl');
}
