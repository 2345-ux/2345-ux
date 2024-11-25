<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode([
        'status' => 'error',
        'message' => 'Méthode non autorisée'
    ]));
}

try {
    // Validation et nettoyage des données
    $shopData = [
        'shop_code' => filter_input(INPUT_POST, 'shop_code', FILTER_SANITIZE_STRING),
        'shop_number' => filter_input(INPUT_POST, 'shop_number', FILTER_SANITIZE_STRING),
        'shop_name' => filter_input(INPUT_POST, 'shop_name', FILTER_SANITIZE_STRING),
        'tenant_name' => filter_input(INPUT_POST, 'tenant_name', FILTER_SANITIZE_STRING),
        'tenant_phone' => filter_input(INPUT_POST, 'tenant_phone', FILTER_SANITIZE_STRING),
        'shop_rent' => filter_input(INPUT_POST, 'shop_rent', FILTER_VALIDATE_FLOAT) ?: 0.00,
        'shop_localisation' => filter_input(INPUT_POST, 'shop_localisation', FILTER_SANITIZE_STRING),
        'id_marche' => filter_input(INPUT_POST, 'id_marche', FILTER_SANITIZE_STRING),
        'shop_latitude' => filter_input(INPUT_POST, 'shop_latitude', FILTER_VALIDATE_FLOAT),
        'shop_longitude' => filter_input(INPUT_POST, 'shop_longitude', FILTER_VALIDATE_FLOAT)
    ];

    // Validation des champs requis
    $requiredFields = ['shop_name', 'shop_localisation', 'id_marche', 'shop_latitude', 'shop_longitude'];
    $missingFields = array_filter($requiredFields, function($field) use ($shopData) {
        return empty($shopData[$field]);
    });

    if (!empty($missingFields)) {
        throw new Exception('Champs obligatoires manquants : ' . implode(', ', $missingFields));
    }

    // Validation des coordonnées GPS
    if (!is_numeric($shopData['shop_latitude']) || !is_numeric($shopData['shop_longitude']) ||
        $shopData['shop_latitude'] < -90 || $shopData['shop_latitude'] > 90 ||
        $shopData['shop_longitude'] < -180 || $shopData['shop_longitude'] > 180) {
        throw new Exception('Coordonnées GPS invalides');
    }

    // Connexion et configuration PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Préparation et exécution de la requête
    $sql = "INSERT INTO t_shop (
        shop_code, shop_number, shop_name, tenant_name,
        tenant_phone, shop_rent, shop_localisation, id_marche,
        shop_latitude, shop_longitude
    ) VALUES (
        :shop_code, :shop_number, :shop_name, :tenant_name,
        :tenant_phone, :shop_rent, :shop_localisation, :id_marche,
        :shop_latitude, :shop_longitude
    )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($shopData);

    echo json_encode([
        'status' => 'success',
        'message' => 'Boutique ajoutée avec succès',
        'id' => $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {
    error_log("Erreur PDO: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => "Erreur lors de l'ajout en base de données"
    ]);

} catch (Exception $e) {
    error_log("Erreur: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}