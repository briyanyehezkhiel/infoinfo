<?php 

$conn = mysqli_connect("localhost", "root", "", "websiteku",3307); //3307

function query($query){
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while( $row = mysqli_fetch_assoc($result) ){
        $rows[] = $row;
    }
    return $rows;
}

function tambah($data){
    global $conn;
    $email =  $_SESSION['login_email'];
    $username =  ucwords($_SESSION['login_givenName'] . " " .$_SESSION['login_familyName']);
    $title = htmlspecialchars($data['title']);
    $content = htmlspecialchars($data['content']);

    //upload gambar
    $gambar = upload();
    if(!$gambar){
        return false;
    }
    

    $query = "INSERT INTO article (user_email, username, title, content, gambar) VALUES ('$email', '$username','$title', '$content', '$gambar')";
    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);


    // Assuming 'login_email' is stored in $_SESSION

    // // Insert the article into the database
    // // Make sure to sanitize the input to prevent SQL injection
    // // Your database connection should be established beforehand
    // // Adjust the SQL query based on your table structure
    // $stmt = $pdo->prepare("INSERT INTO article (title, content, user_email) VALUES (?, ?, ?)");
    // $stmt->execute([$title, $content, $email]);

    // // Check for successful insertion
    // if ($stmt->rowCount() > 0) {
    //     echo "Article uploaded successfully.";
    // } else {
    //     echo "Failed to upload the article.";
    // }
      
}

function tambahUser(){
    global $conn;
    $email =  $_SESSION['login_email'];
    $username =  ucwords($_SESSION['login_givenName'] . " " .$_SESSION['login_familyName']);
    $profile_picture =  $_SESSION['login_picture'];
    $query = "INSERT INTO userinfo (user_email, username, profile_picture) VALUES ('$email', '$username', '$profile_picture')";

// cek username sudah ada atau belum 
    $result = mysqli_query($conn, "SELECT user_email FROM userinfo WHERE user_email = '$email'");

    if(mysqli_fetch_assoc($result)){
        return false;
    }

    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}

function hapus($id){
    global $conn;
    mysqli_query($conn, "DELETE FROM article WHERE id = $id");
    return mysqli_affected_rows($conn);
}


function upload(){
    $namaFile = $_FILES['gambar']['name'];
    $ukuranFIle = $_FILES['gambar']['size'];
    $error = $_FILES['gambar']['error'];
    $tmpName = $_FILES['gambar']['tmp_name'];

    //cek apakah tidak ada gambar yang diupload
    if($error === 4){
        echo "<script>
                alert('pilih gambar terlebih dahulu!);
              </script>";
        return false;

    }

    //cek apakah yang diupload adalah gambar
    $ekstensiGambarValid = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'mp4', 'webm', 'ogg'];
    $ekstensiGambar = explode('.', $namaFile);
    $ekstensiGambar = strtolower(end($ekstensiGambar));

    if(!in_array($ekstensiGambar, $ekstensiGambarValid)){
        echo "<script>
                alert('yang anda upload bukan gambar!);
              </script>";
        return false;
    }

    //cek jika ukurannya terlalu besar
    if($ukuranFIle > 100000000){
        echo "<script>
                alert('ukuran gambar terlalu besar!);
              </script>";
        return false;
    }

    //lolos pengecekan, gambar siap diupload
    // generate nama gambar baru

    $namaFileBaru = uniqid();
    $namaFileBaru .= '.';
    $namaFileBaru .= $ekstensiGambar;


    move_uploaded_file($tmpName, 'img/' . $namaFileBaru);

    return $namaFileBaru;


}

function ubah($data){
    global $conn;

    $id = $data["id"];
    $email =  $_SESSION['login_email'];
    $username =  ucwords($_SESSION['login_givenName'] . " " .$_SESSION['login_familyName']);
    $title = htmlspecialchars($data['title']);
    $content = htmlspecialchars($data['content']);
    $gambarLama = htmlspecialchars($data['gambarLama']);

    //cek apakah user pilih gambar baru atau tidak
    if($_FILES['gambar']['error'] === 4){
        $gambar = $gambarLama;
    } else{
        $gambar = upload();
    }

    $query = "UPDATE article SET
                title = '$title',
                content = '$content',
                gambar = '$gambar'
            WHERE id = $id
            ";
    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);

}

function cari($keyword){
    // SELECT DISTINCT article.id, article.title, article.content, article.gambar, userinfo.username, userinfo.profile_picture
    //       FROM article
    //       INNER JOIN userinfo ON article.user_email = userinfo.user_email

    //tambahkan ke versi 2
    $query = "SELECT DISTINCT article.id, article.article_like, article.article_dislike, article.gambar, article.title, article.article_comment, article.created_at, article.content, userinfo.id AS user_id, userinfo.username, userinfo.profile_picture
          FROM article
          INNER JOIN userinfo ON article.user_email = userinfo.user_email
          WHERE article.id IN (
              SELECT article.id
              FROM article
              WHERE userinfo.username LIKE '%$keyword%'
                 OR article.title LIKE '%$keyword%'
                 OR article.content LIKE '%$keyword%'
          )";

    return query($query);
}

function suka($articleId, $action, $userId){
    global $conn;

    // Check if the user has already performed the action
    $checkQuery = "SELECT * FROM article_like WHERE user_id = $userId AND article_id = $articleId";
    $checkResult = $conn->query($checkQuery);

    if ($checkResult->num_rows > 0) {
        // User has already performed the action, retrieve the existing action
        $existingAction = $checkResult->fetch_assoc()["action"];

        if ($existingAction === $action) {
            // User is trying to perform the same action again, remove the existing action
            $sql = "DELETE FROM article_like WHERE user_id = $userId AND article_id = $articleId";

            if ($conn->query($sql) === TRUE) {
                // Decrement the count as the user is canceling the action
                if ($action === "like") {
                    $sqlUpdate = "UPDATE article SET article_like = article_like - 1 WHERE id = $articleId";
                } elseif ($action === "dislike") {
                    $sqlUpdate = "UPDATE article SET article_dislike = article_dislike - 1 WHERE id = $articleId";
                }

                if ($conn->query($sqlUpdate) === TRUE) {
                    // Return the updated counts
                    $selectCountQuery = "SELECT article_like, article_dislike FROM article WHERE id = $articleId";
                    $result = $conn->query($selectCountQuery);

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();

                        // Calculate the total count
                        $totalCount = $row['article_like'] - $row['article_dislike'];

                        // Return the total count as JSON
                        echo json_encode(['total' => $totalCount]);
                    } else {
                        // No need to echo anything in this case
                        // This might indicate a problem with the database query or data
                        // You might want to log an error or handle it appropriately
                    }
                } else {
                    echo "Error updating record: " . $conn->error;
                }
            } else {
                echo "Error deleting existing record: " . $conn->error;
            }
        } else {
            // User is switching from like to dislike or vice versa
            $sqlUpdate = "UPDATE article_like SET action = '$action' WHERE user_id = $userId AND article_id = $articleId";

            if ($conn->query($sqlUpdate) === TRUE) {
                // Update the count based on the new action
                if ($action === "like") {
                    $sqlCount = "UPDATE article SET article_like = article_like + 1, article_dislike = GREATEST(article_dislike - 1, 0) WHERE id = $articleId";
                } elseif ($action === "dislike") {
                    $sqlCount = "UPDATE article SET article_dislike = article_dislike + 1, article_like = GREATEST(article_like - 1, 0) WHERE id = $articleId";
                }

                if ($conn->query($sqlCount) === TRUE) {
                    // Return the updated counts
                    $selectCountQuery = "SELECT article_like, article_dislike FROM article WHERE id = $articleId";
                    $result = $conn->query($selectCountQuery);

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();

                        // Calculate the total count
                        $totalCount = $row['article_like'] - $row['article_dislike'];

                        // Return the total count as JSON
                        echo json_encode(['total' => $totalCount]);
                    } else {
                        // No need to echo anything in this case
                        // This might indicate a problem with the database query or data
                        // You might want to log an error or handle it appropriately
                    }
                } else {
                    echo "Error updating record: " . $conn->error;
                }
            } else {
                echo "Error updating record: " . $conn->error;
            }
        }
    } else {
        // User hasn't performed the action, proceed with updating the count
        if ($action === "like") {
            $sql = "UPDATE article SET article_like = article_like + 1 WHERE id = $articleId";
        } elseif ($action === "dislike") {
            $sql = "UPDATE article SET article_dislike = article_dislike + 1 WHERE id = $articleId";
        }

        // Insert into article_like table to track user action
        $insertQuery = "INSERT INTO article_like (article_id, user_id, action) VALUES ('$articleId', '$userId', '$action')";
        $conn->query($insertQuery);

        if ($conn->query($sql) === TRUE) {
            // Return the updated counts
            $selectCountQuery = "SELECT article_like, article_dislike FROM article WHERE id = $articleId";
            $result = $conn->query($selectCountQuery);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                // Calculate the total count
                $totalCount = $row['article_like'] - $row['article_dislike'];

                // Return the total count as JSON
                echo json_encode(['total' => $totalCount]);
            } else {
                // No need to echo anything in this case
                // This might indicate a problem with the database query or data
                // You might want to log an error or handle it appropriately
            }
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }
}





?>