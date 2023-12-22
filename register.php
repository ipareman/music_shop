<?php 
$users = json_decode(file_get_contents('users.json'), 1);
$reg = False;
$logs = [];
foreach ($users as $user) {
  array_push($logs, $user['login']);
}

if(isset($_POST['password1'])){
  if ($_POST['password1'] != $_POST['password2']) {
    echo "введенные пароли не совпадают";
  } else if (in_array($_POST['login'], $logs)){
    echo "введите другой логин";
  } else {
    echo "регистрация успешна<br>";
    $reg = True;
    array_push($users, ["name" => $_POST['name'], "login" => $_POST["login"], "password" => $_POST["password1"], "role" => 'user', "favourites" => array()]);
    file_put_contents('users.json', json_encode($users));
    echo "<a href='main.php'>Все товары</a>";
  }
}
if (!$reg) {
?>

<form action="" method="POST">
    Введите имя: <br>
    <input name="name" required> <br><br>
    Введите email: <br>
    <input type = "email" name="email" required> <br><br>
    Введите логин: <br>
    <input name="login" required> <br><br>
    Введите пароль: <br>
    <input name="password1" required> <br><br>
    Подтвердтие пароль: <br>
    <input name="password2" required> <br><br>
    <button type="submit">Зарегестрироваться</button>
</form>


<?php } 