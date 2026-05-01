<?php
require_once("Database.php");

class RealEstateDatabase {
    private PDO $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function addUser(string $userName, string $contactInfo, string $passwordHash, string $userType): bool {
        $stmt = $this->conn->prepare("
            CALL AddOrUpdateUser(NULL, :userName, :contactInfo, :passwordHash, :userType)
        ");

        return $stmt->execute([
            ":userName" => $userName,
            ":contactInfo" => $contactInfo,
            ":passwordHash" => $passwordHash,
            ":userType" => $userType
        ]);
    }

    public function getUserByUsername(string $userName) {
        $sql = "SELECT * FROM Users WHERE userName = :userName LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":userName" => $userName]);
        return $stmt->fetch();
    }

    public function addProperty($title, $propertyType, $address, $city, $price, $status, $agentId, $image_url, $image_url2) {
        $sql = "INSERT INTO Properties (title, propertyType, address, city, price, status, agentId, image_url, image_url2)
                VALUES (:title, :propertyType, :address, :city, :price, :status, :agentId, :image_url, :image_url2)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ":title" => $title,
            ":propertyType" => $propertyType,
            ":address" => $address,
            ":city" => $city,
            ":price" => $price,
            ":status" => $status,
            ":agentId" => $agentId,
            ":image_url" => $image_url,
            ":image_url2" => $image_url2
        ]);
    }

    public function getAllProperties(): array {
        $sql = "SELECT * FROM PropertyListingView ORDER BY propertyId DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll();
    }

    public function getPropertyById(int $propertyId) {
        $sql = "SELECT * FROM PropertyListingView WHERE propertyId = :propertyId";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":propertyId" => $propertyId]);
        return $stmt->fetch();
    }

    public function addInquiry(int $userId, int $propertyId, string $message): bool {
        $sql = "INSERT INTO Inquiries (userId, propertyId, message, inquiryDate)
                VALUES (:userId, :propertyId, :message, NOW())";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ":userId" => $userId,
            ":propertyId" => $propertyId,
            ":message" => $message
        ]);
    }

    public function getUserDetails(int $userId) {
        $stmt = $this->conn->prepare("SELECT * FROM Users WHERE userId = :userId");
        $stmt->execute([":userId" => $userId]);
        $user = $stmt->fetch();

        if (!$user) {
            return null;
        }

        $stmt = $this->conn->prepare("
            SELECT Inquiries.*, Properties.title
            FROM Inquiries
            INNER JOIN Properties ON Inquiries.propertyId = Properties.propertyId
            WHERE Inquiries.userId = :userId
        ");
        $stmt->execute([":userId" => $userId]);
        $inquiries = $stmt->fetchAll();

        $stmt = $this->conn->prepare("
            SELECT Favorites.*, Properties.title
            FROM Favorites
            INNER JOIN Properties ON Favorites.propertyId = Properties.propertyId
            WHERE Favorites.userId = :userId
        ");
        $stmt->execute([":userId" => $userId]);
        $favorites = $stmt->fetchAll();

        $stmt = $this->conn->prepare("
            SELECT Transactions.*, Properties.title
            FROM Transactions
            INNER JOIN Properties ON Transactions.propertyId = Properties.propertyId
            WHERE Transactions.userId = :userId
        ");
        $stmt->execute([":userId" => $userId]);
        $transactions = $stmt->fetchAll();

        return [
            "user" => $user,
            "inquiries" => $inquiries,
            "favorites" => $favorites,
            "transactions" => $transactions
        ];
    }

    public function processTransaction(int $propertyId, int $userId, string $type, float $amount): bool {
        $stmt = $this->conn->prepare("
            CALL ProcessTransaction(:propertyId, :userId, :type, :amount)
        ");

        return $stmt->execute([
            ":propertyId" => $propertyId,
            ":userId" => $userId,
            ":type" => $type,
            ":amount" => $amount
        ]);
    }

    public function searchProperties($city = "", $minPrice = 0, $maxPrice = 0) {
        $sql = "SELECT * FROM PropertyListingView WHERE 1=1";
        $params = [];

        if ($city !== "") {
            $sql .= " AND city = :city";
            $params[":city"] = $city;
        }

        if ($minPrice > 0) {
            $sql .= " AND price >= :minPrice";
            $params[":minPrice"] = $minPrice;
        }

        if ($maxPrice > 0) {
            $sql .= " AND price <= :maxPrice";
            $params[":maxPrice"] = $maxPrice;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function deleteProperty($propertyId) {

    $this->conn->prepare("DELETE FROM Transactions WHERE propertyId = :id")
        ->execute([":id" => $propertyId]);

    $this->conn->prepare("DELETE FROM Inquiries WHERE propertyId = :id")
        ->execute([":id" => $propertyId]);

    $this->conn->prepare("DELETE FROM Favorites WHERE propertyId = :id")
        ->execute([":id" => $propertyId]);

    $stmt = $this->conn->prepare("DELETE FROM Properties WHERE propertyId = :id");
    return $stmt->execute([":id" => $propertyId]);
}

    public function updateProperty($id, $title, $propertyType, $address, $city, $price, $status) {
        $sql = "UPDATE Properties 
                SET title = :title, propertyType = :propertyType, address = :address, city = :city, price = :price, status = :status
                WHERE propertyId = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ":title" => $title,
            ":propertyType" => $propertyType,
            ":address" => $address,
            ":city" => $city,
            ":price" => $price,
            ":status" => $status,
            ":id" => $id
        ]);
    }

    public function addFavorite($userId, $propertyId) {
        $sql = "INSERT INTO Favorites (userId, propertyId, savedDate)
                VALUES (:userId, :propertyId, NOW())";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ":userId" => $userId,
            ":propertyId" => $propertyId
        ]);
    }

    public function getFavorites($userId) {
        $sql = "SELECT p.*, u.userName AS agentName
                FROM Favorites f
                JOIN Properties p ON f.propertyId = p.propertyId
                JOIN Users u ON p.agentId = u.userId
                WHERE f.userId = :userId";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":userId" => $userId]);

        return $stmt->fetchAll();
    }

    public function getAgentInquiries($agentId) {
        $sql = "SELECT i.*, p.title, u.userName
                FROM Inquiries i
                JOIN Properties p ON i.propertyId = p.propertyId
                JOIN Users u ON i.userId = u.userId
                WHERE p.agentId = :agentId";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":agentId" => $agentId]);

        return $stmt->fetchAll();
    }

    public function removeFavorite($userId, $propertyId) {
        $sql = "DELETE FROM Favorites WHERE userId = :userId AND propertyId = :propertyId";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ":userId" => $userId,
            ":propertyId" => $propertyId
        ]);
    }
}
?>