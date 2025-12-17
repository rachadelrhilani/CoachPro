<?php 
session_start();
require_once "../Connectdb/connect.php";

// Vérifier que l'utilisateur est connecté et est un sportif
if (!isset($_SESSION['id_personne']) || $_SESSION['role'] !== 'sportif') {
    header("Location: ../auth/login.php");
    exit;
}

// Récupérer id_personne et id_sportif
$id_personne = $_SESSION['id_personne'];
$stmtSportif = $conn->prepare("SELECT id_sportif FROM sportif WHERE id_personne = ?");
$stmtSportif->bind_param("i", $id_personne);
$stmtSportif->execute();
$resultSportif = $stmtSportif->get_result();
if ($resultSportif->num_rows === 0){
    die("Sportif introuvable");
} 
$id_sportif = $resultSportif->fetch_assoc()['id_sportif'];

// Informations personnelles
$stmt = $conn->prepare("
    SELECT p.nom, p.prenom, p.email, p.telephone, r.nom_role
    FROM personne p
    JOIN role r ON p.id_role = r.id_role
    WHERE p.id_personne = ?
");
$stmt->bind_param("i", $id_personne);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$fullName = htmlspecialchars($user['nom'] . ' ' . $user['prenom']);
$roleName = htmlspecialchars(strtoupper($user['nom_role']));
// Historique des séances
$stmtRes = $conn->prepare("
    SELECT r.id_reservation, r.statut, d.date, d.heure_debut, d.heure_fin, 
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
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mon Profil</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">

<div class="flex min-h-screen">
<?php include '../Components/aside_sportif.php'; ?>

<main class="flex-1 lg:ml-72 p-6 md:p-10 pb-24 lg:pb-10 transition-all">

    <h1 class="text-3xl font-bold mb-6">Mon profil</h1>

    <!-- Informations personnelles -->
    <div class="bg-white p-6 rounded-2xl shadow-md mb-8">
        <h2 class="text-xl font-semibold mb-4">Informations personnelles</h2>
        <p><strong>Nom :</strong> <?= htmlspecialchars($user['nom']) ?></p>
        <p><strong>Prénom :</strong> <?= htmlspecialchars($user['prenom']) ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Téléphone :</strong> <?= htmlspecialchars($user['telephone']) ?></p>
        <p><strong>Rôle :</strong> <?= htmlspecialchars(strtoupper($user['nom_role'])) ?></p>
        <a href="modifier_profil.php" class="inline-block mt-4 bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">Modifier</a>
    </div>

    <!-- Historique des séances -->
    <div class="bg-white p-6 rounded-2xl shadow-md">
        <h2 class="text-xl font-semibold mb-4">Historique des séances</h2>
        <?php if (count($reservations) > 0): ?>
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
                <?php foreach ($reservations as $res): ?>
                <tr>
                    <td class="border-b p-2"><?= date('d/m/Y', strtotime($res['date'])) ?></td>
                    <td class="border-b p-2"><?= date('H:i', strtotime($res['heure_debut'])) ?> - <?= date('H:i', strtotime($res['heure_fin'])) ?></td>
                    <td class="border-b p-2"><?= htmlspecialchars($res['coach_nom'] . ' ' . $res['coach_prenom']) ?></td>
                    <td class="border-b p-2"><?= htmlspecialchars($res['statut']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p class="text-gray-500">Aucune séance réservée pour le moment.</p>
        <?php endif; ?>
    </div>

</main>
</div>

</body>
</html>
