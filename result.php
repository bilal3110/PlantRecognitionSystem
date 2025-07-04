<?php
include "layout/header.php";
include "backend/config.php";

if (isset($_GET['result_id'])) {
    $conn = new mysqli('localhost:3306', 'root', '', 'plantreco');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $result_id = intval($_GET['result_id']);
    $stmt = $conn->prepare("
        SELECT r.image_id, r.plant_id, r.confidence, r.result_timestamp, 
               p.scientific_name, p.common_name, p.description, p.care_instructions, 
               i.image_path AS path
        FROM Recognition_Results r
        JOIN Plants p ON r.plant_id = p.plant_id
        JOIN Images i ON r.image_id = i.image_id
        WHERE r.result_id = ?
    ");
    $stmt->bind_param('i', $result_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    if ($data) {
        $scientific_name = htmlspecialchars($data['scientific_name']);
        $common_name = htmlspecialchars($data['common_name'] ?? 'Unknown');
        $description = htmlspecialchars($data['description'] ?? 'No data available.');
        $care_instructions = htmlspecialchars($data['care_instructions'] ?? 'No data available.');
        $confidence = number_format($data['confidence'] * 100, 2);
        $image_path = htmlspecialchars(str_replace('C:/xampp/htdocs/PlantReco/', '', $data['path']));

        $common_name_folder = strtolower(str_replace([' ', ','], '_', $data['common_name'] ?? $data['scientific_name']));
        $dataset_dir = "dataset/$common_name_folder/";
        $images = [];
        if (is_dir($dataset_dir)) {
            $files = glob($dataset_dir . "*.{jpg,jpeg,png}", GLOB_BRACE);
            $images = array_slice($files, 0, 4);
        }
        $fallback_image = "images/abt.jpg";
    } else {
        $scientific_name = "Unknown";
        $common_name = "Unknown";
        $description = "No data available.";
        $care_instructions = "No data available.";
        $confidence = "0.00";
        $image_path = "images/abt.jpg";
        $images = [];
        $fallback_image = "images/abt.jpg";
    }
} else {
    $scientific_name = "Unknown";
    $common_name = "Unknown";
    $description = "No data available.";
    $care_instructions = "No data available.";
    $confidence = "0.00";
    $image_path = "images/abt.jpg";
    $images = [];
    $fallback_image = "images/abt.jpg";
}
?>

<section class="result">
    <div class="container">
        <div class="header">
            <div>
                <h2>Recognition Result</h2> <br>
                <h1><i><?php echo $scientific_name; ?></i> <span class="iucn">Confidence: <?php echo $confidence; ?>%</span></h1> <br>
                <p class="plant-name"><?php echo $common_name; ?></p> <br>
                <p class="family">Description: <?php echo $description; ?></p> <br>
                <p class="care">Care Instructions: <?php echo $care_instructions; ?></p><br>
            </div>
        </div>
        <div class="image-grid">
            <?php
            for ($i = 0; $i < 4; $i++) {
                $image_path = $images[$i] ?? $fallback_image;
                $image_label = $scientific_name;
                $image_type = ($i % 2 == 0) ? "Leaf" : "";
                ?>
                <div class="image-item">
                    <img src="<?php echo $image_path; ?>" alt="<?php echo $image_label; ?>" class="plant-image">
                    <p class="label"><?php echo $image_label; ?></p>
                    <?php if ($image_type) { ?>
                        <p><?php echo $image_type; ?></p>
                    <?php } ?>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="buttons">
            <a href="new.php" class="btn confirm">Back to Home</a>
        </div>
    </div>
</section>

<?php include "layout/footer.php"; ?>