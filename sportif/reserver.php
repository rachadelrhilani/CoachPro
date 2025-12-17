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

// Traitement de la réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_disponibilite'])) {
    $id_disponibilite = intval($_POST['id_disponibilite']);

    // Récupérer l'id_coach de la disponibilité
    $stmtDisponibilite = $conn->prepare("SELECT id_coach, date, heure_debut FROM disponibilite WHERE id_disponibilite = ?");
    $stmtDisponibilite->bind_param("i", $id_disponibilite);
    $stmtDisponibilite->execute();
    $resDispo = $stmtDisponibilite->get_result()->fetch_assoc();
    $id_coach = $resDispo['id_coach'];
    $date_reservation = date('Y-m-d H:i:s');

    // Insérer la réservation
    $stmtRes = $conn->prepare("
        INSERT INTO reservation (date_reservation, statut, id_sportif, id_coach, id_disponibilite)
        VALUES (?, 'confirmée', ?, ?, ?)
    ");

    // Récupérer id_sportif
    $stmtSportif = $conn->prepare("SELECT id_sportif FROM sportif WHERE id_personne = ?");
    $stmtSportif->bind_param("i", $id_personne);
    $stmtSportif->execute();
    $id_sportif = $stmtSportif->get_result()->fetch_assoc()['id_sportif'];

    $stmtRes->bind_param("siii", $date_reservation, $id_sportif, $id_coach, $id_disponibilite);
    $stmtRes->execute();
    $success = "Séance réservée avec succès !";
}

// Filtre par discipline
$filter_discipline = $_GET['discipline'] ?? '';
$filter_date = $_GET['date'] ?? '';

// Récupérer la liste des disciplines
$disciplines = $conn->query("SELECT * FROM discipline")->fetch_all(MYSQLI_ASSOC);

// Récupérer la liste des coachs et leurs disponibilités
$query = "
    SELECT d.id_disponibilite, d.date, d.heure_debut, d.heure_fin, c.id_coach, 
           p.nom, p.prenom, GROUP_CONCAT(dist.nom) as disciplines
    FROM disponibilite d
    JOIN coach c ON d.id_coach = c.id_coach
    JOIN personne p ON c.id_personne = p.id_personne
    LEFT JOIN coach_discipline cd ON c.id_coach = cd.id_coach
    LEFT JOIN discipline dist ON cd.id_discipline = dist.id_discipline
    WHERE 1=1
";

$params = [];
$types = '';

// Filtre discipline
if ($filter_discipline) {
    $query .= " AND c.id_coach IN (
        SELECT cd2.id_coach 
        FROM coach_discipline cd2
        JOIN discipline dist2 ON cd2.id_discipline = dist2.id_discipline
        WHERE dist2.nom = ?
    ) ";
    $params[] = $filter_discipline;
    $types .= 's';
}

// Filtre date
if ($filter_date) {
    $query .= " AND d.date = ? ";
    $params[] = $filter_date;
    $types .= 's';
}

$query .= " GROUP BY d.id_disponibilite ORDER BY d.date ASC, d.heure_debut ASC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$disponibilites = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="flex min-h-screen">
    <?php include '../Components/aside_sportif.php'; ?>
    <main class="flex-1 lg:ml-72 p-6 md:p-10 pb-24 lg:pb-10 transition-all">
      <h1 class="text-3xl font-bold mb-6">Réserver une séance</h1>

        <?php if (!empty($success)): ?>
            <div class="mb-4 bg-green-100 text-green-700 px-4 py-3 rounded-lg">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <!-- Filtres -->
        <form method="GET" class="flex gap-4 mb-6 flex-wrap">
            <select name="discipline" class="p-2 border rounded">
                <option value="">Toutes les disciplines</option>
                <?php foreach ($disciplines as $d): ?>
                    <option value="<?= $d['nom'] ?>" <?= ($filter_discipline == $d['nom']) ? 'selected' : '' ?>><?= htmlspecialchars($d['nom']) ?></option>
                <?php endforeach; ?>
            </select>

            <input type="date" name="date" value="<?= htmlspecialchars($filter_date) ?>" class="p-2 border rounded">

            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Filtrer</button>
        </form>

        <!-- Liste des créneaux -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if ($disponibilites): ?>
                <?php foreach ($disponibilites as $dispo): ?>
                    <div class="bg-white p-4 rounded-2xl shadow-md">
                        <h2 class="font-bold text-lg mb-2"><?= htmlspecialchars($dispo['nom'] . ' ' . $dispo['prenom']) ?></h2>
                        <p class="text-sm text-gray-500 mb-1">Disciplines : <?= htmlspecialchars($dispo['disciplines']) ?></p>
                        <p class="text-sm text-gray-500 mb-2">Date : <?= date('d/m/Y', strtotime($dispo['date'])) ?></p>
                        <p class="text-sm text-gray-500 mb-4">Heure : <?= date('H:i', strtotime($dispo['heure_debut'])) ?> - <?= date('H:i', strtotime($dispo['heure_fin'])) ?></p>
                        <form method="POST">
                            <input type="hidden" name="id_disponibilite" value="<?= $dispo['id_disponibilite'] ?>">
                            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded">Réserver</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun créneau disponible pour ces filtres.</p>
            <?php endif; ?>
        </div>

    </main>
    </div>
    <script src="https://cdn.tailwindcss.com"></script>
</body>
</html>