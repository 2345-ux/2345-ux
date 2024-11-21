<?php
// add_rental_payment.php
header('Content-Type: application/json');

function generateUniqueCode($prefix) {
    $date = new DateTime();
    return $prefix . $date->format('YmdHis') . sprintf('%03d', rand(0, 999));
}

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
    $missingFields = [];

    if (empty($shop_name)) $missingFields[] = 'Nom de la boutique';
    if (empty($tenant_name)) $missingFields[] = 'Nom du locataire';
    if (empty($montant_verse)) $missingFields[] = 'Montant versé';
    if (empty($payment_date)) $missingFields[] = 'Date de paiement';
    if (empty($nom_marche)) $missingFields[] = 'Nom du marché';
    if (empty($situation_name)) $missingFields[] = 'Nom de la situation';

    // Vérification des champs requis
    if (!empty($missingFields)) {
        throw new Exception("Les champs suivants sont requis : " . implode(', ', $missingFields));
    }

    // Validation du montant
    $montant_verse = filter_var($montant_verse, FILTER_VALIDATE_FLOAT);
    if ($montant_verse === false) {
        throw new Exception("Le montant versé doit être un nombre valide");
    }

    // Vérification des doublons
    $stmt_duplicate = $pdo->prepare("
        SELECT COUNT(*) 
        FROM t_payments 
        WHERE 
            shop_name = :shop_name AND 
            tenant_name = :tenant_name AND 
            montant_verse = :montant_verse AND 
            payment_date = :payment_date AND 
            situation_name = :situation_name AND 
            nom_marche = :nom_marche
    ");
    $stmt_duplicate->execute([
        ':shop_name' => $shop_name,
        ':tenant_name' => $tenant_name,
        ':montant_verse' => $montant_verse,
        ':payment_date' => $payment_date,
        ':situation_name' => $situation_name,
        ':nom_marche' => $nom_marche
    ]);

    if ($stmt_duplicate->fetchColumn() > 0) {
        throw new Exception("Ce paiement existe déjà");
    }

    // Insertion du paiement
    $stmt = $pdo->prepare("
        INSERT INTO t_payments (
            rental_code, 
            shop_name, 
            tenant_name, 
            montant_verse, 
            payment_date, 
            nom_marche,  
            situation_name
        ) VALUES (
            :rental_code, 
            :shop_name, 
            :tenant_name, 
            :montant_verse, 
            :payment_date, 
            :nom_marche, 
            :situation_name
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

    // Réponse de succès
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
    // Erreur de base de données
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Erreur de base de données : ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    // Autres erreurs (validation, etc.)
    http_response_code(400);
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage()
    ]);
}
?>