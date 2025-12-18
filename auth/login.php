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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
</head>

<body style="background-image: url('../images/sportback.jpg');">
    <div class="min-h-screen bg-slate-50 flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-3xl font-bold text-slate-800 mb-6 text-center">Connexion</h2>
            
            <?php if ($error): ?>
                <div class="mb-4 bg-red-100 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Email</label>
                    <input
                        type="email"
                        name="email"
                        required
                        class="w-full mt-1 p-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Mot de passe</label>
                    <input
                        type="password"
                        name="password"
                        required
                        class="w-full mt-1 p-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <button
                    type="submit"
                    class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                    Se connecter
                </button>
            </form>

            <p class="mt-6 text-center text-slate-600">Nouveau ici ? <a href="register.php" class="text-indigo-600 font-medium">Créer un compte</a></p>
        </div>
    </div>
    <script src="https://cdn.tailwindcss.com"></script>
</body>
</html>