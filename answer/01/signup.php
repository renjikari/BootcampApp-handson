<?php
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

        // 既に同じユーザ名で登録されているユーザがいないか確認する
        $query = $db->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
        $query->execute([$username]);
        $users_count = $query->fetchColumn();

        // ユーザ名が登録されていなければ、新規登録を行なう
        if($users_count == 0) {
            // パスワードをハッシュ化する
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // ユーザ情報をデータベースに登録する
            $query = $db->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
            if($query->execute([$username, $password_hash])) {
                // 登録成功した場合、トップページにリダイレクトする
                header('Location: ./index.php');
                exit();
            }
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Twitter Clone - Signup</title>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
        <link href="main.css" rel="stylesheet">
    </head>
    <body>
        <div class="container">
            <form method="POST" action="./signup.php" class="form-signin">
                <h2 class="form-signin-heading">新規登録</h2>
                <input type="text" name="username" class="form-control" placeholder="Username">
                <input type="password" name="password" class="form-control" placeholder="Password">
                <input type="submit" class="btn btn-lg btn-primary btn-block" value="登録する">
            </form>
        </div>
    </body>
</html>
