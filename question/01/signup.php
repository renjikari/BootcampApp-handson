<?php

    require "../functions/functions.php";
    require "../functions/connect_db.php";
    ini_set('display_errors', 1 );

    if($_SERVER["REQUEST_METHOD"] == "POST") { 
        $username = $_POST['username'];
        $password = $_POST['password'];
    }

    //ここださくね…
    // 上のほうのrequestmethod==POSTのif文内で以下を実行しちゃえばよい
    //usernameとpasswordが設定されていないとsql文でエラーが返るので
    if(isset($username) && isset($password))  {

       // 既に同じユーザ名で登録されているユーザがいないか確認する
        $user_count = is_user_exist($pdo,$username);
    	if ( $user_count === 0 ) {

            // パスワードをハッシュ化する
            $hash_password = password_hash($password,PASSWORD_DEFAULT);

     		// ユーザ名が登録されていなければ、新規登録を行なう
        	$success_register_user = register_user($pdo,$username,$hash_password);
            
            //ユーザ登録が成功していればindex.phpに飛ばす
            if ($success_register_user){
                //session_start();
                //$_SESSION['username'] = $username;
                //$_SESSION['password'] = $password;
                header('Location: ../02/index.php');
                exit();
            }
        }else{
        	echo "すでにユーザ名が登録されているよ！";
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
