<?php
require_once("config.php");
require_once("auth.php");
require_once("RealEstateDatabase.php");

requireRole(["agent"]);

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $db = new RealEstateDatabase();

    $title = trim($_POST["title"] ?? "");
    $propertyType = trim($_POST["propertyType"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $city = trim($_POST["city"] ?? "");
    $price = (float)($_POST["price"] ?? 0);
    $status = $_POST["status"] ?? "available";
    $agentId = (int)$_SESSION["user"]["userId"];

    $image_url = "";
    $image_url2 = "";

    $targetDir = "uploads/";

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (!empty($_FILES["image"]["name"])) {
        $fileName = basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . time() . "_" . $fileName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $image_url = $targetFile;
        }
    }

    if (!empty($_FILES["image2"]["name"])) {
        $fileName2 = basename($_FILES["image2"]["name"]);
        $targetFile2 = $targetDir . time() . "_" . $fileName2;

        if (move_uploaded_file($_FILES["image2"]["tmp_name"], $targetFile2)) {
            $image_url2 = $targetFile2;
        }
    }

    if ($title && $propertyType && $address && $city && $price > 0) {
        try {
            $db->addProperty($title, $propertyType, $address, $city, $price, $status, $agentId, $image_url, $image_url2);
            $message = "Property added successfully.";
        } catch (Throwable $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        $message = "Please complete all required fields.";
    }
}
?>

<?php include("header.php"); ?>

<h2>Add Property</h2>

<?php if ($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Title</label>
    <input type="text" name="title" required>

    <label>Property Type</label>
    <input type="text" name="propertyType" placeholder="Apartment, House, Condo..." required>

    <label>Address</label>
    <input type="text" name="address" required>

    <label>City</label>
    <input type="text" name="city" required>

    <label>Price</label>
    <input type="number" step="0.01" name="price" required>

    <label>Status</label>
    <select name="status">
        <option value="available">available</option>
        <option value="sold">sold</option>
        <option value="rented">rented</option>
    </select>

    <label>Exterior Image</label>
    <input type="file" name="image">

    <label>Interior Image</label>
    <input type="file" name="image2">

    <button type="submit">Add Property</button>
</form>

<?php include("footer.php"); ?>