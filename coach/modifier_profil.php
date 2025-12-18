<?php
session_start();
require_once "../Connectdb/connect.php";

if (!isset($_SESSION['id_personne']) || $_SESSION['role'] !== 'coach') {
    header("Location: ../auth/login.php");
    exit;
}

$id_personne = $_SESSION['id_personne'];


$stmtCoach = $conn->prepare("
    SELECT id_coach, photo, biographie, annees_experience
    FROM coach WHERE id_personne = ?
");
$stmtCoach->bind_param("i", $id_personne);
$stmtCoach->execute();
$coach = $stmtCoach->get_result()->fetch_assoc();

if (!$coach) die("Coach introuvable");

$id_coach = $coach['id_coach'];


$disciplines = $conn->query("SELECT * FROM discipline")->fetch_all(MYSQLI_ASSOC);
$certifications = $conn->query("SELECT * FROM certification")->fetch_all(MYSQLI_ASSOC);

$discCoach = $conn->query("SELECT id_discipline FROM coach_discipline WHERE id_coach=$id_coach")
    ->fetch_all(MYSQLI_ASSOC);
$discCoachIds = array_column($discCoach, 'id_discipline');

$certCoach = $conn->query("SELECT id_certification FROM coach_certification WHERE id_coach=$id_coach")
    ->fetch_all(MYSQLI_ASSOC);
$certCoachIds = array_column($certCoach, 'id_certification');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $bio = $_POST['biographie'];
    $experience = (int)$_POST['annees_experience'];
    $newPhoto = $coach['photo'];

    $newPhoto = trim($_POST['photo_url']);

    if (empty($newPhoto)) {
        $newPhoto = $coach['photo']; // garder l’ancienne
    }

    $conn->begin_transaction();

    try {
        /* mise jour de coach */
        $stmt = $conn->prepare("
            UPDATE coach SET photo=?, biographie=?, annees_experience=?
            WHERE id_coach=?
        ");
        $stmt->bind_param("ssii", $newPhoto, $bio, $experience, $id_coach);
        $stmt->execute();

        /* disciplines */
        $conn->query("DELETE FROM coach_discipline WHERE id_coach=$id_coach");
        if (!empty($_POST['disciplines'])) {
            $stmtDisc = $conn->prepare("
                INSERT INTO coach_discipline (id_coach, id_discipline)
                VALUES (?, ?)
            ");
            foreach ($_POST['disciplines'] as $id_d) {
                $stmtDisc->bind_param("ii", $id_coach, $id_d);
                $stmtDisc->execute();
            }
        }

        /* certifications */
        $conn->query("DELETE FROM coach_certification WHERE id_coach=$id_coach");
        if (!empty($_POST['certifications'])) {
            $stmtCert = $conn->prepare("
                INSERT INTO coach_certification (id_coach, id_certification)
                VALUES (?, ?)
            ");
            foreach ($_POST['certifications'] as $id_c) {
                $stmtCert->bind_param("ii", $id_coach, $id_c);
                $stmtCert->execute();
            }
        }

        $conn->commit();
        header("Location: profil.php");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        echo "Erreur : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier Profil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>

    <div class="min-h-screen bg-slate-50 flex">
        <?php include '../Components/aside_coach.php'; ?>

        <main class="flex-1 lg:ml-72 p-6 md:p-10">

            <h1 class="text-3xl font-black mb-6">Modifier mon profil</h1>

            <form method="POST" enctype="multipart/form-data"
                class="bg-white p-8 rounded-3xl shadow-sm space-y-6 max-w-3xl">

                <div>
                    <label class="font-bold">Photo de profil (URL)</label>

                    <input type="url" name="photo_url"
                        value="<?= htmlspecialchars($coach['photo']) ?>"
                        placeholder="https://example.com/photo.jpg"
                        oninput="preview.src=this.value"
                        class="w-full border rounded-xl p-3 mt-2">

                    <div class="mt-4">
                        <img id="preview"
                            src="<?= htmlspecialchars($coach['photo']) ?>"
                            class="w-32 h-32 rounded-full object-cover border">
                    </div>
                </div>


                <div>
                    <label class="font-bold">Biographie</label>
                    <textarea name="biographie" rows="4"
                        class="w-full border rounded-xl p-3"><?= htmlspecialchars($coach['biographie']) ?></textarea>
                </div>

                <div>
                    <label class="font-bold">Années d’expérience</label>
                    <input type="number" name="annees_experience"
                        value="<?= $coach['annees_experience'] ?>"
                        class="w-full border rounded-xl p-2">
                </div>

                <div>
                    <label class="font-bold">Disciplines</label>
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        <?php foreach ($disciplines as $d): ?>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="disciplines[]"
                                    value="<?= $d['id_discipline'] ?>"
                                    <?= in_array($d['id_discipline'], $discCoachIds) ? 'checked' : '' ?>>
                                <?= $d['nom'] ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div>
                    <label class="font-bold">Certifications</label>
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        <?php foreach ($certifications as $c): ?>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="certifications[]"
                                    value="<?= $c['id_certification'] ?>"
                                    <?= in_array($c['id_certification'], $certCoachIds) ? 'checked' : '' ?>>
                                <?= $c['nom'] ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-indigo-700">
                    Enregistrer
                </button>

            </form>

        </main>
    </div>

</body>

</html>