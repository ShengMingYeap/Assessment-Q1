<?php include("db_connect.php"); ?>

<?php 
    session_start();

    // check member type
    $member_type = "nonmember";
    if(isset($_SESSION["username"])){
        $user_query = "SELECT * FROM users WHERE username = '$_SESSION[username]' AND member_type = 'Member'";
        if($user_result = mysqli_query($dblink, $user_query)){
            if(mysqli_num_rows($user_result) > 0){
                $member_type = "member";
            }
        }
    }
?>

<html>
    <head>
        <title>Photo Sharing</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>

    <style>
        img{
            max-width: 100%;
        }

        .photo {
            height: 200px;
            object-fit: contain;
        }

        form label{
            font-weight: bold;
        }
    </style>

    <body>
        <nav class="navbar navbar-light bg-light">
            <div class="container-fluid justify-content-center">
                <?php if(isset($_SESSION["username"])){ ?>
                    Hi, <?php echo $_SESSION["username"]; ?> | &nbsp;<a class="navbar-brand" href="action.php?logout">Logout</a>
                <?php }else{ ?>
                    <a class="navbar-brand" href="login.php">Login</a>
                <?php } ?>
            </div>
        </nav>

        <div class="container mt-5" style="max-width:800px;">
            <?php 
                if(isset($_SESSION["upload_file"])){
                    if(!empty($_SESSION["upload_file"]["errors"])){
                        foreach($_SESSION["upload_file"]["errors"] as $error){
                            echo "<div class='alert alert-danger alert-dismissible fade show'>Image $error[name] - $error[message] <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
                        }
                    }

                    $total_upload = $_SESSION["upload_file"]["total_upload"];
                    if($_SESSION["upload_file"]["total_upload"] > 0){
                        echo "<div class='alert alert-success alert-dismissible fade show'>Total $total_upload image(s) successfully upload. <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
                    }
                }
            ?>
            <form action="action.php" method="POST" enctype="multipart/form-data">
                <label class="form-label" for="uploadFile">Upload Images</label> 
                <small style="color:red;font-weight:bold;">&nbsp;(*Only allow jpg, jpeg, png and gif file format.)</small>
                <input type="file" name="files[]" class="form-control" id="uploadFile" multiple required />
                <br>
                <button type="submit" name="submit" value="upload" class="btn btn-primary float-end">Upload</button>
            </form>
        </div>

        <br><br>

        <div class="container">
            <hr>
            <br><br>

            <?php 
                $query = "SELECT * FROM photo WHERE status = '1' ORDER BY entry_datetime DESC";
                if($result = mysqli_query($dblink, $query)){
                    if(mysqli_num_rows($result) > 0){
                        echo "<div class='row'>";
                        while($row = mysqli_fetch_array($result)){
                ?>
                            <div class="col-6 col-md-4 col-lg-3 mb-4">
                                <img src="<?php echo $row["image"]; ?>" class="photo">
                                <a href="action.php?download=<?php echo $row['image']; ?>" class="btn btn-success w-100 mt-1 download-btn">Download</a>                         
                            </div>
            <?php
                        }
                        echo "</div>"; // end row
                    }else{
                        echo "<div class='alert alert-danger text-center'>No photo records found.</div>";
                    }
                }else{
                    echo "Oops! Something went wrong. Please try again later.";
                }
            ?>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
        
        <script>
            $(document).ready(function() {
                $(".download-btn").click(function(){
                    $.ajax({
                        url:"action.php",
                        type: "POST",
                        data: {
                            action: "checkDownload",
                            member_type: "<?php echo $member_type; ?>"
                        },
                        success:function(result){
                            alert(result);
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                });
            })
        </script>
    </body>
</html>