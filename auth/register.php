<?php
require_once "../Connectdb/connect.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    /* =====================
       1. Données formulaire
    ====================== */
    $nom       = $_POST['nom'];
    $prenom    = $_POST['prenom'];
    $email     = $_POST['email'];
    $telephone = $_POST['telephone'] ?? null;
    $role      = $_POST['role'];
    $password  = $_POST['password'];

    /* =====================
       2. Hash mot de passe
    ====================== */
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    /* =====================
       3. Récupérer id_role
    ====================== */
    $stmtRole = $conn->prepare(
        "SELECT id_role FROM role WHERE nom_role = ?"
    );
    $stmtRole->bind_param("s", $role);
    $stmtRole->execute();
    $resultRole = $stmtRole->get_result();

    if ($resultRole->num_rows === 0) {
        die("Rôle invalide");
    }

    $id_role = $resultRole->fetch_assoc()['id_role'];

    /* =====================
       4. Insertion personne
    ====================== */
    $stmtPersonne = $conn->prepare(
        "INSERT INTO personne 
        (nom, prenom, email, mot_de_passe, telephone, id_role)
        VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmtPersonne->bind_param(
        "sssssi",
        $nom,
        $prenom,
        $email,
        $password_hash,
        $telephone,
        $id_role
    );
    $stmtPersonne->execute();

    $id_personne = $stmtPersonne->insert_id;

    /* =====================
       5. Selon le rôle
    ====================== */

    // SPORTIF
    if ($role === "sportif") {

        $stmtSportif = $conn->prepare(
            "INSERT INTO sportif (id_personne, date_inscription)
             VALUES (?, CURDATE())"
        );
        $stmtSportif->bind_param("i", $id_personne);
        $stmtSportif->execute();
    }

    // COACH
    if ($role === "coach") {

        $photo      = $_POST['photo'] ?? null;
        $bio        = $_POST['biographie'] ?? null;
        $experience = $_POST['annees_experience'] ?? 0;
        $statut     = $_POST['statut'] ?? null;

        $stmtCoach = $conn->prepare(
            "INSERT INTO coach 
            (id_personne, photo, biographie, annees_experience, statut)
            VALUES (?, ?, ?, ?, ?)"
        );
        $stmtCoach->bind_param(
            "issis",
            $id_personne,
            $photo,
            $bio,
            $experience,
            $statut
        );
        $stmtCoach->execute();

        $id_coach = $stmtCoach->insert_id;

        // DISCIPLINES
        if (!empty($_POST['disciplines'])) {
            $stmtCD = $conn->prepare(
                "INSERT INTO coach_discipline (id_coach, id_discipline)
                 VALUES (?, ?)"
            );
            foreach ($_POST['disciplines'] as $id_discipline) {
                $stmtCD->bind_param("ii", $id_coach, $id_discipline);
                $stmtCD->execute();
            }
        }

        // CERTIFICATIONS
        if (!empty($_POST['certifications'])) {
            $stmtCC = $conn->prepare(
                "INSERT INTO coach_certification (id_coach, id_certification)
                 VALUES (?, ?)"
            );
            foreach ($_POST['certifications'] as $id_certification) {
                $stmtCC->bind_param("ii", $id_coach, $id_certification);
                $stmtCC->execute();
            }
        }
    }

    /* =====================
       6. Redirection
    ====================== */
    header("Location: login.php?success=1");
    exit;
}
 ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription | CoachPro</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen relative flex items-center justify-center p-6 overflow-hidden">


 <div 
    class="absolute inset-0 bg-cover bg-center scale-110 blur-sm"
    style="background-image: url('../images/sportback.jpg');">
  </div>
<div class="absolute inset-0 bg-black/60"></div>


<div class="relative z-10 w-full max-w-3xl max-h-[90vh] overflow-y-auto overflow-x-hidden">
  <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-2xl p-8">
  <h1 class="text-3xl font-black text-slate-800 mb-2">Créer un compte</h1>
  <p class="text-slate-500 mb-8">Rejoignez la plateforme CoachPro</p>

  <form action="register.php" method="POST" class="space-y-6">

    
    <div>
      <label class="block text-sm font-bold text-slate-600 mb-2">Je suis</label>
      <div class="flex gap-4">
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="radio" name="role" value="sportif" checked class="role-radio">
          <span class="font-medium">Sportif</span>
        </label>

        <label class="flex items-center gap-2 cursor-pointer">
          <input type="radio" name="role" value="coach" class="role-radio">
          <span class="font-medium">Coach</span>
        </label>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <input type="text" name="nom" placeholder="Nom" required class="input">
      <input type="text" name="prenom" placeholder="Prénom" required class="input">
      <input type="email" name="email" placeholder="Email" required class="input">
      <input type="text" name="telephone" placeholder="Téléphone" class="input">
      <input type="password" name="password" placeholder="Mot de passe" required class="input md:col-span-2">
    </div>

    <div id="sportifFields" class="hidden">
      <input type="hidden" name="date_inscription" value="<?= date('Y-m-d') ?>">
      <p class="text-sm text-slate-500">Inscription en tant que sportif.</p>
    </div>

    <div id="coachFields" class="hidden space-y-4">

      <input type="text" name="photo" placeholder="URL de la photo" class="input">
      
      <textarea name="biographie" rows="3" placeholder="Biographie" class="input"></textarea>

      <input type="number" name="annees_experience" min="0" placeholder="Années d'expérience" class="input">

      <input type="text" name="statut" placeholder="Statut (Freelance, Certifié...)" class="input">

      <!-- DISCIPLINES -->
      <div>
        <p class="font-bold text-sm mb-2">Disciplines</p>
        <div class="flex flex-wrap gap-3">
          <label><input type="checkbox" name="disciplines[]" value="1"> Musculation</label>
          <label><input type="checkbox" name="disciplines[]" value="2"> Cardio</label>
          <label><input type="checkbox" name="disciplines[]" value="3"> Yoga</label>
        </div>
      </div>

      <!-- CERTIFICATIONS -->
      <div>
        <p class="font-bold text-sm mb-2">Certifications</p>
        <div class="flex flex-wrap gap-3">
          <label><input type="checkbox" name="certifications[]" value="1"> CrossFit</label>
          <label><input type="checkbox" name="certifications[]" value="2"> IFBB</label>
        </div>
      </div>
    </div>

    <!-- SUBMIT -->
    <button type="submit"
      class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-bold transition">
      Créer mon compte
    </button>

    <p class="text-center text-sm text-slate-500">
      Déjà un compte ?
      <a href="login.php" class="text-indigo-600 font-bold">Se connecter</a>
    </p>

  </form>
</div>
</div>
<style>
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-thumb {
  background: #c7d2fe;
  border-radius: 10px;
}
</style>
<!-- STYLE INPUT -->
<style>
  .input {
    width: 100%;
    padding: 14px;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    outline: none;
  }
  .input:focus {
    border-color: #6366f1;
  }
</style>


<script>
const roleRadios = document.querySelectorAll('.role-radio');
const coachFields = document.getElementById('coachFields');
const sportifFields = document.getElementById('sportifFields');

function toggleFields() {
  const role = document.querySelector('input[name="role"]:checked').value;
  coachFields.classList.toggle('hidden', role !== 'coach');
  sportifFields.classList.toggle('hidden', role !== 'sportif');
}

roleRadios.forEach(r => r.addEventListener('change', toggleFields));
toggleFields();
</script>

</body>
</html>
