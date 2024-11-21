<?php
// get_rentpaye.php
header('Content-Type: application/json');

try {
    // Connexion à la base de données
    $pdo = new PDO(
        "mysql:host=localhost;dbname=rent_shop;charset=utf8",
        "saw24",
        "saw24",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Requête pour récupérer les paiements avec les informations supplémentaires
    $stmt = $pdo->query("
SELECT 
    t_payments.id, 
    t_payments.shop_name, 
    t_payments.tenant_name, 
    t_payments.montant_verse, 
    t_payments.payment_date, 
    t_marche.nom_marche, 
    t_situations.situation_nom
FROM t_payments
LEFT JOIN t_marche ON t_payments.nom_marche = t_marche.id
LEFT JOIN t_situations ON t_payments.situation_name = t_situations.code
    ");
    $payments = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'message' => 'Liste des paiements récupérée avec succès !',
        'donnees' => $payments
    ]);
} catch (PDOException $e) {
    http_response_code(500); // Indiquer une erreur serveur
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?>
