<?php
require_once("config.php");
require_once("auth.php");
require_once("RealEstateDatabase.php");

requireRole(["agent"]);

$db = new RealEstateDatabase();

$id = (int)($_GET["id"] ?? 0);
$property = $db->getPropertyById($id);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $db->updateProperty(
        $id,
        $_POST["title"],
        $_POST["propertyType"],
        $_POST["address"],
        $_POST["city"],
        (float)$_POST["price"],
        $_POST["status"]
    );

    header("Location: properties.php");
    exit;
}
?>

<?php include("header.php"); ?>

<h2>Edit Property</h2>

<form method="POST">
    <input type="text" name="title" value="<?= htmlspecialchars($property["title"]) ?>">
    <input type="text" name="propertyType" value="<?= htmlspecialchars($property["propertyType"]) ?>">
    <input type="text" name="address" value="<?= htmlspecialchars($property["address"]) ?>">
    <input type="text" name="city" value="<?= htmlspecialchars($property["city"]) ?>">
    <input type="number" name="price" value="<?= htmlspecialchars($property["price"]) ?>">

    <select name="status">
        <option value="available">available</option>
        <option value="sold">sold</option>
        <option value="rented">rented</option>
    </select>

    <button type="submit">Update</button>
</form>

<?php include("footer.php"); ?>