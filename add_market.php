<?php
// add_market.php
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

    // Récupération des données POST
    $code = generateUniqueCode('MKT');
    $nom_marche = $_POST['nom_marche'] ?? '';
    $region = $_POST['region'] ?? '';
    $commune = $_POST['commune'] ?? '';

    // Validation des données
    if (empty($nom_marche) || empty($region) || empty($commune)) {
        throw new Exception("Tous les champs sont requis pour ajouter un marché.");
    }

    // Insertion du marché
    $stmt = $pdo->prepare("INSERT INTO t_marche (code, nom_marche, region, commune) VALUES (:code, :nom_marche, :region, :commune)");
    $stmt->execute([
        ':code' => $code,
        ':nom_marche' => $nom_marche,
        ':region' => $region,
        ':commune' => $commune
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Marché ajouté avec succès !',
        'data' => [
            'code' => $code,
            'nom_marche' => $nom_marche,
            'region' => $region,
            'commune' => $commune
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erreur de base de données: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

function generateUniqueCode($prefix) {
    $date = new DateTime();
    return $prefix . $date->format('YmdHis') . sprintf('%03d', rand(0, 999));
}
?>
