<?php
/* -------------------------------------------------------------
 * 基本設定ファイル
 * ------------------------------------------------------------- */

/* MODE設定 (エラー設定)
 * ------------------------------------------------------------- */
ini_set('display_errors', 0);
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_DEPRECATED);

/* URI設定
 * ------------------------------------------------------------- */
define('_URI_', 'http://'.$_SERVER['HTTP_HOST'].'/');
define('_URI_SSL_', 'https://'.$_SERVER['HTTP_HOST'].'/');
define('_URI_DIR_', '/');

/* MAIL設定
 * ------------------------------------------------------------- */
//$mail_address           = "arrows_request@fcam.jp"; // 本番用
//$mail_address_developer = "mpsc-apiquery@ml.css.fujitsu.com"; // API質問投稿箱用
//$mail_address = "tomonari.suzuki@fourier.jp"; // デバッグ用

/* MySQL設定 （$mdb2）
 * ------------------------------------------------------------- */
define('DB_NAME',     'db_name');
define('DB_USER',     'user_name');
define('DB_PASSWORD', 'password');
define('DB_HOST',     'localhost');
define('DB_CHARSET',  'utf8');
define('DB_TYPE',     'mysql');


/* 共通設定
 * ------------------------------------------------------------- */

/* ディレクトリ/URL設定
 * ------------------------------------------------------------- */
define('__ABSPATH__', dirname(__FILE__) . '/');

/* CMS設定
 * ------------------------------------------------------------- */
define("TMP_DIR","tmp/"); // mdb-index.phpからの相対パス
define("IMG_MAXSIZE","1000000");    // 1MB
define("FILE_MAXSIZE","10000000");  // 10MB
define("ALL_MAXSIZE","10000000");  // 10MB
define("FLV_MAXSIZE","50000000");   // 50MB

/** allowed extension */
define("IMG_EXT","jpg,JPG");
define("FILE_EXT","pdf,ppt,xls,doc,xlsx,docx");
define("FLV_EXT","flv");
define("ALL_EXT",IMG_EXT.",".FILE_EXT);

// Cache
define("CACHE_CLEAR",time());

/* 初期化
 * ------------------------------------------------------------- */
include (__ABSPATH__.'libs/initial.inc.php');
