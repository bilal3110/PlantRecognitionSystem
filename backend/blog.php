<?php
include "config.php";
if (isset($_FILES['fileToUpload'])) {
    $errors = array();

    $file_name = $_FILES['fileToUpload']['name'];
    $file_size = $_FILES['fileToUpload']['size'];
    $file_tmp = $_FILES['fileToUpload']['tmp_name'];
    $file_type = $_FILES['fileToUpload']['type'];
    $file_ext = explode('.', $file_name);
    $file_ext = end($file_ext);

    $extensions = array("jpeg", "jpg", "png");

    if (in_array($file_ext, $extensions) === false) {
        $errors[] = "This extension file not allowed, Please choose a JPG or PNG file.";
    }
    if ($file_size > 2097152) {
        $errors[] = "File size must be 2mb or lower.";
    }
    $new_name = time() . "-" . basename($file_name);
    $target = "../upload/" . $new_name;

    if (empty($errors) == true) {
        move_uploaded_file($file_tmp, $target);
    } else {
        print_r($errors);
        die();
    }
}

$title = mysqli_real_escape_string($conn, $_POST['title']);
$description = mysqli_real_escape_string($conn, $_POST['description']);
$date = date("Y-m-d H:i:s");

$insert =  "INSERT INTO blog (`title`, `description`, `image`)
VALUES ('$title', '$description', '$new_name');
";
$result = mysqli_query($conn, $insert) or die(mysqli_error($conn));
if ($result) {
    header("Location: ../adminpanel/dashboard.php");
} else {
    echo "<div class='alert alert-danger'>Query Failed.</div>";
}

?>