<?php

    require "../functions/functions.php";
    require "../functions/connect_db.php";
    ini_set('display_errors', 1 );

    //初期化
    $user_count = 0;

    // POSTでリクエストがきた場合
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];
/*    }elseif($_SERVER["REQUEST_METHOD"] == "GET") {
        echo "GET";
        session_start();
        $username = $_SESSION['username'];
        $password = $_SESSION['password'];
        var_dump($username);
       var_dump($password);
*/
    //ユーザが存在するか関数に聞く
    $user_count = is_user_exist($pdo,$username);
    }


    // 当該ユーザが存在する場合、パスワードが合っているか確認する
    if ($user_count !== 0){
        $user = get_user_data($pdo,$username);
        $hash = $user["password"];
        $user_id = $user["id"];
//        var_dump($user[password]);
        $password_correct = password_verify($password,$hash);
        //var_dump($password_correct);

        // パスワードが合っていた場合、セッションを開始し、ツイートページにリダイレクトする
        if ($password_correct){
            session_start();
            $_SESSION['user_id'] = $user_id;
            header('Location: ../03/tweets.php');
            exit();
        }
    } 

     
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Twitter Clone</title>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
        <link href="main.css" rel="stylesheet">
    </head>
    <body>
        <div class="container">
            <form method="POST" action="./index.php" class="form-signin">
                <h2 class="form-signin-heading">サインイン</h2>
                <input type="text" name="username" class="form-control" placeholder="Username">
                <input type="password" name="password" class="form-control" placeholder="Password">
                <input type="submit" class="btn btn-lg btn-primary btn-block" value="サインイン">
                <hr>
                <a href="./signup.php" class="btn btn-lg btn-success btn-block">新規登録する</button></a>
            </form>
        </div>
    </body>
</html>