<?php

session_start();

if(!isset($_SESSION['ucode']) || (isset($_SESSION['ucode']) && empty($_SESSION['ucode']))){
    if(strstr($_SERVER['PHP_SELF'], 'login.php') === false)
    header('location:login.php');
}

require 'functions.php';

if(isset($_POST["tambah"])){

    if(tambah($_POST) > 0){
        echo "
            <script>
                alert('data berhasil ditambahkan');
                document.location.href = 'feed.php';
            </script>



        ";
    } else{
        echo "
            <script>
                alert('data gagal ditambahkan!');
                document.location.href = 'feed.php';
            </script>


        ";
    }


}



?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
	<title></title>
</head>
<body>


<div class="container my-5">
    <div class="row">
        <div class="col-lg-5 col-md-7 col-sm-12 mx-auto">
            <div class="card rounded-0">
                <div class="card-body">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="mb-3">Add Article</div>
                        <div class="mb-3">
                            <label for="title" class="form-label">Article Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Article Content</label>
                            <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="gambar">Image: </label>
                            <input type="file" name="gambar" id="gambar" required>
                        </div>
                        <button type="submit" class="btn btn-primary rounded-0" name="tambah">Upload Article</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


</body>
</html>