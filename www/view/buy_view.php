<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入明細</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'cart.css'); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>購入明細画面</h1>
  <div class="container">

    <?php include VIEW_PATH . 'templates/messages.php'; ?>
    <?php if(count($order) > 0){ ?>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>注文番号</th>
            <th>購入日時</th>
            <th>合計金額</th>
          </tr>
        </thead>
        <tbody>
          <tr>
          　<td><?php print number_format($order['order_id']); ?></td>
            <td><?php print(h($order['created'])); ?></td>
            <!-- 合計金額 -->
            <td><?php print(number_format($order['total'])); ?>円</td>
          </tr>
        </tbody>
      </table>
    <?php } else { ?>
      <p>履歴がありません。</p>
    <?php } ?> 





    <?php if(count($buys) > 0){ ?>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>商品名</th>
            <th>商品価格</th>
            <th>購入数</th>
            <th>小計</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($buys as $buy){ ?>
          <tr>
            <td><?php print h(($buy['name'])); ?></td>
            <td><?php print(number_format($buy['price'])); ?></td>
            <td><?php print number_format($buy['amount']); ?></td>
            <!-- 合計金額 -->
            <td><?php print(number_format($buy['price']*$buy['amount'])); ?>円</td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } else { ?>
      <p>履歴がありません。</p>
    <?php } ?> 
  </div>
</body>
</html>