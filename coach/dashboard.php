<?php

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
        Bienvenue, nous sommes le 21 Février 2025
      </p>
    </div>

    <div class="hidden md:flex items-center gap-4">
      <button class="relative p-2 bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-indigo-600 transition-colors">
        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-width="2"
            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11
               a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341
               C7.67 6.165 6 8.388 6 11v3.159
               c0 .538-.214 1.055-.595 1.436L4 17h5m6 0
               v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
      </button>
    </div>
  </header>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-10">

    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
      <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-4">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-width="2"
            d="M8 7V3m8 4V3m-9 8h10M5 21h14
               a2 2 0 002-2V7a2 2 0 00-2-2H5
               a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </div>
      <p class="text-slate-500 text-sm font-bold uppercase tracking-wider">
        Séances prévues
      </p>
      <p class="text-3xl font-black text-slate-900 mt-1">04</p>
    </div>

    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
      <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-width="2"
            d="M9 12l2 2 4-4m6 2
               a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <p class="text-slate-500 text-sm font-bold uppercase tracking-wider">
        Séances complétées
      </p>
      <p class="text-3xl font-black text-slate-900 mt-1">28</p>
    </div>

  </div>

  <div class="bg-white rounded-[2.5rem] border border-slate-100 p-6 md:p-10">
    <h2 class="text-xl font-bold mb-6 italic">Activités récentes</h2>

    <div class="space-y-4">
      <div class="flex items-center gap-4 p-4 hover:bg-slate-50 rounded-2xl transition-all cursor-pointer">
        <div class="w-12 h-12 bg-slate-100 rounded-full overflow-hidden">
          <img src="https://i.pravatar.cc/100" alt="">
        </div>

        <div class="flex-1 min-w-0">
          <h4 class="font-bold text-slate-800 truncate">
            Séance de HIIT
          </h4>
          <p class="text-xs text-slate-500">
            Aujourd’hui à 18:30 • Avec Sportif Ahmed
          </p>
        </div>

        <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-black uppercase rounded-full">
          En attente
        </span>
      </div>
    </div>
  </div>

</main>
</div>
    <script src="https://cdn.tailwindcss.com"></script>
</body>
</html>