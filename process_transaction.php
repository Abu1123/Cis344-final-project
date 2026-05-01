<?php
require_once("config.php");
require_once("auth.php");
require_once("RealEstateDatabase.php");

requireRole(["buyer", "renter"]);

$db = new RealEstateDatabase();

$propertyId = (int)($_POST["propertyId"] ?? 0);
$userId = (int)$_SESSION["user"]["userId"];
$type = $_POST["type"] ?? "sale";
$amount = (float)($_POST["amount"] ?? 0);

if ($propertyId > 0 && $amount > 0) {

    if ($db->processTransaction($propertyId, $userId, $type, $amount)) {
        echo "Transaction successful!";
        echo "<br><a href=\"properties.php\">Back to Properties</a>";
    } else {
        echo "Transaction failed.";
    }

} else {
    echo "Invalid transaction data.";
}
?>