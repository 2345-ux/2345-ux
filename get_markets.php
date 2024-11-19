<?php
// get_markets.php
header('Content-Type: application/json');

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=rent_shop;charset=utf8",
        "saw24",
        "saw24",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Requête pour récupérer les marchés
    $stmt = $pdo->query("SELECT * FROM t_marche");
    $markets = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'message' => 'Liste des marchés récupérée avec succès !',
        'donnees' => $markets
    ]);
} catch (PDOException $e) {
    http_response_code(500); // Indiquer une erreur serveur
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?>
