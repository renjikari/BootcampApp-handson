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

    // POSTされたときの処理
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        // 渡されたactionがtweetの場合
        if($_POST['action'] == "tweet") {
            // tweetsテーブルにツイートを保存
            $query = $db->prepare('INSERT INTO tweets (user_id, tweet, created) VALUES (?, ?, NOW())');
            $query->execute([$user_id, $_POST['tweet']]);
        }
        // 「フォローする」ボタンが押された場合に、フォロー情報を保存せよ
        else if($_POST['action'] == "follow") {
            // followsテーブルにツイートを保存
            $query = $db->prepare('INSERT INTO follows (user_id, follow_user_id) VALUES (?, ?)');
            $query->execute([$user_id, $_POST['follow_user_id']]);
        }
        // アンフォロー処理の実装
        else if($_POST['action'] == "unfollow") {
            $query = $db->prepare('DELETE FROM follows WHERE user_id = ? AND follow_user_id = ?');
            $query->execute([$user_id, $_POST['follow_user_id']]);       
        }
    }

    // 自身を除くユーザの一覧を取得する
    $query = $db->prepare('
        SELECT 
            users.id,
            users.username,
            follows.id AS follows_id,
            follows.user_id,
            follows.follow_user_id
        FROM users 
        LEFT JOIN follows ON follows.user_id = ? AND follows.follow_user_id = users.id
        WHERE users.id != ?
    ');
    $query->execute([$user_id, $user_id]);
    $users = $query->fetchAll();

    // 自身のツイートを新しい順で取得
    $query = $db->prepare('SELECT * FROM tweets WHERE user_id = ? ORDER BY tweets.created DESC');
    $query->execute([$user_id]);
    $tweets = $query->fetchAll();
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
                            <form action="./tweets.php" method="POST">
                                <input type="hidden" name="follow_user_id" value="<?= $user['id'] ?>">
                                <?php if($user['follows_id'] == NULL): ?>
                                    <input type="hidden" name="action" value="follow">
                                    <input type="submit" class="btn btn-primary" value="フォロー">
                                <?php else: ?>
                                    <input type="hidden" name="action" value="unfollow">
                                    <input type="submit" class="btn btn-danger" value="アンフォロー">
                                <?php endif; ?>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="col-xs-9">
                    <!-- ツイートフォーム -->
                    <div class="box">
                        <form method="POST" action="./tweets.php" class="form-tweets">
                            <input type="hidden" name="action" value="tweet">
                            <textarea name="tweet" placeholder="なにしてる？"></textarea>
                            <div class="button">
                                <input type="submit" class="btn btn-default" value="ツイートする">
                            </div>
                        </form>
                    </div>

                    <!-- ツイート一覧 -->
                    <?php foreach($tweets as $tweet): ?>
                        <div class="box">
                            <?= $tweet['tweet'] ?>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        </div>
    </body>
</html>
