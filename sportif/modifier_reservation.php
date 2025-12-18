<?php
session_start();
require_once "../Connectdb/connect.php";

// Vérifier que l'utilisateur est connecté et est un sportif
if (!isset($_SESSION['id_personne']) || $_SESSION['role'] !== 'sportif') {
    header("Location: ../auth/login.php");
    exit;
}

// Récupérer id_personne 
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
/* Récupérer id_sportif */
$stmtSportif = $conn->prepare("SELECT id_sportif FROM sportif WHERE id_personne = ?");
$stmtSportif->bind_param("i", $id_personne);
$stmtSportif->execute();
$resultSportif = $stmtSportif->get_result();
if ($resultSportif->num_rows === 0) die("Sportif introuvable");
$id_sportif = $resultSportif->fetch_assoc()['id_sportif'];

// Récupérer l'id_reservation
$id_reservation = $_GET['id'] ?? null;
if (!$id_reservation) die("ID réservation manquant");

// Vérifier que la réservation appartient au sportif et est en attente
$stmtCheck = $conn->prepare("
    SELECT r.id_reservation, r.statut, d.id_disponibilite, d.date, d.heure_debut, d.heure_fin
    FROM reservation r
    JOIN disponibilite d ON r.id_disponibilite = d.id_disponibilite
    WHERE r.id_reservation = ? AND r.id_sportif = ? AND r.statut = 'en_attente'
");
$stmtCheck->bind_param("ii", $id_reservation, $id_sportif);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();
$reservation = $resultCheck->fetch_assoc();
if (!$reservation) die("Réservation introuvable ou non modifiable");

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_disponibilite = $_POST['disponibilite'] ?? null;
    if ($new_disponibilite) {
        $stmtUpdate = $conn->prepare("UPDATE reservation SET id_disponibilite = ? WHERE id_reservation = ?");
        $stmtUpdate->bind_param("ii", $new_disponibilite, $id_reservation);
        $stmtUpdate->execute();
        header("Location: reservations.php?msg=modifie");
        exit;
    }
}

// Récupérer toutes les disponibilités disponibles pour modification
$stmtDisp = $conn->prepare("
    SELECT d.id_disponibilite, d.date, d.heure_debut, d.heure_fin, c.id_coach, p.nom AS coach_nom, p.prenom AS coach_prenom
    FROM disponibilite d
    JOIN coach c ON d.id_coach = c.id_coach
    JOIN personne p ON c.id_personne = p.id_personne
    WHERE d.statut = 'Disponible'
    ORDER BY d.date ASC, d.heure_debut ASC
");
$stmtDisp->execute();
$disponibilites = $stmtDisp->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Modifier Réservation</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">
<div class="flex min-h-screen">
<?php include '../Components/aside_sportif.php'; ?>
<main class="flex-1 lg:ml-72 p-6 md:p-10">
<h1 class="text-2xl font-bold mb-4">Modifier la réservation</h1>

<form method="POST" class="bg-white p-6 rounded-2xl shadow-md">
    <label class="block mb-2 font-bold">Sélectionner une nouvelle disponibilité :</label>
    <select name="disponibilite" class="w-full border rounded-lg p-2 mb-4" required>
        <?php foreach ($disponibilites as $d): ?>
            <option value="<?= $d['id_disponibilite'] ?>"
                <?= $d['id_disponibilite'] == $reservation['id_disponibilite'] ? 'selected' : '' ?>>
                <?= date('d/m/Y', strtotime($d['date'])) ?> | <?= date('H:i', strtotime($d['heure_debut'])) ?> - <?= date('H:i', strtotime($d['heure_fin'])) ?> | Coach: <?= htmlspecialchars($d['coach_nom'].' '.$d['coach_prenom']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">Modifier</button>
</form>
</main>
</div>
</body>
</html>
