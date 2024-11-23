<?php
// add_rental_payment.php
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
    $rental_code = generateUniqueCode('PAY');
    $shop_name = $_POST['shop_name'] ?? '';
    $tenant_name = $_POST['tenant_name'] ?? '';
    $montant_verse = $_POST['montant_verse'] ?? '';
    $payment_date = $_POST['payment_date'] ?? '';
    $nom_marche = $_POST['nom_marche'] ?? '';
    $situation_name = $_POST['situation_name'] ?? '';

    // Validation des données
    if (empty($shop_name) || empty($tenant_name) || empty($montant_verse) || 
        empty($payment_date) || empty($nom_marche) || empty($situation_name)) {
        throw new Exception("Tous les champs sont requis pour ajouter un paiement.");
    }

    // Validation du montant
    if (!is_numeric($montant_verse)) {
        throw new Exception("Le montant versé doit être un nombre valide");
    }

    // Vérification des doublons
    $checkStmt = $pdo->prepare("
        SELECT COUNT(*) as count FROM t_payments 
        WHERE shop_name = :shop_name 
        AND tenant_name = :tenant_name 
        AND payment_date = :payment_date
    ");
    $checkStmt->execute([
        ':shop_name' => $shop_name,
        ':tenant_name' => $tenant_name,
        ':payment_date' => $payment_date
    ]);

    $result = $checkStmt->fetch();
    if ($result['count'] > 0) {
        throw new Exception("Un paiement similaire existe déjà pour ce locataire et cette date.");
    }

    // Insertion du paiement
    $stmt = $pdo->prepare("
        INSERT INTO t_payments (
            rental_code, shop_name, tenant_name, montant_verse, 
            payment_date, nom_marche, situation_name
        ) VALUES (
            :rental_code, :shop_name, :tenant_name, :montant_verse, 
            :payment_date, :nom_marche, :situation_name
        )
    ");

    $stmt->execute([
        ':rental_code' => $rental_code,
        ':shop_name' => $shop_name,
        ':tenant_name' => $tenant_name,
        ':montant_verse' => $montant_verse,
        ':payment_date' => $payment_date,
        ':nom_marche' => $nom_marche,
        ':situation_name' => $situation_name
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Paiement ajouté avec succès !',
        'data' => [
            'rental_code' => $rental_code,
            'shop_name' => $shop_name,
            'tenant_name' => $tenant_name,
            'montant_verse' => $montant_verse,
            'payment_date' => $payment_date,
            'nom_marche' => $nom_marche,
            'situation_name' => $situation_name
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
