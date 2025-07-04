<?php
include "layout/header.php";
include "backend/config.php";

?>
      <setion class="blog">
        <div class="blog-head">
          <h3>Blog Details</h3>
          <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, voluptatibus.</p>
        </div>
        <div class="blog-list">
          <?php
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $sql = "SELECT * FROM blog WHERE id = {$id}";
                $result = mysqli_query($conn, $sql) or die("Query Failed.");
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<div class='blog-card' style='width: 1000px; display: flex;  flex-direction: column;'>
                                <span class='blog-img'>
                                    <img src='upload/{$row['image']}' alt=''>
                                </span>
                                <h3>{$row['title']}</h3>
                                <p>{$row['description']}</p>
                              </div>";
                    }
                } else {
                    echo "<p>No blog found.</p>";
                }
            } else {
                echo "<p>Invalid request.</p>";
            }
          ?>
        </div>
      </setion>
<?php
include "layout/footer.php";
?>