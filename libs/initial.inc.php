<?php 
/* ----------------------------------------------------------------------------
 * システム初期設定
 * ------------------------------------------------------------------------ */
//ini_set('mbstring.internal_encoding', 'UTF-8');
//ini_set("include_path",ini_get("include_path").":".__ABSPATH__."libs:".__ABSPATH__."libs/PEAR:".__ABSPATH__."libs/PEAR/MDB2");

/** crypt keys setting */
define('CRYPT_KEY', 'wCp12mFr');   // 任意のキーコード
define('CRYPT_IV',  'fYcKg7q8');   // 8文字の文字列
define('LIBPATH', __ABSPATH__.'libs/');
define('TPLPATH', __ABSPATH__.'templates/');
define('MDB_VERSION', '1.2');

//set_include_path(get_include_path().PATH_SEPARATOR."/var/www/html/common/libs");


/* --------------------------------------------------------------------------
 * 外部読み込みファイル群
 * -------------------------------------------------------------------------- */
include ('MDB2.php'); // server include
include ('Pager/Pager.php'); // server include
include ('Pager/Sliding.php'); // server include
include ('Pager_Wrapper.php'); // server include
include ('Smarty/Smarty.class.php'); // server include
include (__ABSPATH__.'libs/common.inc.php');
include (__ABSPATH__.'libs/functions.inc.php');


/* --------------------------------------------------------------------------
 * クラス外部ファイル自動読み込み関数
 * -------------------------------------------------------------------------- */
function __autoload($className) {
  include_once $className . '.class.php';
}


/* --------------------------------------------------------------------------
 * DB接続準備
 * -------------------------------------------------------------------------- */
//MySQL
$db_dsn = DB_TYPE."://".DB_USER.":".DB_PASSWORD."@".DB_HOST."/".DB_NAME."?charset=".DB_CHARSET;
$mdb2 =& MDB2::factory($db_dsn);
$mdb2->setFetchMode(MDB2_FETCHMODE_ASSOC);
$mdb2->loadModule('Extended'); // autoExecute()の有効化

/* --------------------------------------------------------------------------
 * ユーザー認証設定
 * -------------------------------------------------------------------------- */

//MDB
$authMdbParams = array(
  'dsn' => $db_dsn,
  'table' => 'mdb_users',
  'usernamecol' => 'username',
  'passwordcol' => 'password',
  'db_fields' => '*',
  'cryptType'=>'md5'
);

/* --------------------------------------------------------------------------
 * Pager Option
 * -------------------------------------------------------------------------- */
$pagerOptions = array(
    'mode'                  => 'sliding',
    'perPage'               => 50,
    'delta'                 => 10,
    'separator'             => '',
    'currentPage'           => 1,
    'curPageLinkClassName'  => 'current',
    'prevImg'               => '&#171; Previous',
    'nextImg'               => 'Next &#187;',
    'firstPagePre'          => '',
    'firstPagePost'         => '',
    'lastPagePre'           => '',
    'lastPagePost'          => '',
    'spacesBeforeSeparator' => 0,
    'spacesAfterSeparator'  => 0
  );
