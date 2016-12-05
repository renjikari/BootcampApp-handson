<?php
    // セッションを開始
    session_start();

    // セッションからユーザIDを取得する
    if(!isset($_SESSION['user_id'])) {
        // セッションにユーザIDがない場合はエラー表示
        header("HTTP/1.1 400 Bad Request");
        echo("400 Bad Request.");
        exit();
    }
    $user_id = $_SESSION['user_id'];

    // データベースに接続
    try {
        $db = new PDO('mysql:host=localhost;dbname=twitter_clone;charset=utf8', 'root', 'aaaaaa');
    } catch(PDOException $e) {
        print('Error: '.$e->getMessage());
        exit();
    }

    // 自身を除くユーザの一覧を取得する
    $query = $db->prepare('
        SELECT 
            users.id,
            users.username
        FROM users 
        WHERE users.id != ?
    ');
    $query->execute([$user_id]);
    $users = $query->fetchAll();

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Twitter Clone - Tweets</title>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
        <link href="main.css" rel="stylesheet">
    </head>
    <body>
        <div class="container">
            <!-- 左ペインと右ペインに分ける -->
            <div class="row">
                <div class="col-xs-3">
                    <!-- 他のユーザの名前一覧を出力する -->
                    <?php foreach($users as $user): ?>
                        <div class="box">
                            <?= $user['username'] ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="col-xs-9">
                    <!-- ツイートフォーム -->
                </div>
            </div>
        </div>
    </body>
</html>
