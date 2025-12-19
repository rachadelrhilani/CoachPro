<?php
session_start();
require_once "../Connectdb/connect.php";
if (!isset($_SESSION['id_personne']) || $_SESSION['role'] !== 'coach') {
    header("Location: ../auth/login.php");
    exit;
}
$id_personne = $_SESSION['id_personne'];
// Récupérer id_coach
$stmtCoach = $conn->prepare("SELECT id_coach FROM coach WHERE id_personne = ?");
$stmtCoach->bind_param("i", $id_personne);
$stmtCoach->execute();
$id_coach = $stmtCoach->get_result()->fetch_assoc()['id_coach'];

// Ajouter disponibilité
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $date = $_POST['date'];
    $heure_debut = $_POST['heure_debut'];
    $heure_fin = $_POST['heure_fin'];

    $stmt = $conn->prepare("
        INSERT INTO disponibilite (date, heure_debut, heure_fin, statut, id_coach)
        VALUES (?, ?, ?, 'disponible', ?)
    ");
    $stmt->bind_param("sssi", $date, $heure_debut, $heure_fin, $id_coach);
    $stmt->execute();
}

// Supprimer disponibilité
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("
        UPDATE disponibilite
        SET statut = 'indisponible'
        WHERE id_disponibilite = ? AND id_coach = ?
    ");
    $stmt->bind_param("ii", $id, $id_coach);
    $stmt->execute();
}

// Modifier disponibilité
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id = intval($_POST['id_disponibilite']);
    $date = $_POST['date'];
    $heure_debut = $_POST['heure_debut'];
    $heure_fin = $_POST['heure_fin'];

    $stmt = $conn->prepare("
        UPDATE disponibilite
        SET date = ?, heure_debut = ?, heure_fin = ?
        WHERE id_disponibilite = ? AND id_coach = ?
    ");
    $stmt->bind_param("sssii", $date, $heure_debut, $heure_fin, $id, $id_coach);
    $stmt->execute();
}

// Liste des disponibilités
$stmt = $conn->prepare("
    SELECT * FROM disponibilite
    WHERE id_coach = ? AND statut='disponible'
    ORDER BY date ASC, heure_debut ASC
");
$stmt->bind_param("i", $id_coach);
$stmt->execute();
$disponibilites = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disponibilites</title>
</head>

<body>
    <div class="min-h-screen bg-slate-50 flex">
        <?php include '../Components/aside_coach.php'; ?>
        <main class="flex-1 lg:ml-72 p-4 md:p-10 pb-24 lg:pb-10 transition-all">
            <h1 class="text-3xl font-bold mb-6">Mes disponibilités</h1>

            <div class="bg-white p-6 rounded-2xl shadow-md mb-8">
                <h2 class="text-xl font-bold mb-4">Ajouter un créneau</h2>

                <form method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input type="date" name="date" required class="p-2 border rounded">
                    <input type="time" name="heure_debut" required class="p-2 border rounded">
                    <input type="time" name="heure_fin" required class="p-2 border rounded">

                    <button name="add"
                        class="bg-indigo-600 text-white rounded px-4 py-2 hover:bg-indigo-700">
                        Ajouter
                    </button>
                </form>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-md overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-slate-500 text-sm uppercase tracking-wider">
                            <th class="border-b p-3">Date</th>
                            <th class="border-b p-3">Heure</th>
                            <th class="border-b p-3">Statut</th>
                            <th class="border-b p-3 text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($disponibilites)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-6 text-gray-400 italic">
                                    Aucun créneau disponible
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($disponibilites as $d): ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <form method="POST">
                                        <td class="border-b p-3">
                                            <input type="date" name="date"
                                                value="<?= $d['date'] ?>"
                                                class="p-1.5 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                                        </td>

                                        <td class="border-b p-3 flex items-center gap-2">
                                            <input type="time" name="heure_debut"
                                                value="<?= $d['heure_debut'] ?>"
                                                class="p-1.5 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                                            <span class="text-slate-400 font-bold">→</span>
                                            <input type="time" name="heure_fin"
                                                value="<?= $d['heure_fin'] ?>"
                                                class="p-1.5 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                                        </td>

                                        <td class="border-b p-3">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black uppercase
                        <?= $d['statut'] === 'disponible'
                                    ? 'bg-emerald-100 text-emerald-600'
                                    : 'bg-rose-100 text-rose-600' ?>">
                                                <?= $d['statut'] ?>
                                            </span>
                                        </td>

                                        <td class="border-b p-3 flex justify-center gap-2">
                                            <input type="hidden" name="id_disponibilite"
                                                value="<?= $d['id_disponibilite'] ?>">

                                            
                                            <button name="edit"
                                                class="flex items-center gap-1 bg-amber-400 text-white px-3 py-1.5 rounded-lg text-sm font-bold
                                   hover:bg-amber-500 transition focus:outline-none focus:ring-2 focus:ring-amber-300">
                                                Modifier
                                            </button>

                                            
                                            <a href="?delete=<?= $d['id_disponibilite'] ?>"
                                                onclick="return confirm('Supprimer ce créneau ?')"
                                                class="flex items-center gap-1 bg-rose-500 text-white px-3 py-1.5 rounded-lg text-sm font-bold
                                  hover:bg-rose-600 transition">
                                                Désactiver
                                            </a>
                                        </td>
                                    </form>
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