<?php
    // POSTでリクエストがきた場合
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // データベースに接続
        try {
            $db = new PDO('mysql:host=localhost;dbname=twitter_clone;charset=utf8', 'root', 'aaaaaa');
        } catch(PDOException $e) {
            print('Error: '.$e->getMessage());
            exit();
        }

        // 当該ユーザ名の情報を取得する
        $query = $db->prepare('SELECT * FROM users WHERE username = ?');
        $query->execute([$username]);
        $user = $query->fetch();

        // 当該ユーザが存在する場合、パスワードが合っているか確認する
        if($user !== false) {
            $is_correct = password_verify($password, $user['password']);

            if($is_correct) {
                // パスワードが合っていた場合、セッションを開始し、ツイートページにリダイレクトする
                session_start();
                $_SESSION['user_id'] = $user['id'];

                // ツイートページにリダイレクトする
                header('Location: ./tweets.php');
                exit();
            }
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