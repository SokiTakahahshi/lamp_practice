<?php 
//functions.php 読み込む
require_once MODEL_PATH . 'functions.php';
//db.php 読み込み
require_once MODEL_PATH . 'db.php';
//結合
function get_user_carts($db, $user_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
  ";
  $params = array($user_id);
  return fetch_all_query($db, $sql,$params);
}
//結合
function get_user_cart($db, $user_id, $item_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
    AND
      items.item_id = ?
  ";
  $params = array($user_id,$item_id);
  return fetch_query($db, $sql,$params);

}
function add_cart($db, $user_id, $item_id ) {
  //carts.itme_id = items.item_id　の結合を$cartに代入
  $cart = get_user_cart($db, $user_id, $item_id);
  //$cartがfalseなら
  if($cart === false){
  //falseならINSERT INTO  
    return insert_cart($db, $user_id, $item_id);
  }
  //UPDATE
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

function insert_cart($db, $user_id, $item_id, $amount = 1){
  //INSERT INTO文を$sqlに代入
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES(?,?,?)
  ";
  $params = array($item_id,$user_id,$amount);
  return execute_query($db, $sql,$params);
}

function update_cart_amount($db, $cart_id, $amount){
  //UPDATE文を$sqlに代入
  $sql = "
    UPDATE
      carts
    SET
      amount = ?
    WHERE
      cart_id = ?
    LIMIT 1
  ";
  $params = array($amount,$cart_id);
  return execute_query($db, $sql,$params);
}

function delete_cart($db, $cart_id){
  //DELETEに代入
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = ?
    LIMIT 1
  ";
  $params = array($cart_id);
  return execute_query($db, $sql,$params);
}

function purchase_carts($db, $carts){
  //$cartsの中身が0じゃないとき
  if(validate_cart_purchase($carts) === false){
    return false;
  }
  foreach($carts as $cart){
    //UPDATE
    if(update_item_stock(
        $db, 
        $cart['item_id'], 
        $cart['stock'] - $cart['amount']
      ) === false){
      //stockからamountを正常に引けなかったらエラー  
      set_error($cart['name'] . 'の購入に失敗しました。');
    }
  }
  //user_idのcartの中身をdelete
  delete_user_carts($db, $carts[0]['user_id']);
}

function delete_user_carts($db, $user_id){
  //user_idをdelete
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = ?
  ";
  $params = array($user_id);
  //プレースホルダー
  execute_query($db, $sql,$params);
}

//price*amountを足していく
function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}

function validate_cart_purchase($carts){
  //countが0の時
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  //cartにうつす
  foreach($carts as $cart){
    //statusが1じゃない時
    if(is_open($cart) === false){
      //エラーメッセージ
      set_error($cart['name'] . 'は現在購入できません。');
    }
    //在庫が0以下になる時エラー
    if($cart['stock'] - $cart['amount'] < 0){
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  if(has_error() === true){
    return false;
  }
  return true;
}

