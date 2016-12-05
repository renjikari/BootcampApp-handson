<?php
function is_user_exist($pdo,$username)
	{
        $stmt = $pdo -> prepare("select count(*) from users where username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        //$row = $stmt->fetch(); //$row : array(2) { ["count(*)"]=> string(1) "0" [0]=> string(1) "0" }
        $row = $stmt->fetchColumn(); //$row : string(1) "0"
        $user_count = intval($row);
        //var_dump($row);
        //echo "$row[0]";
        return $user_count;
	}

    // ユーザ情報をデータベースに登録する関数
    function register_user($pdo,$username,$hash_password)
    {
		$stmt = $pdo -> prepare("INSERT INTO users (username,password) values (:username,:hash_password)");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':hash_password', $hash_password, PDO::PARAM_STR);
        return $stmt->execute();
    }

    function get_user_data($pdo,$username){
        $stmt = $pdo -> prepare("select * from users where username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(); //$row :"
        //var_dump($user);
        //echo "$row[0]";
        return $user;
    }

    function get_all_users($pdo){
    	$stmt = $pdo->prepare("select * from users");
        $stmt->execute();
        return $all_users = $stmt->fetchAll();
    }

    function get_except_me($pdo,$user_id){
//    	$stmt = $pdo->prepare("select * from users where id != :user_id");
    	$stmt = $pdo->prepare("
    		SELECT users.id,users.username,follows.id AS follows_id,follows.user_id,follows.follow_user_id 
    		FROM users LEFT JOIN follows 
    		ON follows.user_id =:user_id AND follows.follow_user_id = users.id 
    		WHERE users.id != :user_id
    	");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->execute();
        return $except_me = $stmt->fetchAll();
    }

    function save_tweets($pdo,$user_id,$tweets){
		$stmt = $pdo -> prepare("INSERT INTO tweets (user_id,tweet,created) values (:user_id,:tweet,now())");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindParam(':tweet', $tweets, PDO::PARAM_STR);
        return $stmt->execute();
    }

    //$uesr_idのツイートをすべて取得
    //のちのち、created(datetime)も取っていきたい
    function get_my_tweets($pdo,$user_id){
        $stmt = $pdo -> prepare("select tweet from tweets where user_id = :user_id order by created DESC");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->execute();
        $user_tweets = $stmt->fetchAll(); //$row :"
        return $user_tweets;
    }

    //followしたユーザを追加する関数
    function add_follow_user($pdo,$user_id,$follow_user_id){

    	//まずfollow済みじゃないかチェックする
    	$follow_users = get_follow_users($pdo,$user_id);
    	   // followしているユーザを取得
	    foreach ($follow_users as $value) {
  			if ($follow_user_id == $value["follow_user_id"]){
	       //     echo "$value[follow_user_id]";
 				return "Already followed";
  			}
        	// echo "<br />\n";
        	// echo "$value[follow_user_id]";
        	// // var_dump($value);
        	//print_r ($tmp[0]);
   		}

    	$stmt = $pdo -> prepare("INSERT INTO follows (user_id,follow_user_id) values (:user_id,:follow_user_id)");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindParam(':follow_user_id', $follow_user_id, PDO::PARAM_STR);
        return $stmt->execute();
    }

    //followしたユーザをgetする関数
    function get_follow_users($pdo,$user_id){
    	$stmt = $pdo -> prepare("select * from follows where user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->execute();
        $follow_users = $stmt->fetchAll(); //$row :"
        return $follow_users;
    }

    function unfollow_user($pdo,$user_id,$unfollow_user_id) {
 	 	$stmt = $pdo -> prepare("
 	 		DELETE from follows where user_id = :user_id and follow_user_id = :unfollow_user_id
 	 		");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindParam(':unfollow_user_id', $unfollow_user_id, PDO::PARAM_STR);
        return $stmt->execute();
   }

   // function get_timeline($pdo,$user_id){
 	 //    $stmt = $pdo -> prepare("
			// 		select tweet,user_id from tweets 
			// 		where user_id in (select follow_user_id from follows where user_id = :user_id) 
			// 		order by created desc
			// 	");
   //      $stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
   //      $stmt->execute();
   //      $timeline = $stmt->fetchAll(); //$row :"
   //      return $timeline;

   // }

   function get_timeline($pdo,$user_id){
 	    $stmt = $pdo -> prepare("
 	    	SELECT tweets.tweet,users.username FROM tweets 
 	    	JOIN users ON tweets.user_id = users.id 
 	    	WHERE user_id IN (select follow_user_id from follows where user_id =:user_id) 
 	    	OR user_id =:user_id order by created desc
		"); 
		$stmt->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->execute();
        $timeline = $stmt->fetchAll(); //$row :"
        return $timeline;
	}
  
?>