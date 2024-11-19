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
    p.code_location, 
    p.numero_boutique, 
    p.nom_locataire, 
    p.montant_verse, 
    p.date_paiement, 
    p.situation, 
    m.nom_marche
FROM 
    t_paiements p
LEFT JOIN 
    t_marche m 
ON 
    p.marche_id = m.id
ORDER BY 
    p.date_paiement DESC
LIMIT 0, 25;
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
