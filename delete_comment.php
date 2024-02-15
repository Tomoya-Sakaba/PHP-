<?php
if (isset($_GET['id']) && isset($_GET['comment_id'])) {
    // URLパラメータから各idを取得
    $postId = $_GET['id'];
    $commentId = $_GET['comment_id'];

    $commentFile = "comments.json";
    $comments_array = [];

    // ファイルの読み込み
    $commentJsonContent = file_get_contents($commentFile);
    if (!empty($commentJsonContent)) {
        $comments_array = json_decode($commentJsonContent, true);
    }

    // 該当のコメントを削除する処理
    unset($comments_array[$postId][$commentId]);
    // 該当のコメントが削除された新しい配列
    $comments_array[$postId] = array_values($comments_array[$postId]);

    // 削除後の配列をテキストファイルに書き込む処理
    file_put_contents($commentFile, json_encode($comments_array, JSON_PRETTY_PRINT));
}

// 元のページにリダイレクト
header("Location: detail.php?id=$postId");
exit();
?>
