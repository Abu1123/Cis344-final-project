<?php
require_once("RealEstateDatabase.php");

$db = new RealEstateDatabase();

$propertyId = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$property = $db->getPropertyById($propertyId);
?>

<?php include("header.php"); ?>

<h2>Property Details</h2>

<?php if (!$property): ?>
    <p class="error">Property not found.</p>
<?php else: ?>
    <div class="card">

        <div style="display:flex; gap:10px;">
            <?php if (!empty($property["image_url"])): ?>
                <img src="<?= htmlspecialchars($property["image_url"]) ?>" width="300">
            <?php endif; ?>

            <?php if (!empty($property["image_url2"])): ?>
                <img src="<?= htmlspecialchars($property["image_url2"]) ?>" width="300">
            <?php endif; ?>
        </div>

        <h3><?= htmlspecialchars($property["title"]) ?></h3>
        <p><strong>Type:</strong> <?= htmlspecialchars($property["propertyType"]) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($property["address"]) ?></p>
        <p><strong>City:</strong> <?= htmlspecialchars($property["city"]) ?></p>
        <p><strong>Price:</strong> $<?= htmlspecialchars($property["price"]) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($property["status"]) ?></p>
        <p><strong>Agent:</strong> <?= htmlspecialchars($property["agentName"]) ?></p>
    </div>

    <?php if (
        isset($_SESSION["user"]) &&
        in_array($_SESSION["user"]["userType"], ["buyer", "renter"], true) &&
        $property["status"] === "available"
    ): ?>

        <a href="submit_inquiry.php?propertyId=<?= (int)$property["propertyId"] ?>">Submit Inquiry</a>

        <a href="add_favorite.php?id=<?= (int)$property["propertyId"] ?>">Save to Favorites</a>

        <form method="POST" action="process_transaction.php">
            <input type="hidden" name="propertyId" value="<?= (int)$property["propertyId"] ?>">
            <input type="hidden" name="type" value="sale">
            <input type="hidden" name="amount" value="<?= (float)$property["price"] ?>">

            <button type="submit">Buy Property</button>
        </form>

        <form method="POST" action="process_transaction.php">
            <input type="hidden" name="propertyId" value="<?= (int)$property["propertyId"] ?>">
            <input type="hidden" name="type" value="rental">
            <input type="hidden" name="amount" value="<?= (float)$property["price"] ?>">

            <button type="submit">Rent Property</button>
        </form>

    <?php endif; ?>

<?php endif; ?>

<?php include("footer.php"); ?>