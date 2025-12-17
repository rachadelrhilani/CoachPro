<?php
session_start();
require_once "../Connectdb/connect.php";

if (!isset($_SESSION['id_personne']) || $_SESSION['role'] !== 'sportif') {
    header("Location: ../auth/login.php");
    exit;
}

$id_personne = $_SESSION['id_personne'];
$error = $success = "";
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
// Récupérer les infos actuelles
$stmt = $conn->prepare("
    SELECT nom, prenom, email, telephone
    FROM personne 
    WHERE id_personne = ?
");
$stmt->bind_param("i", $id_personne);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['telephone'] ?? '';

    
    if (empty($nom) || empty($prenom) || empty($email)) {
        $error = "Nom, prénom et email sont obligatoires.";
    } else {
        $stmtUpdate = $conn->prepare("
            UPDATE personne
            SET nom = ?, prenom = ?, email = ?, telephone = ?
            WHERE id_personne = ?
        ");
        $stmtUpdate->bind_param("ssssi", $nom, $prenom, $email, $telephone, $id_personne);
        if ($stmtUpdate->execute()) {
            $success = "Profil mis à jour avec succès.";
            $user['nom'] = $nom;
            $user['prenom'] = $prenom;
            $user['email'] = $email;
            $user['telephone'] = $telephone;
        } else {
            $error = "Erreur lors de la mise à jour du profil.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Modifier mon profil</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50">

<div class="flex min-h-screen">
<?php include '../Components/aside_sportif.php'; ?>

<main class="flex-1 lg:ml-72 p-6 md:p-10 pb-24 lg:pb-10 transition-all">
    <h1 class="text-3xl font-bold mb-6">Modifier mon profil</h1>

    <?php if($error): ?>
        <div class="mb-4 bg-red-100 text-red-700 px-4 py-3 rounded-lg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if($success): ?>
        <div class="mb-4 bg-green-100 text-green-700 px-4 py-3 rounded-lg"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" class="bg-white p-6 rounded-2xl shadow-md max-w-lg space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-700">Nom</label>
            <input type="text" name="nom" required value="<?= htmlspecialchars($user['nom']) ?>"
                class="w-full mt-1 p-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Prénom</label>
            <input type="text" name="prenom" required value="<?= htmlspecialchars($user['prenom']) ?>"
                class="w-full mt-1 p-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Email</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>"
                class="w-full mt-1 p-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">Téléphone</label>
            <input type="text" name="telephone" value="<?= htmlspecialchars($user['telephone']) ?>"
                class="w-full mt-1 p-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
        </div>

        <button type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-bold transition">
            Enregistrer les modifications
        </button>
    </form>
</main>
</div>

</body>
</html>
