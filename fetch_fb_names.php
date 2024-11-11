<?php
include 'db.php'; // Include the database connection file

// Check if the search term is set
if (isset($_GET['term'])) {
    $term = $_GET['term'];

    // Prepare the SQL statement to fetch matching FB names
    $sql = "SELECT DISTINCT fb_name FROM deposits WHERE fb_name LIKE :term";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':term', '%' . $term . '%');
    $stmt->execute();
    $fbNames = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the results as a JSON array
    echo json_encode($fbNames);
}
?>