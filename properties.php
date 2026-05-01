<?php
require_once("RealEstateDatabase.php");

$db = new RealEstateDatabase();

$city = $_GET["city"] ?? "";
$minPrice = (float)($_GET["minPrice"] ?? 0);
$maxPrice = (float)($_GET["maxPrice"] ?? 0);

if ($city || $minPrice || $maxPrice) {
    $properties = $db->searchProperties($city, $minPrice, $maxPrice);
} else {
    $properties = $db->getAllProperties();
}
?>

<?php include("header.php"); ?>

<form method="GET" style="margin-bottom:20px;">
    <input type="text" name="city" placeholder="City" value="<?= htmlspecialchars($_GET["city"] ?? "") ?>">

    <input type="number" name="minPrice" placeholder="Min Price" value="<?= htmlspecialchars($_GET["minPrice"] ?? "") ?>">

    <input type="number" name="maxPrice" placeholder="Max Price" value="<?= htmlspecialchars($_GET["maxPrice"] ?? "") ?>">

    <button type="submit">Search</button>
</form>

<h2>Property Listings</h2>

<?php if (!$properties): ?>
    <p>No properties found.</p>
<?php endif; ?>

<?php foreach ($properties as $property): ?>
    <div class="card">

        <div style="display:flex; gap:10px;">
            <?php if (!empty($property["image_url"])): ?>
                <img src="<?= htmlspecialchars($property["image_url"]) ?>" width="200">
            <?php endif; ?>

            <?php if (!empty($property["image_url2"])): ?>
                <img src="<?= htmlspecialchars($property["image_url2"]) ?>" width="200">
            <?php endif; ?>
        </div>

        <h3><?= htmlspecialchars($property["title"]) ?></h3>
        <p><strong>Type:</strong> <?= htmlspecialchars($property["propertyType"]) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($property["address"]) ?></p>
        <p><strong>City:</strong> <?= htmlspecialchars($property["city"]) ?></p>
        <p><strong>Price:</strong> $<?= htmlspecialchars($property["price"]) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($property["status"]) ?></p>
        <p><strong>Agent:</strong> <?= htmlspecialchars($property["agentName"]) ?></p>

        <a href="property_details.php?id=<?= (int)$property["propertyId"] ?>">View Details</a>

        <?php if (isset($_SESSION["user"]) && $_SESSION["user"]["userType"] === "agent"): ?>

            <a href="edit_property.php?id=<?= (int)$property["propertyId"] ?>">Edit</a>

            <a href="delete_property.php?id=<?= (int)$property["propertyId"] ?>" onclick="return confirm('Delete this property?')">Delete</a>

        <?php endif; ?>

    </div>
<?php endforeach; ?>

<?php include("footer.php"); ?>