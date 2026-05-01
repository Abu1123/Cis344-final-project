<?php
require_once("config.php");
require_once("auth.php");
require_once("RealEstateDatabase.php");

requireRole(["agent"]);

$db = new RealEstateDatabase();
$agentId = $_SESSION["user"]["userId"];

$inquiries = $db->getAgentInquiries($agentId);
?>

<?php include("header.php"); ?>

<h2>My Property Inquiries</h2>

<?php if (!$inquiries): ?>
    <p>No inquiries yet.</p>
<?php endif; ?>

<?php foreach ($inquiries as $inq): ?>
    <div class="card">
        <p><strong>Property:</strong> <?= htmlspecialchars($inq["title"]) ?></p>
        <p><strong>User:</strong> <?= htmlspecialchars($inq["userName"]) ?></p>
        <p><strong>Message:</strong> <?= htmlspecialchars($inq["message"]) ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($inq["inquiryDate"]) ?></p>
    </div>
<?php endforeach; ?>

<?php include("footer.php"); ?>