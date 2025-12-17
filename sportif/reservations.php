<?php
session_start();
require_once "../Connectdb/connect.php";

// Vérifier que l'utilisateur est connecté et a le rôle "sportif"
if (!isset($_SESSION['id_personne']) || $_SESSION['role'] !== 'sportif') {
    header("Location: ../auth/login.php");
    exit;
}

$id_personne = $_SESSION['id_personne'];
/* les info de user */
$stmt = $conn->prepare("
    SELECT p.nom, p.prenom, r.nom_role 
    FROM personne p
    JOIN role r ON p.id_role = r.id_role
    JOIN sportif s ON s.id_personne = p.id_personne
    WHERE p.id_personne = ?
");
$stmt->bind_param("i", $id_personne);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$fullName = htmlspecialchars($user['nom'] . ' ' . $user['prenom']);
$roleName = htmlspecialchars(strtoupper($user['nom_role']));

// Récupérer l'id_sportif pour l'utilisateur connecté
$stmtSportif = $conn->prepare("SELECT id_sportif FROM sportif WHERE id_personne = ?");
$stmtSportif->bind_param("i", $id_personne);
$stmtSportif->execute();
$resultSportif = $stmtSportif->get_result();

if ($resultSportif->num_rows === 0) {
    die("Sportif introuvable !");
}

$id_sportif = $resultSportif->fetch_assoc()['id_sportif'];


/* recuperer les réservations du sportif */
$stmtRes = $conn->prepare("
    SELECT r.id_reservation, r.statut,
           d.date, d.heure_debut, d.heure_fin,
           c.id_coach, p.nom AS coach_nom, p.prenom AS coach_prenom
    FROM reservation r
    JOIN disponibilite d ON r.id_disponibilite = d.id_disponibilite
    JOIN coach c ON r.id_coach = c.id_coach
    JOIN personne p ON c.id_personne = p.id_personne
    WHERE r.id_sportif = ?
    ORDER BY d.date DESC, d.heure_debut DESC
");
$stmtRes->bind_param("i", $id_sportif);
$stmtRes->execute();
$reservations = $stmtRes->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation</title>
</head>
<body>
    <div class="flex min-h-screen">
     <?php include '../Components/aside_sportif.php'; ?>
     <main class="flex-1 lg:ml-72 p-6 md:p-10 pb-24 lg:pb-10 transition-all">
        
        <h1 class="text-3xl font-bold mb-6">Mes Réservations</h1>

        <div class="bg-white p-6 rounded-2xl shadow-md overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="border-b p-2">Coach</th>
                        <th class="border-b p-2">Date</th>
                        <th class="border-b p-2">Heure</th>
                        <th class="border-b p-2">Statut</th>
                        <th class="border-b p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reservations)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">Aucune réservation trouvée</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reservations as $res): ?>
                        <tr>
                            <td class="border-b p-2"><?= htmlspecialchars($res['coach_nom'] . ' ' . $res['coach_prenom']) ?></td>
                            <td class="border-b p-2"><?= date('d/m/Y', strtotime($res['date'])) ?></td>
                            <td class="border-b p-2"><?= date('H:i', strtotime($res['heure_debut'])) ?> - <?= date('H:i', strtotime($res['heure_fin'])) ?></td>
                            <td class="border-b p-2"><?= htmlspecialchars($res['statut']) ?></td>
                            <td class="border-b p-2 flex gap-2">
                                <?php if ($res['statut'] === 'en_attente'): ?>
                                    <a href="modifier_reservation.php?id=<?= $res['id_reservation'] ?>" class="bg-yellow-400 px-3 py-1 rounded text-white text-sm hover:bg-yellow-500 transition">Modifier</a>
                                <?php endif; ?>
                                <a href="annuler_reservation.php?id=<?= $res['id_reservation'] ?>" class="bg-red-500 px-3 py-1 rounded text-white text-sm hover:bg-red-600 transition">Annuler</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
     </main>
    </div>
    <script src="https://cdn.tailwindcss.com"></script>
</body>
</html>