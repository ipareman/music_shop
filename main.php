<?php 
session_start();
$goods = json_decode(file_get_contents('goods.json'), 1);
$id = 0;
foreach ($goods as $good){
  if ($id <= $good['id']){ 
    $id = $good['id'];
  }
  $id += 1;
}

$allcats = [];
foreach($goods as $good){
  array_push($allcats, $good['category']);
}
$cats = array_unique($allcats);
$users = json_decode(file_get_contents('users.json'), 1);
$auth = False;
$c = 0;
if (isset($_POST["logout"])) {
  unset($_SESSION['user']);
}

$_SESSION['role'] = 'user';

$key = '';
if (isset($_SESSION['user'])){
  echo "Вы авторизованы. Ваше имя: ", $_SESSION['user']["name"], "<br>";
  if ($_SESSION['user']['role'] == 'user') {echo "<a href='favourites.php'>Ваши избранные товары</a><br>"; };
  $auth = True;
  $_SESSION['role'] =  $_SESSION['user']["role"];
  echo "Ваша роль: ", $_SESSION['role'], "<br><br>";
  ?> 
  <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="POST">
    <button type="submit" name="logout" value="<?=$key?>">Выйти из аккаунта</button>
  </form>
  <?php
} else if(isset($_POST['password'])){
  foreach($users as $user){
    if($user['login'] == $_POST['login'] and $user['password'] == $_POST['password']){
      $_SESSION['user'] = $user;
      $_SESSION['role'] = $_SESSION['user']['role'];
      $_SESSION['login'] = $_SESSION['user']['login'];
      $_SESSION['user']['favourites'] = [];
      echo "Вы авторизованы. Ваше имя: ", $user["name"], "<br>";
      if ($_SESSION['role'] == 'user') {echo "<a href='favourites.php'>Ваши избранные товары</a><br>"; };
      $auth = True;
      $c = 1;
      echo "Ваша роль: ", $_SESSION['role'], "<br><br>";
      ?> 
      <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="POST">
        <button type="submit" name="logout" value="<?=$key?>">Выйти из аккаунта</button>
      </form>
      <?php
      break;
    }
  }
  if($c == 0) echo "Неправильный логин или пароль";
}
if (!isset($_SESSION['user'])) {
  echo "<a href='register.php'>Регистрация</a><br>";
}
if (!$auth) {
?>


<form action="" method="POST">
    Введите логин: <br>
    <input name="login" required> <br><br>
    Введите пароль: <br>
    <input name="password" required> <br><br>
    <button type="submit">Войти</button>
</form>


<?php }
if ($_SESSION['role'] == 'admin') {
?>

Форма добавления товара: <br>
<form action="" method="POST"> 
    Введите название: <br>
    <input name="name" required> <br><br>
    Введите описание: <br>
    <textarea name="description" required> </textarea><br><br>
    Введите категорию: <br>
    <input name="category"> <br><br>

    Выберите категорию: <br>
    <select name="select_category">
      <?php foreach($cats as $value){?>
        <option value="<?php echo $value;?>"> <?php echo $value; ?> </option>
      <?php } ?>
    </select><br><br>

    Введите цену:
    <input type="number" name="price" required> <br><br>
    Введите акцию:
    <input name="offer"> <br><br>
    Вставьте изображение: <br>
    <input type="file" name="img"><br><br>
    Сколько на складе?<br>
    <input type="number" name="stock" required> <br><br>
    <button type="submit">Добавить товар</button>
</form>


<?php
}

  if (isset($_GET['favorites'])){ 
    for ($i=0; $i<count($users); $i++){
      if ($users[$i]['login'] == $_SESSION['login']) {
        if (!in_array($goods[$_GET['favorites']]['id'], $users[$i]['favourites'])) {
          array_push($users[$i]['favourites'], $goods[$_GET['favorites']]['id']);
          file_put_contents('users.json', json_encode($users));
          array_push($_SESSION['user']['favourites'], $goods[$_GET['favorites']]['id']);
        }
      }
    }

  }

  if(isset($_GET['delindex'])){
    unset($goods[$_GET['delindex']]);
    $goods = array_values($goods);
    usort($goods, function($a, $b) {return strcmp($b["category"], $a["category"]);});
    file_put_contents('goods.json', json_encode($goods));
  }
  
  else if (isset($_POST['name'])){
    if (!empty($_POST['category'])){
      array_push($goods, ['name'=>$_POST['name'], 'description'=> $_POST['description'], 'category'=>$_POST['category'], 'price'=>$_POST['price'], 'imageUrl'=>'img/'.$_POST['img'], 'stock'=>$_POST['stock'], 'offer'=>$_POST['offer'], 'id'=>$id]);
    } else {
      array_push($goods, ['name'=>$_POST['name'], 'description'=> $_POST['description'], 'category'=>$_POST['select_category'], 'price'=>$_POST['price'], 'imageUrl'=>'img/'.$_POST['img'], 'stock'=>$_POST['stock'], 'offer'=>$_POST['offer'], 'id'=>$id]);
    }
    usort($goods, function($a, $b) {return strcmp($b["category"], $a["category"]);});
    file_put_contents('goods.json', json_encode($goods));

  } else {
    usort($goods, function($a, $b) {return strcmp($b["category"], $a["category"]);});
  }


$b = '';

foreach($goods as $key=>$a){
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
  if ($_SESSION['role'] == "admin") {
    echo "<a href = '?delindex=$key'>Удалить объявление</a><br><br>";
  } else if($auth) {
    echo "<a href = '?favorites=$key'>Добавить в избранное</a><br><br>";
  }
  $b = $a['category'];
}
?>

