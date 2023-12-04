<?php include("db_connect.php"); ?>

<?php 
session_start();

if(isset($_GET["logout"])){
    unset($_SESSION["username"]);
    unset($_SESSION["total_download"]);
    unset($_SESSION["upload_file"]);
    header("Location: index.php");
}

if(isset($_POST["login"])){
    unset($_SESSION["total_download"]);
    unset($_SESSION["upload_file"]);

    $member_password = hash('sha256', $_POST["password"]);
    $query = "SELECT * FROM users WHERE email = '$_POST[email]' AND password = '$member_password'";
    if($result = mysqli_query($dblink, $query)){
        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_array($result);
            $_SESSION["username"] = $row["username"];
            header("Location: index.php"); exit();
        }else{
            header("Location: login.php?fail=1"); exit();
        }
    }
}

if(isset($_POST["submit"]) == "upload"){
    // Check session upload_file exist, clear this session
    if(isset($_SESSION["upload_file"])){
        unset($_SESSION["upload_file"]);
    }

    $errors = array();
    $imgPath = "photo/";
    $allowTypes = array('jpg','jpeg','png','gif');
    $maxSize = 2 * 1024 * 1024; // Define maxsize for files i.e 2MB
    $totalFileUploaded = 0;

    if(!empty($_FILES["files"]["name"])){
        $countFiles = count($_FILES["files"]["name"]);

        for($i=0;$i<$countFiles;$i++){
            $fileName = $_FILES["files"]["name"][$i];
            $filePath = $imgPath.$fileName;
            $fileSize = $_FILES["files"]["size"][$i];          
            $fileType = pathinfo($filePath, PATHINFO_EXTENSION);
            $fileNameWithoutExtension = pathinfo($filePath, PATHINFO_FILENAME);
            if(in_array(strtolower($fileType), $allowTypes)){ 
                // Verify file size
                if($fileSize > $maxSize){ 
                    $tempError["name"] = $fileName;
                    $tempError["message"] = "Image size is larger than the allowed limit.";
                    $errors[] = $tempError;
                }else{
                    // Upload File
                    if(file_exists($filePath)) {
                        $filePath = $imgPath.$fileNameWithoutExtension."-".time().".".$fileType;
                        if(move_uploaded_file($_FILES['files']['tmp_name'][$i], $filePath)){
                            // Insert to database
                            $username = "guest";
                            if(isset($_SESSION["username"])){
                                $username = $_SESSION["username"];
                            }
                            $img = $filePath;
                            $status = 1;
                            $query = "INSERT INTO photo (username,image,status,entry_datetime) VALUE ('$username', '$img', '$status', NOW())";
                            mysqli_query($dblink, $query) or die(mysqli_error($dblink));

                            $totalFileUploaded++;
                        }else{
                            $tempError["name"] = $fileName;
                            $tempError["message"] = "Failed to upload image.";
                            $errors[] = $tempError;
                        }
                    }else{
                        if(move_uploaded_file($_FILES['files']['tmp_name'][$i], $filePath)){
                            // Insert to database
                            $username = "guest";
                            if(isset($_SESSION["username"])){
                                $username = $_SESSION["username"];
                            }
                            $img = $filePath;
                            $status = 1;
                            $query = "INSERT INTO photo (username,image,status,entry_datetime) VALUE ('$username', '$img', '$status', NOW())";
                            mysqli_query($dblink, $query) or die(mysqli_error($dblink));
                        }else{
                            $tempError["name"] = $fileName;
                            $tempError["message"] = "Failed to upload image.";
                            $errors[] = $tempError;
                        }
                    }
                }
            }else{
                // File extension not valid
                $tempError["name"] = $fileName;
                $tempError["message"] = "File type is not allowed.";
                $errors[] = $tempError;
            }
        }
    }

    $_SESSION['upload_file'] = array("total_upload" => $totalFileUploaded,"errors" => $errors);

    header("Location: ".$_SERVER["HTTP_REFERER"]);
}

//unset($_SESSION["total_download"]);

if(empty($_SESSION["total_download"])){
    $_SESSION["total_download"] = 0;
}

if(!empty($_GET["download"])){

    $url = $_SERVER["HTTP_REFERER"].$_GET["download"];
    if(str_contains($url, ".php")){
        $url = "http://localhost/photo-sharing/".$_GET["download"];
    }
    $file = basename($url);
    ob_end_clean();
    header("Content-Description:File Transfer");
    header("Content-Type:application/image");
    header("Content-Disposition:attachment;filename=$file");
    readfile($url);
    
}

if(isset($_POST["action"]) == "checkDownload"){
    $_SESSION["total_download"] += 1;

    if($_POST["member_type"] == "member"){
        if($_SESSION["total_download"] == 1){
            echo "Your download is starting...";
            sleep(3);
        }else if($_SESSION["total_download"] == 2){
            echo "Your download is starting...";
            sleep(2);
        }else if($_SESSION["total_download"] >= 3){
            echo "Too many downloads";
            sleep(5);
        }
    }else if($_POST["member_type"] == "nonmember"){
        if($_SESSION["total_download"] == 1){
            echo "Your download is starting...";
            sleep(3);
        }else{
            echo "Too many downloads";
            sleep(5);
        }
    }
}

?>