<?php
$title = "";
$message = "";
$errors = [];
$bord_array = [];

// ファイルの読み込み
$jsonContent = file_get_contents("bord.json");

// ファイルにデータが入っているか確認する処理
if (!empty($jsonContent)) {
	// テキストファイルからjson形式をデコードして配列に格納
	// 一覧表示する時にこの変数を使う
    $bord_array = json_decode($jsonContent, true);
}

// 投稿ボタンが押された時の処理
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['send'])) {
    // 特殊文字をHTMLエンティティに変換する関数
    $title = htmlspecialchars($_POST['title'] ?? '', ENT_QUOTES);
    // タイトルのバリデーション
    if (empty($title)) {
        $errors[] = "タイトルは必須です。";
    } elseif (mb_strlen($title) > 30) {
        $errors[] = "タイトルは30文字以下で入力してください。";
    }

    $message = htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES);
    // メッセージのバリデーション
    if (empty($message)) {
        $errors[] = "記事は必須です。";
    }

	// エラーがなければ実行する処理
    if (empty($errors)) {
        // 新しい投稿を配列に追加
        $bord_array[] = [
            "title" => $title,
            "message" => $message
        ];
        
        // データをファイルに書き込む処理
        file_put_contents("bord.json", json_encode($bord_array, JSON_PRETTY_PRINT));
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/sanitize.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/home.css">
    <title>掲示板</title>
    <script>
        // 投稿ボタンを押した時に確認ダイアログを表示する処理
        function confirmSubmit() {
            return confirm("投稿してもよろしいでしょうか？");
        }
    </script>
</head>
<body>
    <header>
        <!-- ナビゲーションバーにホームに戻るリンクを貼る -->
        <h1><a href="index.php">Laravel News</a></h1>
    </header>
    <h2>さあ、最新のニュースシェアしましょう</h2>
    <div class="errors">
        <ul>
            <?php $i = 0; ?>
            <?php while ($i < count($errors)): ?>
                <li><?php echo $errors[$i]; ?></li>
                <?php $i++; ?>
            <?php endwhile; ?>
        </ul>
    </div>
    <form action="" method="post" onsubmit="return confirmSubmit()">
        <div class="form">
            <label for="title">タイトル：</label>
            <input type="text" id="title" name="title">
        </div>
        <div class="form">
            <label for="message">記事：</label>
            <textarea id="message" name="message"></textarea>
        </div>
        <div class="container">
            <input type="submit" name="send" value="投稿" class="btn">
        </div>
    </form>
    <hr>
    <div class="contents">
        <ul>
            <?php
                $index = 0;
                while ($index < count($bord_array)) {
                    $post = $bord_array[$index];
                    // 記事の改行を無くす処理（１列にするための処理）
                    $oneRowMessage = str_replace("\r\n", '', $post["message"]);
                    echo "<li>";
                    echo "<h3> ". $post["title"] . "</h3>";
                    echo "<p class='post'>" . $oneRowMessage . "</p>";
                    // URLパラメータで該当のidを指定して詳細画面へ飛べるようにする
                    echo "<a href='detail.php?id=" . $index ."'>記事全文・コメントを見る</a>";
                    echo "</li>";
                    echo "<hr>";
                    $index++;
                }
            ?>
        </ul>
    </div>
</body>
</html>