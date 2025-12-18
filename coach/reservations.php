<?php
session_start();
require_once "../Connectdb/connect.php";
if (!isset($_SESSION['id_personne']) || $_SESSION['role'] !== 'coach') {
    header("Location: ../auth/login.php");
    exit;
}

$id_personne = $_SESSION['id_personne'];
// recuperer id coach d'apres idpersonne
$stmtCoach = $conn->prepare("SELECT id_coach FROM coach WHERE id_personne = ?");
$stmtCoach->bind_param("i", $id_personne);
$stmtCoach->execute();
$id_coach = $stmtCoach->get_result()->fetch_assoc()['id_coach'];

// traitement actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_reservation = intval($_POST['id_reservation']);
    $action = $_POST['action'];

    if ($action === 'accepter') {
        $statut_res = 'confirmée';
        $statut_dispo = 'occupée';
    } else {
        $statut_res = 'refusée';
        $statut_dispo = 'disponible';
    }

    // mettre à jour la réservation
    $stmt = $conn->prepare("
        UPDATE reservation 
        SET statut = ?
        WHERE id_reservation = ?
    ");
    $stmt->bind_param("si", $statut_res, $id_reservation);
    $stmt->execute();

    // mettre à jour la disponibilite lie
    $stmt = $conn->prepare("
        UPDATE disponibilite 
        SET statut = ?
        WHERE id_disponibilite = (
            SELECT id_disponibilite FROM reservation WHERE id_reservation = ?
        )
    ");
    $stmt->bind_param("si", $statut_dispo, $id_reservation);
    $stmt->execute();
}

// récupérer les demandes du coach
$stmt = $conn->prepare("
    SELECT r.id_reservation, r.statut,
           d.date, d.heure_debut, d.heure_fin,
           p.nom, p.prenom
    FROM reservation r
    JOIN sportif s ON r.id_sportif = s.id_sportif
    JOIN personne p ON s.id_personne = p.id_personne
    JOIN disponibilite d ON r.id_disponibilite = d.id_disponibilite
    WHERE r.id_coach = ?
    ORDER BY d.date ASC
");
$stmt->bind_param("i", $id_coach);
$stmt->execute();
$demandes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>listes des demandes</title>
</head>

<body>
    <div class="min-h-screen bg-slate-50 flex">
        <?php include '../Components/aside_coach.php'; ?>
        <main class="flex-1 lg:ml-72 p-4 md:p-10 pb-24 lg:pb-10 transition-all">
            <h1 class="text-3xl font-bold mb-6">Demandes de réservation</h1>

            <div class="bg-white p-6 rounded-2xl shadow-md overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="border-b p-2">Sportif</th>
                            <th class="border-b p-2">Date</th>
                            <th class="border-b p-2">Heure</th>
                            <th class="border-b p-2">Statut</th>
                            <th class="border-b p-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($demandes)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-gray-500">
                                    Aucune demande trouvée
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($demandes as $d): ?>
                                <tr>
                                    <td class="border-b p-2">
                                        <?= htmlspecialchars($d['nom'] . ' ' . $d['prenom']) ?>
                                    </td>
                                    <td class="border-b p-2">
                                        <?= date('d/m/Y', strtotime($d['date'])) ?>
                                    </td>
                                    <td class="border-b p-2">
                                        <?= date('H:i', strtotime($d['heure_debut'])) ?> -
                                        <?= date('H:i', strtotime($d['heure_fin'])) ?>
                                    </td>
                                    <td class="border-b p-2">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold
                <?= $d['statut'] === 'en_attente'
                                    ? 'bg-amber-100 text-amber-600'
                                    : ($d['statut'] === 'confirmée'
                                        ? 'bg-green-100 text-green-600'
                                        : 'bg-red-100 text-red-600') ?>">
                                            <?= htmlspecialchars($d['statut']) ?>
                                        </span>
                                    </td>
                                    <td class="border-b p-2 flex gap-2">
                                        <?php if ($d['statut'] === 'en_attente'): ?>
                                            <form method="POST">
                                                <input type="hidden" name="id_reservation" value="<?= $d['id_reservation'] ?>">
                                                <button name="action" value="accepter"
                                                    class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">
                                                    Accepter
                                                </button>
                                            </form>
                                            <form method="POST">
                                                <input type="hidden" name="id_reservation" value="<?= $d['id_reservation'] ?>">
                                                <button name="action" value="refuser"
                                                    class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">
                                                    Refuser
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
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