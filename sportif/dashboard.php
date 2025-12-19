<?php
session_start();
require_once "../Connectdb/connect.php";

// verifier que l'utilisateur est connecte et a le role "sportif"
if (!isset($_SESSION['id_personne']) || $_SESSION['role'] !== 'sportif') {
    header("Location: ../auth/login.php");
    exit;
}

$id_personne = $_SESSION['id_personne'];

// recuperer l'id_sportif correspondant
$stmtSportif = $conn->prepare("SELECT id_sportif FROM sportif WHERE id_personne = ?");
$stmtSportif->bind_param("i", $id_personne);
$stmtSportif->execute();
$resultSportif = $stmtSportif->get_result();

if ($resultSportif->num_rows === 0) {
    die("Sportif introuvable.");
}

$id_sportif = $resultSportif->fetch_assoc()['id_sportif'];

// nombre total de reservations
$stmtTotal = $conn->prepare("SELECT COUNT(*) AS total_reservations FROM reservation WHERE id_sportif = ? AND (statut='en_attente' OR statut='confirmée')");
$stmtTotal->bind_param("i", $id_sportif);
$stmtTotal->execute();
$totalReservations = $stmtTotal->get_result()->fetch_assoc()['total_reservations'];

// prochaine seance
$stmtNext = $conn->prepare("
    SELECT d.date, d.heure_debut AS heure, c.id_personne AS coach_id, p.nom, p.prenom
    FROM reservation r
    JOIN disponibilite d ON r.id_disponibilite = d.id_disponibilite
    JOIN coach c ON r.id_coach = c.id_coach
    JOIN personne p ON c.id_personne = p.id_personne
    WHERE r.id_sportif = ? AND d.date >= CURDATE() AND r.statut='confirmée'
    ORDER BY d.date ASC, d.heure_debut ASC
    LIMIT 1
");
$stmtNext->bind_param("i", $id_sportif);
$stmtNext->execute();
$nextSession = $stmtNext->get_result()->fetch_assoc();

// Reservations recentes
$stmtRecent = $conn->prepare("
    SELECT d.date, d.heure_debut AS heure, c.id_personne AS coach_id, p.nom, p.prenom, r.statut
    FROM reservation r
    JOIN disponibilite d ON r.id_disponibilite = d.id_disponibilite
    JOIN coach c ON r.id_coach = c.id_coach
    JOIN personne p ON c.id_personne = p.id_personne
    WHERE r.id_sportif = ? AND (r.statut='en_attente' OR r.statut='confirmée')
    ORDER BY d.date DESC, d.heure_debut DESC
    LIMIT 5
");
$stmtRecent->bind_param("i", $id_sportif);
$stmtRecent->execute();
$resultRecent = $stmtRecent->get_result();
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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Sportif</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">

<div class="flex min-h-screen">
    <?php include '../Components/aside_sportif.php'; ?>
    <main class="flex-1 lg:ml-72 p-6 md:p-10 pb-24 lg:pb-10 transition-all">

        
        <h1 class="text-3xl font-bold mb-6">Bienvenue sur votre dashboard</h1>

        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-md">
                <h2 class="text-xl font-semibold mb-2">Nombre total de réservations</h2>
                <p class="text-2xl font-bold text-indigo-600"><?= htmlspecialchars($totalReservations) ?></p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-md">
                <h2 class="text-xl font-semibold mb-2">Prochaine séance</h2>
                <?php if ($nextSession): ?>
                    <p class="text-indigo-600 font-bold">
                        <?= date('d/m/Y', strtotime($nextSession['date'])) ?> à <?= date('H:i', strtotime($nextSession['heure'])) ?>
                    </p>
                    <p class="text-gray-500">
                        Coach : <?= htmlspecialchars($nextSession['nom'] . ' ' . $nextSession['prenom']) ?>
                    </p>
                <?php else: ?>
                    <p class="text-gray-500">Aucune séance prévue</p>
                <?php endif; ?>
            </div>
        </div>


        
        <div class="bg-white p-6 rounded-2xl shadow-md">
            <h2 class="text-xl font-semibold mb-4">Réservations récentes</h2>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="border-b p-2">Date</th>
                        <th class="border-b p-2">Heure</th>
                        <th class="border-b p-2">Coach</th>
                        <th class="border-b p-2">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $resultRecent->fetch_assoc()): ?>
                    <tr>
                        <td class="border-b p-2"><?= date('d/m/Y', strtotime($row['date'])) ?></td>
                        <td class="border-b p-2"><?= date('H:i', strtotime($row['heure'])) ?></td>
                        <td class="border-b p-2"><?= htmlspecialchars($row['nom'] . ' ' . $row['prenom']) ?></td>
                        <td class="border-b p-2"><?= htmlspecialchars($row['statut']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </main>
</div>
</body>
</html>
