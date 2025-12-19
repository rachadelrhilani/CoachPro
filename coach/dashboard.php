<?php
session_start();
require_once "../Connectdb/connect.php";
if (!isset($_SESSION['id_personne']) || $_SESSION['role'] !== 'coach') {
  header("Location: ../auth/login.php");
  exit;
}

$id_personne = $_SESSION['id_personne'];
// recuperer id_coach
$stmtCoach = $conn->prepare("
    SELECT id_coach 
    FROM coach 
    WHERE id_personne = ?
");
$stmtCoach->bind_param("i", $id_personne);
$stmtCoach->execute();
$id_coach = $stmtCoach->get_result()->fetch_assoc()['id_coach'];
/* reservation en attente */
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM reservation
    WHERE id_coach = ?
    AND statut = 'en_attente'
");
$stmt->bind_param("i", $id_coach);
$stmt->execute();
$en_attente = $stmt->get_result()->fetch_assoc()['total'];
/* seance d'aujourdhui */
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM reservation r
    JOIN disponibilite d ON r.id_disponibilite = d.id_disponibilite
    WHERE r.id_coach = ?
    AND d.date = CURDATE()
    AND r.statut = 'confirmée'
");
$stmt->bind_param("i", $id_coach);
$stmt->execute();
$aujourd_hui = $stmt->get_result()->fetch_assoc()['total'];
/* seance demain */
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM reservation r
    JOIN disponibilite d ON r.id_disponibilite = d.id_disponibilite
    WHERE r.id_coach = ?
    AND d.date = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
    AND r.statut = 'confirmée' 
");
$stmt->bind_param("i", $id_coach);
$stmt->execute();
$demain = $stmt->get_result()->fetch_assoc()['total'];
/* prochaine seance */
$stmt = $conn->prepare("
    SELECT p.nom, p.prenom, d.date, d.heure_debut, r.statut
    FROM reservation r
    JOIN sportif s ON r.id_sportif = s.id_sportif
    JOIN personne p ON s.id_personne = p.id_personne
    JOIN disponibilite d ON r.id_disponibilite = d.id_disponibilite
    WHERE r.id_coach = ?
    AND r.statut IN ('confirmée', 'en_attente')
    AND d.date >= CURDATE()
    ORDER BY d.date ASC, d.heure_debut ASC
    LIMIT 1
");
$stmt->bind_param("i", $id_coach);
$stmt->execute();
$nextSession = $stmt->get_result()->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Coach</title>
</head>

<body>
  <div class="min-h-screen bg-slate-50 flex">

    <?php include '../Components/aside_coach.php'; ?>

    <main class="flex-1 lg:ml-72 p-4 md:p-10 pb-24 lg:pb-10 transition-all">
      <header class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight italic uppercase">
            Dashboard
          </h1>
          <p class="text-sm text-slate-500 font-medium">
            Bienvenue, nous sommes le <?= date('d F Y') ?>
          </p>
        </div>
      </header>

      <!-- STATISTIQUES -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-10">

        <!-- Réservations en attente -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
          <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-4">
            ⏳
          </div>
          <p class="text-slate-500 text-sm font-bold uppercase tracking-wider">
            Réservations en attente
          </p>
          <p class="text-3xl font-black text-slate-900 mt-1">
            <?= $en_attente ?>
          </p>
        </div>

        <!-- Séances aujourd’hui -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
          <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-4">
            📅
          </div>
          <p class="text-slate-500 text-sm font-bold uppercase tracking-wider">
            Séances aujourd’hui
          </p>
          <p class="text-3xl font-black text-slate-900 mt-1">
            <?= $aujourd_hui ?>
          </p>
        </div>

        <!-- Séances demain -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
          <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4">
            ⏭️
          </div>
          <p class="text-slate-500 text-sm font-bold uppercase tracking-wider">
            Séances demain
          </p>
          <p class="text-3xl font-black text-slate-900 mt-1">
            <?= $demain ?>
          </p>
        </div>

      </div>

      <!-- PROCHAINE SÉANCE -->
      <div class="bg-white rounded-[2.5rem] border border-slate-100 p-6 md:p-10">
        <h2 class="text-xl font-bold mb-6 italic">Prochaine séance</h2>

        <?php if ($nextSession): ?>
          <div class="flex items-center gap-4 p-4 hover:bg-slate-50 rounded-2xl transition-all">

            <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center font-bold text-slate-700">
              <?= strtoupper(substr($nextSession['prenom'], 0, 1)) ?>
            </div>

            <div class="flex-1">
              <h4 class="font-bold text-slate-800">
                <?= htmlspecialchars($nextSession['prenom'] . ' ' . $nextSession['nom']) ?>
              </h4>
              <p class="text-xs text-slate-500">
                <?= date('d/m/Y', strtotime($nextSession['date'])) ?> à
                <?= date('H:i', strtotime($nextSession['heure_debut'])) ?>
              </p>
            </div>

            <span class="px-3 py-1 text-[10px] font-black uppercase rounded-full
        <?= $nextSession['statut'] === 'en_attente'
            ? 'bg-amber-50 text-amber-600'
            : 'bg-emerald-50 text-emerald-600' ?>">
              <?= htmlspecialchars($nextSession['statut']) ?>
            </span>

          </div>
        <?php else: ?>
          <p class="text-sm text-slate-500">Aucune séance prévue.</p>
        <?php endif; ?>
      </div>


    </main>
  </div>
  <script src="https://cdn.tailwindcss.com"></script>
</body>

</html>