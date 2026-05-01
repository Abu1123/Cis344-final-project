<?php
require_once("config.php");
require_once("auth.php");
requireLogin();

$user = $_SESSION["user"];
?>

<?php include("header.php"); ?>

<h2>Dashboard</h2>

<div class="card">
    <p><strong>Welcome:</strong> <?= htmlspecialchars($user["userName"]) ?></p>
    <p><strong>Role:</strong> <?= htmlspecialchars($user["userType"]) ?></p>
</div>

<?php if ($user["userType"] === "agent"): ?>
    <div class="card">
        <h3>Agent Actions</h3>
        <a href="add_property.php">Add Property</a>

        <br>

        <a href="inquiries.php">View Inquiries</a>
    </div>
<?php endif; ?>

<div class="card">
    <h3>Common Actions</h3>
    <a href="properties.php">Browse Properties</a>

    <?php if (in_array($user["userType"], ["buyer", "renter"])): ?>
        <br>
        <a href="favorites.php">My Favorites</a>
    <?php endif; ?>
</div>

<?php include("footer.php"); ?>