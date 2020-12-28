<?php

//結合
function get_user_orders($db, $user_id){
  $sql = "
    SELECT
      orders.order_id,
      orders.created,
      SUM(amount*price) AS total
    FROM
      orders
    JOIN
      buy
    ON
      orders.order_id = buy.order_id
    WHERE
      orders.user_id = ?
    GROUP BY
      order_id
    ORDER BY
      orders.created DESC  
  ";
  $params = array($user_id);
  return fetch_all_query($db, $sql,$params);
}

function get_user_buy($db,$order_id){
  $sql ="
   SELECT
     items.name,
     buy.price,
     buy.amount
    FROM
     buy
    JOIN
     items
    ON
     buy.item_id = items.item_id
    WHERE
     buy.order_id = ?
  ";
  $params = array($order_id);
  return fetch_all_query($db,$sql,$params);
}

function get_user_order($db, $order_id){
  $sql = "
    SELECT
      orders.order_id,
      orders.created,
      SUM(amount*price) AS total,
      orders.user_id
    FROM
      orders
    JOIN
      buy
    ON
      orders.order_id = buy.order_id
    WHERE
      orders.order_id = ?
    GROUP BY
      order_id
  ";
  $params = array($order_id);
  return fetch_query($db, $sql,$params);
}

function get_user_all_orders($db){
  $sql = "
  SELECT
  orders.order_id,
  orders.created,
  SUM(amount*price) AS total
FROM
  orders
JOIN
  buy
ON
  orders.order_id = buy.order_id
GROUP BY
  order_id
ORDER BY
  orders.created DESC  
";
$params = array();
return fetch_all_query($db, $sql,$params);
}

?>