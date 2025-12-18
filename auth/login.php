<?php
session_start();
require_once "../Connectdb/connect.php";

$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("
        SELECT 
            p.id_personne,
            p.mot_de_passe,
            r.nom_role
        FROM personne p
        JOIN role r ON p.id_role = r.id_role
        WHERE p.email = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['mot_de_passe'])) {

            $_SESSION['id_personne'] = $user['id_personne'];
            $_SESSION['role'] = $user['nom_role'];

            if ($user['nom_role'] === 'sportif') {
                header("Location: ../sportif/dashboard.php");
            } elseif ($user['nom_role'] === 'coach') {
                header("Location: ../coach/dashboard.php");
            }
            exit;
        } else {
            $error = "Mot de passe incorrect";
        }
    } else {
        $error = "Email introuvable";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion CoachPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <!-- Container principal centré -->
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden">

        <!-- Image de fond avec overlay sombre -->
        <div class="absolute inset-0">
            <img src="../images/sportback.jpg" alt="Background" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/50"></div>
        </div>

        <!-- Card de login centrée -->
        <div class="relative z-10 w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 md:p-10">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-extrabold text-gray-900 uppercase tracking-wide">CoachPro</h1>
                <p class="mt-2 text-gray-500">Connectez-vous pour accéder à votre espace</p>
            </div>

            <!-- Affichage des erreurs -->
            <?php if ($error): ?>
                <div class="mb-6 bg-red-100 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire -->
            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" required placeholder="exemple@email.com"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                    <input type="password" name="password" required placeholder="********"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-lg shadow-lg transition">
                    Se connecter
                </button>
            </form>

            <p class="mt-6 text-center text-gray-500 text-sm">
                Nouveau ici ? 
                <a href="register.php" class="text-indigo-600 font-medium hover:underline">Créer un compte</a>
            </p>
        </div>
    </div>

</body>

</html>
