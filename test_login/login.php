<?php require_once('auth.php') ?>
<?php require_once('../vendor/autoload.php') ?>
<?php
$clientID = "209900732974-1kdbldch420ucdurdr6vsdg7d2cvd28i.apps.googleusercontent.com";
$secret = "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";

// Google API Client
$gclient = new Google_Client();

$gclient->setClientId($clientID);
$gclient->setClientSecret($secret);
$gclient->setRedirectUri('http://localhost/phpdasar/infoinfopedia/test_login/login.php');


$gclient->addScope('email');
$gclient->addScope('profile');

if(isset($_GET['code'])){
    // Get Token
    $token = $gclient->fetchAccessTokenWithAuthCode($_GET['code']);

    // Check if fetching token did not return any errors
    if(!isset($token['error'])){
        // Setting Access token
        $gclient->setAccessToken($token['access_token']);

        // store access token
        $_SESSION['access_token'] = $token['access_token'];

        // Get Account Profile using Google Service
        $gservice = new Google_Service_Oauth2($gclient);

        // Get User Data
        $udata = $gservice->userinfo->get();
        foreach($udata as $k => $v){
            $_SESSION['login_'.$k] = $v;
        }
        $_SESSION['ucode'] = $_GET['code'];

        header('location: feed.php');
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
    <style type="text/css">
        .isi {
            width: 45vw;
            height: 50vh;
            text-align: center;
            display: grid;
            place-content: center;
            margin: auto;
            border-radius: 8%;
            margin-top: 40px;
            font-family: fantasy;
            background-color: white;
        }
        .tombol:hover {
            background-color: light;
        }
        body {
            background-image: url('src/cover.png'); /* Ganti path/to/gambar-anda.jpg dengan path ke gambar Anda */
            background-size: cover; /* Agar gambar mencakup seluruh halaman */
            background-repeat: no-repeat; /* Menghindari pengulangan gambar */
            background-attachment: fixed; /* Membuat gambar tetap di tempat ketika halaman di-scroll */
            /* Opsional: tambahkan properti lain sesuai kebutuhan Anda */
        }
        .isi {
            min-width: 230px;
        }
        /*.isi {
            opacity: 0.9;
        }
        .gambar {
            opacity: 1;
        }*/

    </style>

</head>
<body>
    
    <div class="isi ">
        <section class="gambar">
            <img src="src\logo.png" width="200px" height="55px">
        </section>
        <section class="login">
            <div class="container my-5">
        <div class="row">
            <!-- <div class="col-auto mx-auto">
                <a href="<?= $gclient->createAuthUrl() ?>" class="btn btn btn-primary btn-flat rounded-0">Login with Google</a>
            </div> -->
        </div>
    </div>
    <p>
        <span> 
        </span>
    </p>
    <div class="tombol">
          <a class="nav-link" href="<?= $gclient->createAuthUrl() ?>"><img src="https://www.drupal.org/files/issues/2020-01-19/google_logo.png" width="165px" height="50px"></a>
    </div>
        </section>
    </div>
    
</body>
</html>