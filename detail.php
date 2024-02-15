<?php
// エラーメッセージを格納する配列を初期化
$errors = [];

// 投稿IDを取得
$id = $_GET['id'];

// ボードの内容を読み込む
$bordContent = file_get_contents("bord.json");
$bord_array = json_decode($bordContent, true);

// コメントファイルの内容を読み込む
$commentFile = "comments.json";
$comments_array = json_decode(file_get_contents($commentFile), true);

// コメントを書くボタンを押した時の処理
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['comment_send'])) {
    $commentText = htmlspecialchars($_POST['comment_text'] ?? '', ENT_QUOTES);
    // コメントのバリデーション
    if (empty($commentText)) {
        $errors[] = "コメントは必須です。";
    } elseif (mb_strlen($commentText) > 50) {
        $errors[] = "コメントは50文字以下で入力してください。";

    // 上記条件に該当しなければコメントを書くことができる処理
    } else {
        // コメントを配列に追加
        $comments_array[$id][] = $commentText;
        // コメントをファイルに書き込む処理
        file_put_contents($commentFile, json_encode($comments_array, JSON_PRETTY_PRINT));
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/sanitize.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/detail.css">
    <title>Detail Page</title>
</head>
<body>
    <header>
        <h1><a href="index.php">Laravel News</a></h1>
    </header>
    <div class="news">
        <h2><?php echo $bord_array[$id]["title"] ?></h2>
        <!-- 改行を読み込むための関数 -->
        <p><?php echo nl2br($bord_array[$id]["message"]) ?></p>
    </div>
    <hr>
    <div class="errors">
        <ul>
            <?php $i = 0; ?>
            <?php while ($i < count($errors)): ?>
                <li><?php echo $errors[$i]; ?></li>
                <?php $i++; ?>
            <?php endwhile; ?>
        </ul>
    </div>
    <div class="comments">
        <div class="form">
            <form action="" method="post" class="box-item">
                <textarea id="comment_text" name="comment_text"></textarea>
                <input type="submit" name="comment_send" value="コメントを書く">
            </form>
        </div>
        <?php
            if (isset($comments_array[$id])) {
                $commentId = 0;
                $totalComments = count($comments_array[$id]);
                while ($commentId < $totalComments) {
                    $comment = $comments_array[$id][$commentId];
        ?>
                <div class="box-item">
                    <p><?php echo nl2br($comment); ?>
                        <!-- コメントを削除するためのファイルへ遷移するためのURLパラメータ -->
                        <a href="delete_comment.php?id=<?php echo $id; ?>&comment_id=<?php echo $commentId; ?>">コメントを消す</a>
                    </p>
                </div>
        <?php
                $commentId++;
            }
        }
        ?>
    </div>
    <script>
        const comments = document.querySelectorAll('.box-item');
        let i = 0;
        while (i < comments.length) {
            const comment = comments[i];
            comment.style.backgroundColor = ['#fff799', '#87cefa', '#ffdada'][Math.floor(Math.random() * 3)];
            i++;
        }
    </script>
</body>
</html>