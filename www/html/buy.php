<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'order.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();
$user = get_login_user($db);
$order_id = get_get('order_id');
$buys = get_user_buy($db, $order_id);
$order = get_user_order($db,$order_id);

if(is_admin($user) === false){
    if($user['user_id'] !== $order['user_id']){
      redirect_to(LOGIN_URL);
    }
   
  }


include_once VIEW_PATH . 'buy_view.php';