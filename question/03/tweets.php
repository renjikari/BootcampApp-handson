<?php
    // データベースに接続
    require "../functions/connect_db.php";
    require "../functions/functions.php";
    ini_set('display_errors', 1 );

    // セッションを開始
    session_start();

    // セッションからユーザIDを取得する
    $user_id = $_SESSION['user_id'];
    //var_dump($user_id);
  
    // セッションにユーザIDがない場合はエラー表示
    if ($user_id == null){
        echo "不正なユーザです";
        exit();
        //ここでログインページへのリンクとか
    }

    // 自身を除くユーザの一覧を取得する
    $users_except_me = get_except_me($pdo,$user_id);

    //followしているユーザを取得
    $follow_users = get_follow_users($pdo,$user_id);

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if($_POST['action'] == "tweet"){
           $tweets = $_POST['tweets'];
            //var_dump($tweets);
            //ツイートと現在時刻をDBに保存 エラーならなんか返してあげたい
            $success_save_tweets = save_tweets($pdo,$user_id,$tweets);

        //fllowリクエストがきた場合
        }elseif ($_POST['action'] == "follow") {
            $follow_user_id = $_POST['follow_user_id'];
//            var_dump($follow_user_id);
 //           var_dump($user_id);
            //follow_userするユーザを追加
            //followしているユーザをfollowできないようにしたい
            $tmp = add_follow_user($pdo,intval($user_id),intval($follow_user_id));

            if ($tmp == 'true') {
                header("Location: " . $_SERVER['PHP_SELF']);
            }

        //fllow解除リクエストがきた場合
        }elseif ($_POST['action'] == "unfollow") {
            $unfollow_user_id = $_POST['unfollow_user_id'];

            $tmp = unfollow_user($pdo,intval($user_id),intval($unfollow_user_id));
            if ($tmp == 'true') {
                header("Location: " . $_SERVER['PHP_SELF']);
            }
    }  
}

    //$my_tweets = get_my_tweets($pdo,$user_id);
    $timeline = get_timeline($pdo,$user_id);
    //var_dump($my_tweets);

    // foreach ($my_tweets as $value) {
    //     echo "<br />\n";
    //     echo "$value[0]";
    //     //print_r ($tmp[0]);
    // }
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

                <?php foreach($users_except_me as $value): ?>
                    <div class="box">
                        <p><?= $value["username"]?></p>

                        <form action="./tweets.php" method="post">

                            <?php if($value["follow_user_id"] == NULL): ?>
                                <input type="hidden" name="follow_user_id" value="<?= $value["id"]?>">
                                <input type="hidden" name="action" value="follow">
                                <input class="btn btn-primary btn-sm" type="submit" value="フォローする">
                            <?php else: ?>
                                <input type="hidden" name="unfollow_user_id" value="<?= $value["id"]?>">
                                <input type="hidden" name="action" value="unfollow">
                                <input class="btn btn-danger btn-sm" type="submit" value="フォローをはずす">
                            <?php endif; ?>
                        </form>
                    </div>
                <?php endforeach; ?>

            </div>

            <div class="col-xs-9">
                <!-- ツイートフォーム -->

                <div class="box">
                    <form action="./tweets.php" method="post" class=form-tweet>
                        <input type="hidden" name="action" value="tweet">
                        <textarea name="tweets" style="width:100%;height:auto" placeholder="いま何してる？"></textarea>
                        <div class=button>
                            <input class="btn btn-primary" type="submit" value="つぶやく">
                        </div>
                    </form> 

                    <?php foreach($timeline as $value): ?>
                        <div class="box">
                            <?= htmlspecialchars($value['username'], ENT_QUOTES, "utf-8") ?>
                            <?= htmlspecialchars($value['tweet'], ENT_QUOTES, "utf-8") ?>
                        </div>
                    <?php endforeach; ?>
                </div>   

            </div>
        </div>
    </div>
</body>
</html>
