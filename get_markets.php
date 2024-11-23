<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Si nécessaire pour le développement

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

    // Sélectionner spécifiquement les colonnes nécessaires
    $stmt = $pdo->query("SELECT * FROM t_marche ORDER BY nom_marche");
    $markets = $stmt->fetchAll();

    if (!empty($markets)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Liste des marchés récupérée avec succès !',
            'donnees' => $markets
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Aucun marché trouvé.',
            'donnees' => []
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur de base de données: ' . $e->getMessage(),
        'donnees' => []
    ]);
}