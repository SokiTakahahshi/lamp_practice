<?php
//確認
function dd($var){
  var_dump($var);
  exit();
}
//$urlに飛ぶ
function redirect_to($url){
  header('Location: ' . $url);
  exit;
}

function get_get($name){
  //$nameがセットされてたらGETで送信
  if(isset($_GET[$name]) === true){
    return $_GET[$name];
  };
  return '';
}

function get_post($name){
  //$nameがセットされてたらPOSTで送信
  if(isset($_POST[$name]) === true){
    return $_POST[$name];
  };
  return '';
}

function get_file($name){
  //$nameがセットされてたらファイルアップロード
  if(isset($_FILES[$name]) === true){
    return $_FILES[$name];
  };
  return array();
}

function get_session($name){
  //$naemがセットされてたら$nameをセッションに
  if(isset($_SESSION[$name]) === true){
    return $_SESSION[$name];
  };
  return '';
}

function set_session($name, $value){
  //$valueを$_SESSION[$name]に代入
  $_SESSION[$name] = $value;
}

function set_error($error){
  //$errorを$_SESSION['__errors'][]に連想配列で代入
  $_SESSION['__errors'][] = $error;
}

function get_errors(){
  //sessionを$errorsに代入
  $errors = get_session('__errors');
  //$errorsが空だったらarray()を返す
  if($errors === ''){
    return array();
  }
  set_session('__errors',  array());
  return $errors;
}

function has_error(){
  //($_SESSION['__errors']がセットされて、($_SESSION['__errors']が0じゃない
  return isset($_SESSION['__errors']) && count($_SESSION['__errors']) !== 0;
}

function set_message($message){
  //$messageをセッションに
  $_SESSION['__messages'][] = $message;
}

function get_messages(){
  //$messagesにセッションを代入
  $messages = get_session('__messages');
  if($messages === ''){
    return array();
  }
  set_session('__messages',  array());
  return $messages;
}
//user_idがからじゃなっかたらtrue
function is_logined(){
  return get_session('user_id') !== '';
}
//fileがfalseだったら空を返す
function get_upload_filename($file){
  if(is_valid_upload_image($file) === false){
    return '';
  }
  //画像の種類の判別
  $mimetype = exif_imagetype($file['tmp_name']);
  $ext = PERMITTED_IMAGE_TYPES[$mimetype];
  return get_random_string() . '.' . $ext;
}

function get_random_string($length = 20){
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}

function save_image($image, $filename){
  return move_uploaded_file($image['tmp_name'], IMAGE_DIR . $filename);
}

function delete_image($filename){
  if(file_exists(IMAGE_DIR . $filename) === true){
    unlink(IMAGE_DIR . $filename);
    return true;
  }
  return false;
  
}



function is_valid_length($string, $minimum_length, $maximum_length = PHP_INT_MAX){
  $length = mb_strlen($string);
  return ($minimum_length <= $length) && ($length <= $maximum_length);
}

function is_alphanumeric($string){
  return is_valid_format($string, REGEXP_ALPHANUMERIC);
}

function is_positive_integer($string){
  return is_valid_format($string, REGEXP_POSITIVE_INTEGER);
}

function is_valid_format($string, $format){
  return preg_match($format, $string) === 1;
}

//ファイル形式が正しいか
function is_valid_upload_image($image){
  if(is_uploaded_file($image['tmp_name']) === false){
    set_error('ファイル形式が不正です。');
    return false;
  }
  //利用出来るファイルを表示
  $mimetype = exif_imagetype($image['tmp_name']);
  if( isset(PERMITTED_IMAGE_TYPES[$mimetype]) === false ){
    set_error('ファイル形式は' . implode('、', PERMITTED_IMAGE_TYPES) . 'のみ利用可能です。');
    return false;
  }
  return true;
}

function h($h){
//htmlエスケープ
  return htmlspecialchars($h,ENT_QUOTES,"UTF-8");
}

//トークンの生成
function get_csrf_token(){
  // get_random_string()はユーザー定義関数。
  $token = get_random_string(30);
  // set_session()はユーザー定義関数。
  set_session('csrf_token', $token);
  return $token;
}
// トークンのチェック
function is_valid_csrf_token($token){
  if($token === '') {
    return false;
  }
  // get_session()はユーザー定義関数
  return $token === get_session('csrf_token');
}
