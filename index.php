<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/d9c5ee75e5.js" crossorigin="anonymous"></script>
    <title>List of folders/files</title>
    <style>

    </style>
</head>

<body>
    <?php
    session_start();
    // LOGOUT Conditional Statement
    if (isset($_GET['action']) and $_GET['action'] == 'logout') {
        session_destroy();
        session_start();
    }

    // lOGIN Conditional Statement
    $msg = '';
    if (isset($_POST['login']) && !empty($_POST['username']) && !empty($_POST['password'])) {
        if ($_POST['username'] == 'John' && $_POST['password'] == '1234') {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $_POST['username'];
        } else {
            $msg = 'Wrong username or password';
        }
    }
    ?>
    <!-- LOGIN FORM -->
    <!-- --------------------------- -->
    <div class="text-center mt-5 w-50  m-auto">
        <h1 class="mb-4" <?php isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true
                                ? print("style = 'display: none'")
                                : print("style = 'display: block'") ?>>
            Enter Username and Password</h1>
        <div>
            <form action="" method="POST" <?php isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true
                                                ? print("style = \"display: none\"")
                                                : print("style = \"display: block\"") ?>>
                <h4><?php echo $msg; ?></h4>
                <input class="mb-4" type="text" name="username" placeholder="username = John" required autofocus></br>
                <input class="mb-4" type="password" name="password" placeholder="password = 1234" required></br>
                <button class="btn btn-lg" style=" color: white; background: #2884bd;" type="submit" name="login" formaction="./">Login</button>
            </form>
        </div>
    </div>

    <?php
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
        $path = isset($_GET["path"]) ? './' . $_GET["path"] : './';
        $docs = scandir($path);

        // DOWNLOAD FILE Conditional Statement
        //------------------------------------------
        if (isset($_POST['download'])) {
            $file = './' . $_GET['path'] . "/" . $_POST['download'];
            $fileToDownloadEscaped = str_replace("&nbsp;", " ", htmlentities($file, 0, 'utf-8'));

            ob_clean();
            ob_start();
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename=' . basename($fileToDownloadEscaped));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fileToDownloadEscaped));
            ob_end_flush();
            readfile($fileToDownloadEscaped);
            exit;
        }

        // DELETE FILE Conditional Statement
        //------------------------------------------   
        $deleteSuccess = "";
        $deleteError = '';
        if (isset($_POST['delete']) && $_POST['delete'] !== 'index.php' && $_POST['delete'] !== 'README.md') {
            $file = './' . isset($_GET['path']) . $_POST['delete'];
            if (is_file($file)) {
                if (file_exists($file)) {
                    unlink($file);
                    header("refresh: 1");
                    $deleteSuccess = 'File Deleted Successfuly!';
                }
            }
        }
        if (isset($_POST['delete']) && ($_POST['delete'] === 'index.php' || $_POST['delete'] === 'README.md')) {
            $deleteError = 'This file can not be deleted!';
        }

        // GO BACK Conditional Statement
        //------------------------------------------   
        if (isset($_GET["path"])) {
            $backButton = "?path=" . ltrim(dirname($_GET["path"]), "./") . "/";
        } else $backButton = "";

        //  DISPLAY HEADER
        //------------------------------------------    
        print("<div style='margin-top: -20px; margin-left:10px;'>");
        print('<span class="" style="font-size: 40px; margin-left:10px;">
            <i class="fa-solid fa-folder-open" style="font-size: 40px; "></i> 
            FILE SYSTEM BROWSER</span>');
        print('<h6 class="mx-2 mt-4" >Directory: ' . str_replace('?path=/', "", $_SERVER["REQUEST_URI"]) . '</h6>');
        print('<div>');
        print('<button class="mb-4 mx-2 btn " style="float: left; background: #44a665">
            <a style="text-decoration: none; color: white;" href= "' . $backButton . '">
            <i class="fa-solid fa-circle-arrow-left"></i> 
            Back</a>
            </button>');
        print('<button class="mb-4 mx-2 btn " style="float: right; background: #44a665;">
            <a href="index.php?action=logout" style="text-decoration: none; color: white;">
            <i class="fa-solid fa-right-from-bracket"></i> 
            Logout</a>
            </button>');
        print('<p style=" color: #eb5b34;">' . $deleteError . '</p>');
        print('<p style=" color:#eb5b34;">' . $deleteSuccess . '</p>');
        print("</div>");
        print("</div>");

        //  DISPLAY TABLE
        //------------------------------------------        
        print('
            <table class="table table-striped table-active" style="width:80%; margin:auto; border-radius: 10%; border: 1px solid black;">
            <th style="width: 40%; text-align: center; border: 1px solid black;">Name</th>
            <th style="width: 20%; text-align: center; border: 1px solid black;">Type</th>
            <th style="width: 20%; text-align: center; border: 1px solid black;">Delete</th> 
            <th style="width: 20%; text-align: center; border: 1px solid black;">Download</th>
        ');

        foreach ($docs as $value) {
            if ($value != ".." and $value != ".") {
                print('<tr>');
                print('<td style="border: 1px solid black;">' . (is_dir($path . $value)
                    ? '<a style=" color: #2884bd; " href="' . (isset($_GET['path'])
                        ? $_SERVER["REQUEST_URI"] . $value . '/'
                        : $_SERVER['REQUEST_URI'] . '?path=' . $value . '/') . '">' . $value . '</a>'
                    : $value)
                    . '</td>');
                print('<td style="border: 1px solid black;">' . (is_dir($path . $value) ? "Folder" : "File") . '</td>');
                
                // DELETE and DOWNLOAD BUTTONS do not appear for folders
                //-----------------------------------------      
                if (is_dir($path . $value)) {
                    print('<td style="border: 1px solid black;"></td>');
                    print('<td style="border: 1px solid black;"></td>');
                } else if (is_file($path . $value)) {

                    // DELETE BUTTON
                    //-----------------------------------------      
                    print('<td style="border: 1px solid black;">' .
                        '<form style= "display: flex; justify-content: center" action="" method="post">
                            <button class="delete btn btn-xs" type ="submit" name="delete" value =' . $value . ' style="color: white; background: #eb5b34;">
                            <i class="fa-regular fa-trash-can"></i> 
                            Delete</button>
                            </form>
                    </td>');

                    // DOWNLOAD FILE BUTTON
                    //------------------------------------------   
                    print('<td style="border: 1px solid black;">');
                    print('<form style= "display: flex; justify-content: center" action="" method="POST">');
                    print('<button type="submit" name="download" value="' . $value . '" class="btn" style=" color: white; background: #2884bd;">
                        <i class="fa-solid fa-download"></i> 
                        Download</button>');
                    print('</form>');
                    print('</td>');
                    print("</tr>");
                }
            }
        }
        print('</table>');
    }
    ?>
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
    <script src="https://kit.fontawesome.com/d9c5ee75e5.js" crossorigin="anonymous"></script>
</body>

</html>