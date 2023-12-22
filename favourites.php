<?php 
session_start();
echo "<a href='main.php'>Обратно</a><br>";
$users = json_decode(file_get_contents('users.json'), 1);
$goods = json_decode(file_get_contents('goods.json'), 1);
$user = $_SESSION['user'];

if (isset($_GET['delindex'])){
    for($i = 0; $i < count($users); $i++){
        if ($users[$i]['login'] == $user['login']) {
            unset($_SESSION['user']['favourites'][array_search($goods[$_GET['delindex']]['id'], $_SESSION['user']['favourites'])]);
            unset($users[$i]['favourites'][array_search($goods[$_GET['delindex']]['id'], $users[$i]['favourites'])]);
            file_put_contents('users.json', json_encode($users));
        }
    }
}
$user = $_SESSION['user'];
foreach($users as $userm){
    if ($userm['login'] == $user['login']){
        $temp = $userm['favourites'];
    }
}
$user['favourites'] = $temp;
$b = '';
foreach($goods as $key=>$a){
  if (in_array($a['id'], $user['favourites'])) {
    if ($b != $a['category']) {
        echo "<br><br>";
        echo "<b>Категория:</b> ", $a['category'], "<br><br>";
    }
    echo "<b>Название товара:</b> ", $a['name'], "<br>";
    echo "<b>Описание:</b> ", $a['description'], "<br>";
    echo "<b>Цена:</b> ", $a['price'], "<br>";
    if ($a['offer']) echo "<b>Акция:</b> ", $a['offer'], "<br>";
    if ($a['imageUrl'] != 'img/') echo "<br><img src=", $a['imageUrl'], " width='200px' /><br>";
    if (!$a['stock']){
        echo "<b>Отсутствует на складе</b><br>";
    } else {
        echo "<b>Количество на складе: </b>", $a['stock'];
    }
    echo "<br>";
    echo "<a href = '?delindex=$key'>Удалить объявление из избранного</a><br><br>";
    
    $b = $a['category'];
}
}


?>