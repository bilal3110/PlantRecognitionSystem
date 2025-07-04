<?php
include "layout/header.php";
include "backend/config.php";

?>
      <setion class="blog">
        <div class="blog-head">
          <h3>ALL Blogs</h3>
          <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, voluptatibus.</p>
        </div>
        <div class="blog-list">
          <?php
            $sql = "SELECT * FROM blog ORDER BY created_at ";
            $result = mysqli_query($conn, $sql) or die("Query Failed.");
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<div class='blog-card'>
                            <span class='blog-img'>
                                <img src='upload/{$row['image']}' alt=''>
                            </span>
                            <h3>{$row['title']}</h3>
                            <p>" . substr($row['description'], 0, 100) . "...</p>
                            <span id='blog-link'>
                                <a href='blog-details.php?id={$row['id']}'>Read More</a>
                                <i class='fa-solid fa-arrow-right'></i>
                            </span>
                          </div>";
                }
            } else {
                echo "<p>No blogs found.</p>";
            }
          ?>
        </div>
      </setion>
<?php
include "layout/footer.php";
?>