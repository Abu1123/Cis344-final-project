<?php
require_once("config.php");
require_once("auth.php");
require_once("RealEstateDatabase.php");

requireRole(["buyer", "renter"]);

$db = new RealEstateDatabase();

$userId = $_SESSION["user"]["userId"];
$propertyId = (int)($_GET["id"] ?? 0);

if ($propertyId > 0) {
    $db->removeFavorite($userId, $propertyId);
}

header("Location: favorites.php");
exit;
?>