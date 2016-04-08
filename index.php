<?php
session_start();

header("Content-type: text/html; charset=UTF-8");

require_once('view/IndexView.php');

$view = new IndexView();

if($res = $view->fetch()){
  echo $res;
}else{
  echo '<div style="text-align:center;margin-top:50px;"><h1> Страница не существует <h1><br />
        <a href="http://diplom.my/"> Вернуться на главную </a>
        </div>';
}