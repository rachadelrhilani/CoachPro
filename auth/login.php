<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
</head>
<body>
    <div class="min-h-screen bg-slate-50 flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8">
        <h2 class="text-3xl font-bold text-slate-800 mb-6 text-center">Connexion</h2>
        <form class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700">Email</label>
                <input type="email" class="w-full mt-1 p-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Mot de passe</label>
                <input type="password" class="w-full mt-1 p-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <button class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">Se connecter</button>
        </form>
        <p class="mt-6 text-center text-slate-600">Nouveau ici ? <a href="register.php" class="text-indigo-600 font-medium">Créer un compte</a></p>
    </div>
</div>
    <script src="https://cdn.tailwindcss.com"></script>
</body>
</html>