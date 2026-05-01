<?php
require_once("config.php");
require_once("auth.php");
require_once("RealEstateDatabase.php");

requireRole(["buyer", "renter"]);

$db = new RealEstateDatabase();
$userId = $_SESSION["user"]["userId"];

$favorites = $db->getFavorites($userId);
?>

<?php include("header.php"); ?>

<h2>My Favorites</h2>

<?php if (!$favorites): ?>
    <p>No favorites yet.</p>
<?php endif; ?>

<?php foreach ($favorites as $property): ?>
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
        <p><strong>City:</strong> <?= htmlspecialchars($property["city"]) ?></p>
        <p><strong>Price:</strong> $<?= htmlspecialchars($property["price"]) ?></p>
        <p><strong>Agent:</strong> <?= htmlspecialchars($property["agentName"]) ?></p>

        <a href="property_details.php?id=<?= (int)$property["propertyId"] ?>"> View Details</a>

        <br>
        <a href="remove_favorite.php?id=<?= (int)$property["propertyId"] ?>" onclick="return confirm('Remove from favorites?')">Remove</a>

    </div>
<?php endforeach; ?>

<?php include("footer.php"); ?>