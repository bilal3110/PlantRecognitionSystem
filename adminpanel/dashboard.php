<?php
include "layout/header.php";
include "../backend/config.php";
?>

<!-- Header Section -->
<section class="header bg-body-tertiary" style="min-height: 85vh;">
    <?php
    include "layout/side.php";
    ?>
    <div class="main">
        <div class="table-contents d-flex justify-content-between">
            <h4 style="color:#8eb533; font-size: 18px; font-weight: 700;">ALL POSTS</h4>
            <a id="logout" href="add-post.php">Add Post</a>
        </div>
        <table class="table">
            <thead>
                <tr id="t-heading">
                    <th scope="col">Sr No</th>
                    <th scope="col">Title</th>
                    <th scope="col">Date</th>
                    <th scope="col">Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM blog";
                $result = mysqli_query($conn, $sql) or die("Query Failed.");
                if (mysqli_num_rows($result) > 0) {
                    $i = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr id='t-data'>
                                <td>{$i}</td>
                                <td>{$row['title']}</td>
                                <td>" . date('d M, Y', strtotime($row['created_at'])) . "</td>
                                <td class='delete'><a href='../backend/delete-blog.php?id={$row['id']}'><i class='fa-solid fa-trash'></i></a></td>
                              </tr>";
                        $i++;
                    }
                    
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No Posts Found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<?php
include "layout/footer.php";
?>