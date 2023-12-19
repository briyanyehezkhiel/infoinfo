<?php 

session_start();

require 'functions.php';

$commentId = $_POST['commentId'];
$articleId = $_POST['articleId'];

// You may want to perform additional validation and authorization checks here

// Delete the comment from the database
$deleteCommentQuery = "DELETE FROM article_comment WHERE id = '$commentId'";
$deleteResult = $conn->query($deleteCommentQuery);

if ($deleteResult) {
    $updateArticleQuery = "UPDATE article SET article_comment = article_comment - 1 WHERE id = '$articleId'";
        $updateResult = $conn->query($updateArticleQuery);
    // Return a success message or any other response
    echo "Comment deleted successfully";
} else {
    // Return an error message or handle errors accordingly
    echo "Error deleting comment: " . $conn->error;
}


 ?>