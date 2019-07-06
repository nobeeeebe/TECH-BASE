<html>
<head>
  <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes"><!-- for smartphone. ここは一旦、いじらなくてOKです。 -->
	<meta charset="utf-8"><!-- 文字コード指定。ここはこのままで。 -->
	<title>mission5-1</title>
</head>
<body>

<form action="mission_5-1.php" method="post" >
	名前:<input type="text" name="name" value="name"><br>
  コメント:<input type="text" name="comment" value="comment"><br>
  投稿番号:<input type="text" name="num"><br>
  パスワード:<input type="text" name="pass" value="1"><br>
  <input type="submit" name="submit" value="送信"><input type ="submit" name="delete" value="削除"><br>
  <hr>
  <p>修正フォーム(指定した番号の投稿を修正します)</p>
  名前:<input type="text" name="rev_name" value="name"><br>
  コメント:<input type="text" name="rev_comment" value="comment"><br>
  投稿番号:<input type="text" name="rev_num"><br>
  パスワード:<input type="text" name="rev_pass"><br><input type ="submit" name="revision" value="修正"><br>
</form>
</body>
</html>

<?php

//接続
$dsn = 'mysql:dbname='データベース名';host=localhost';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));


//テーブル作成
$sql = "CREATE TABLE IF NOT EXISTS mission5"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "date TEXT,"
	. "pass TEXT"
	.");";
	$stmt = $pdo->query($sql);

//投稿
if(isset($_POST['submit'])){
  if($_POST['comment']==NULL || $_POST['name']==NULL || $_POST['pass']==NULL){
    echo "名前，コメント，パスワード全てを入力してください<br>";
  }else{
    $sql = $pdo -> prepare("INSERT INTO mission5(name,comment,date,pass)
    VALUES (:name,:comment,:date,:pass)");
      $date = date("Y-m-d H:i:s");
      $sql -> bindParam(':name', $_POST['name'], PDO::PARAM_STR);
      $sql -> bindParam(':comment', $_POST['comment'], PDO::PARAM_STR);
      $sql -> bindParam(':date', $date);
      $sql -> bindParam(':pass', $_POST['pass'], PDO::PARAM_STR);
    	$sql -> execute();

  }
}

//削除，修正のフラグ立て
$rev = 0;
$del = 0;
if(isset($_POST['delete'])||isset($_POST['revision'])){
  if($_POST['comment']==NULL || $_POST['name']==NULL || $_POST['pass']==NULL){
    echo "名前，コメント，パスワード全てを入力してください<br>";
  }elseif($_POST['num']==NULL&&$_POST['rev_num']==NULL){
    echo "投稿番号を指定してください<br>";
  }else{
    $sql = 'SELECT * FROM mission5';
	  $stmt = $pdo->query($sql);
	  $results = $stmt->fetchAll();
	  foreach ($results as $row){
      if($row['id']==$_POST['rev_num'] && $row['pass']==$_POST['rev_pass']){
        $rev=1;
      }
      if($row['id']==$_POST['num'] && $row['pass']==$_POST['pass']){
        $del=1;
      }
	   }
   }
}

//削除
if($del){
  $sql = 'delete from mission5 where id=:id';
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':id', $_POST['num'], PDO::PARAM_INT);
  $stmt->execute();
}


//修正
if($rev){
  $id = $_POST['rev_num']; //変更する投稿番号
	$sql = 'update mission5 set id=:id,name=:name,comment=:comment where id=:id';
	$stmt = $pdo->prepare($sql);
  $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->bindParam(':name', $_POST['rev_name'], PDO::PARAM_STR);
	$stmt->bindParam(':comment', $_POST['rev_comment'], PDO::PARAM_STR);
	$stmt->execute();
}

//掲示板表示
$sql = 'SELECT * FROM mission5';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].' ';
		echo $row['name'].' ';
		echo $row['comment'].' ';
		echo $row['date'].' ';
	echo "<hr>";
	}
