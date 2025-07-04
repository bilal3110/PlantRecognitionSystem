<?php
include "layout/header.php";
include "backend/config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $uploadDir = __DIR__ . '/upload/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true); 
    }
    $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
    $targetPath = $uploadDir . $fileName;

    if (getimagesize($_FILES['image']['tmp_name']) !== false) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $pythonExec = 'C:/xampp/htdocs/PlantReco/venv/Scripts/python.exe';
            $pythonScript = 'C:/xampp/htdocs/PlantReco/python/predict.py';
            $command = "\"" . str_replace('/', '\\', $pythonExec) . "\" \"" . str_replace('/', '\\', $pythonScript) . "\" \"" . str_replace('/', '\\', $targetPath) . "\" 2>&1";
            $output = shell_exec($command);

            file_put_contents(__DIR__ . '/debug.log', "Command: $command\nOutput: " . ($output ?? 'null') . "\n", FILE_APPEND);

            if ($output !== null) {
                preg_match('/\{.*\}/', $output, $json_matches);
                $json_output = $json_matches[0] ?? $output;
                $result = json_decode($json_output, true);

                if ($result && !isset($result['error'])) {
                    $conn = new mysqli('localhost:3306', 'root', '', 'plantreco');
                    if ($conn->connect_error) {
                        $error = "Database connection failed: " . $conn->connect_error;
                    } else {
                        $stmt = $conn->prepare("INSERT INTO Images (image_path, status) VALUES (?, 'pending')");
                        $stmt->bind_param('s', $targetPath);
                        $stmt->execute();
                        $imageId = $conn->insert_id;

                        $common_name = $result['common_name'];
                        $stmt = $conn->prepare("SELECT plant_id FROM Plants WHERE common_name = ?");
                        $stmt->bind_param('s', $common_name);
                        $stmt->execute();
                        $plant_result = $stmt->get_result();
                        $plant_id = $plant_result->num_rows > 0 ? $plant_result->fetch_assoc()['plant_id'] : null;
                        $stmt->close();

                        if ($plant_id === null) {
                            $error = "Plant not found in database: " . htmlspecialchars($scientific_name);
                        } else {
                            $stmt = $conn->prepare("INSERT INTO Recognition_Results (image_id, plant_id, confidence) VALUES (?, ?, ?)");
                            $stmt->bind_param('iid', $imageId, $plant_id, $result['confidence']);
                            $stmt->execute();
                            $resultId = $conn->insert_id;
                            $stmt->close();
                            $conn->close();

                            header("Location: result.php?result_id=" . $resultId);
                            exit();
                        }
                    }
                } else {
                    $error = "Prediction failed: " . ($result['error'] ?? 'Invalid JSON: ' . htmlspecialchars($json_output));
                }
            } else {
                $error = "Python script execution failed. Check debug.log for details.";
            }
        } else {
            $error = "Failed to upload the file.";
        }
    } else {
        $error = "Please upload a valid image file.";
    }
}
?>

<main>
    <div class="about">
        <div class="abt-txt">
            <h3>Identify and Explore Plant Reco</h3>
            <p>Our Plant Recognition System is a web-based application designed to identify different types of leaves using machine learning. Built with a PHP backend and integrated with a Python-based image classification model, the system accurately detects and classifies 5–7 types of plants. It's ideal for educational and research use, especially in university-level botany and environmental science projects. With a simple user interface and fast recognition results, this tool makes plant identification more accessible and efficient.</p>
        </div>
        <div class="abt-img">
            <img src="images/abt.jpg" alt="About Plant Reco">
        </div>
    </div>
    <div class="reco">
        <h3>Try Plant Reco</h3>
        <?php if (isset($error)) { ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php } ?>
        <form style="width: 100%" id="uploadForm" method="post" enctype="multipart/form-data" action="">
            <div class="box">
                <button type="button" class="upload-button" id="uploadButton">
                    <div class="button-icon">
                        <i class="fas fa-image"></i>
                    </div>
                    <span>add / drop an image</span>
                </button>
                <input type="file" id="fileInput" name="image" accept="image/*" style="display: none;">
                <p id="errorMessage" style="color: red; display: none;"></p>
            </div>
        </form>
    </div>
</main>
<section class="blog">
    <div class="blog-head">
        <h3>Latest Blogs</h3>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, voluptatibus.</p>
    </div>
    <div class="blog-list">
        <?php
        $sql = "SELECT * FROM blog ORDER BY created_at DESC LIMIT 3";
        $result = mysqli_query($conn, $sql) or die("Query Failed.");
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='blog-card'>
                        <span class='blog-img'>
<img 
    src='upload/{$row['image']}' 
    alt='Blog Image'
    style='transition: transform 0.3s ease;'
    onmouseover='this.style.transform='scale(1.05)''
    onmouseout='this.style.transform='scale(1)''>
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
    <div class="blog-btn">
        <a href="blogs.php">See More</a>
    </div>
</section>
<?php include "layout/footer.php"; ?>