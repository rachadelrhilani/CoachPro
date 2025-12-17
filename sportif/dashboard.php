<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
 <!-- sidebar-sportif.php -->
<div class="flex min-h-screen bg-slate-50">
    <aside class="w-72 bg-white border-r border-slate-200 flex flex-col fixed h-full">
        <div class="p-8 flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-200">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <span class="text-xl font-bold text-slate-800 tracking-tight">FitPlayer</span>
        </div>

        <nav class="flex-1 px-4 space-y-2">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4 mb-4">Ma progression</p>
            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 bg-indigo-50 text-indigo-600 rounded-2xl font-bold transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Vue d'ensemble
            </a>
            <a href="reserver.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-indigo-600 rounded-2xl transition-all group">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Trouver un coach
            </a>
            <a href="reservations.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-indigo-600 rounded-2xl transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Mes rendez-vous
            </a>
            <a href="profil.php" class="flex items-center gap-3 px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-indigo-600 rounded-2xl transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Mon profil
            </a>
        </nav>

        <div class="p-6">
            <div class="bg-gradient-to-br from-indigo-600 to-violet-700 rounded-3xl p-5 text-white shadow-xl shadow-indigo-100 relative overflow-hidden">
                <p class="text-xs opacity-80 mb-1">Total Séances</p>
                <p class="text-2xl font-black">24h</p>
                <div class="mt-4 h-1 bg-white/20 rounded-full overflow-hidden">
                    <div class="h-full bg-white w-2/3"></div>
                </div>
            </div>
        </div>
    </aside>

    <main class="ml-72 flex-1 p-10">
        <!-- Contenu Sportif -->
    </main>
</div>
<script src="https://cdn.tailwindcss.com"></script>
</body>
</html>