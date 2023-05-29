<?php
  include "config.php";
  if(isset($_FILES['fileToUpload']))
  {
    $errors = array();

    $file_name = $_FILES['fileToUpload']['name'];
    $file_size = $_FILES['fileToUpload']['size'];
    # Temporary name
    $file_tmp = $_FILES['fileToUpload']['tmp_name'];
    $file_type = $_FILES['fileToUpload']['type'];
    # explode() breaks the string after given delimeter eg : file1.txt explodes to txt
    # end function just takes the exploded string and store in the variable.
    $file_ext = end(explode('.', $file_name));

    $extensions = array("jpeg","jpg","png");
    # in_array() is used to search any element stored in the array.
    if(in_array($file_ext, $extensions) === false)
    {
      $errors[] = "This extension file not allowed, Please choose a JPG or PNG file.";
    }

    # 1kb = 1024 bytes and 1mb = 1024 bytes so 2mb = 2 x 1024 x 1024 bytes
    if($file_size > 2097152) # file size should not exeed 2MB
    {
      $errors[] = "File size must be 2mb or lower.";
    }


    /* we are adding server time also bcoz if we want to add same named image again then old image will be 
       destroyed and new image will be applied to both current and previous posts. */
    $new_name = time(). "-".basename($file_name);
    /* basename(path, suffix)
      PHP basename() Function : Return filename from the specified path.
      $path = "/testweb/home.php";
      echo basename($path); --> home.php
      below statement shows filename, but cut off file extension for ".php" files
      echo basename($path,".php"); --> home
    */
    $target = "upload/".$new_name;

    if(empty($errors) == true)
    {
      move_uploaded_file($file_tmp, $target);
    }
    else
    {
      print_r($errors);
      die();
    }
  }

  session_start();
  $title = mysqli_real_escape_string($conn, $_POST['post_title']);
  $description = mysqli_real_escape_string($conn, $_POST['postdesc']);
  $category = mysqli_real_escape_string($conn, $_POST['category']);
  $date = date("d M, Y");
  $author = $_SESSION['user_id'];

  $sql = "INSERT INTO post(title, description,category,post_date,author,post_img)
          VALUES('{$title}','{$description}',{$category},'{$date}',{$author},'{$new_name}');";
  $sql .= "UPDATE category SET post = post + 1 WHERE category_id = {$category}";

  // same as mysqli_query() but returns true only if both queries runs successfully.
  if(mysqli_multi_query($conn, $sql))
  {
    header("location: {$hostname}/admin/post.php");
  }
  else
  {
    echo "<div class='alert alert-danger'>Query Failed.</div>";
  }
?>

<!-- Uploaded images will be store here : C:\xampp\htdocs\news-site\admin\upload -->