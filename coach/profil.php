<?php 
session_start();
require_once "../Connectdb/connect.php";
if (!isset($_SESSION['id_personne']) || $_SESSION['role'] !== 'coach') {
    header("Location: ../auth/login.php");
    exit;
}

$id_personne = $_SESSION['id_personne'];
/*Récupérer infos coach*/
$stmtCoach = $conn->prepare("
    SELECT c.id_coach, c.photo, c.biographie, c.annees_experience,
           p.nom, p.prenom
    FROM coach c
    JOIN personne p ON c.id_personne = p.id_personne
    WHERE c.id_personne = ?
");
$stmtCoach->bind_param("i", $id_personne);
$stmtCoach->execute();
$coach = $stmtCoach->get_result()->fetch_assoc();

if (!$coach) {
    die("Coach introuvable");
}

$id_coach = $coach['id_coach'];

/*Disciplines du coach*/
$stmtDisc = $conn->prepare("
    SELECT d.nom
    FROM coach_discipline cd
    JOIN discipline d ON cd.id_discipline = d.id_discipline
    WHERE cd.id_coach = ?
");
$stmtDisc->bind_param("i", $id_coach);
$stmtDisc->execute();
$disciplines = $stmtDisc->get_result()->fetch_all(MYSQLI_ASSOC);

/* =========================
   Certifications du coach
========================= */
$stmtCert = $conn->prepare("
    SELECT c.nom, c.organisme
    FROM coach_certification cc
    JOIN certification c ON cc.id_certification = c.id_certification
    WHERE cc.id_coach = ?
");
$stmtCert->bind_param("i", $id_coach);
$stmtCert->execute();
$certifications = $stmtCert->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
</head>
<body>
    <div class="min-h-screen bg-slate-50 flex">
    <?php include '../Components/aside_coach.php'; ?>
    <main class="flex-1 lg:ml-72 p-4 md:p-10 pb-24 lg:pb-10 transition-all">
        <h1 class="text-3xl font-black mb-8">Mon Profil</h1>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Photo + Infos -->
        <div class="flex flex-col items-center text-center">
            <img
                src="<?= $coach['photo'] ?: 'https://ui-avatars.com/api/?name=Coach&background=6366f1&color=fff' ?>"
                class="w-40 h-40 rounded-2xl object-cover shadow-md mb-4"
            >

            <h2 class="text-xl font-black text-slate-800">
                <?= htmlspecialchars($coach['nom'].' '.$coach['prenom']) ?>
            </h2>

            <p class="text-indigo-600 font-bold mt-1">COACH</p>

            <a href="modifier_profil.php"
               class="mt-6 inline-block bg-indigo-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-indigo-700 transition">
                Modifier le profil
            </a>
        </div>

        <!-- Détails -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Biographie -->
            <div>
                <h3 class="text-lg font-bold mb-2">Biographie</h3>
                <p class="text-slate-600 leading-relaxed">
                    <?= nl2br(htmlspecialchars($coach['biographie'] ?? 'Aucune biographie renseignée')) ?>
                </p>
            </div>

            <!-- Expérience -->
            <div>
                <h3 class="text-lg font-bold mb-2">Années d’expérience</h3>
                <span class="inline-block px-4 py-2 bg-indigo-50 text-indigo-600 font-black rounded-xl">
                    <?= (int)$coach['annees_experience'] ?> ans
                </span>
            </div>

            <!-- Disciplines -->
            <div>
                <h3 class="text-lg font-bold mb-2">Disciplines sportives</h3>
                <div class="flex flex-wrap gap-2">
                    <?php if ($disciplines): ?>
                        <?php foreach ($disciplines as $d): ?>
                            <span class="px-4 py-1 bg-emerald-100 text-emerald-600 rounded-full text-sm font-bold">
                                <?= htmlspecialchars($d['nom']) ?>
                            </span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="text-slate-400 italic">Aucune discipline</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Certifications -->
            <div>
                <h3 class="text-lg font-bold mb-2">Certifications</h3>
                <ul class="space-y-2">
                    <?php if ($certifications): ?>
                        <?php foreach ($certifications as $c): ?>
                            <li class="bg-slate-50 border border-slate-200 rounded-xl p-3">
                                <p class="font-bold text-slate-800"><?= htmlspecialchars($c['nom']) ?></p>
                                <p class="text-xs text-slate-500"><?= htmlspecialchars($c['organisme']) ?></p>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="text-slate-400 italic">Aucune certification</li>
                    <?php endif; ?>
                </ul>
            </div>

        </div>
    </div>

</main>
    </div>
    <script src="https://cdn.tailwindcss.com"></script>
</body>
</html>