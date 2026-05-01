<?php
require_once("config.php");
require_once("auth.php");
require_once("RealEstateDatabase.php");

requireRole(["agent"]);

$db = new RealEstateDatabase();
$id = (int)($_GET["id"] ?? 0);

if ($id > 0) {
    $db->deleteProperty($id);
}

header("Location: properties.php");
exit;
?>