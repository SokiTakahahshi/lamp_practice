<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}
//formから送られてきたトークンを取得
$token = get_post('token');
if(is_valid_csrf_token($token)===false){
  set_error('不正なページ移動です。');
  redirect_to(ADMIN_URL);
}

$db = get_db_connect();

$user = get_login_user($db);
//falseだった場合リダイレクト
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}
//postを変数に代入
$item_id = get_post('item_id');
$stock = get_post('stock');
//在庫数の変更
if(update_item_stock($db, $item_id, $stock)){
  set_message('在庫数を変更しました。');
} else {
  set_error('在庫数の変更に失敗しました。');
}

redirect_to(ADMIN_URL);