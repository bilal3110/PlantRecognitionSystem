<?php
    include "config.php";

    $delete = $_GET['id'];
    $sql = "DELETE FROM blog WHERE id = {$delete}";
    $result = mysqli_query($conn, $sql) or die("Query Failed.");
    if ($result) {
        header("Location: ../adminpanel/dashboard.php");
    } else {
        echo "<div class='alert alert-danger'>Query Failed.</div>";
    }
    mysqli_close($conn);
?>