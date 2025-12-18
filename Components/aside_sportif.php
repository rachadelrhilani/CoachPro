<div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 px-6 py-3 z-50 flex justify-between items-center">

  <a href="dashboard.php" class="mobile-link flex flex-col items-center text-slate-400">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7"/></svg>
    <span class="text-[10px] mt-1 font-bold">Accueil</span>
  </a>

  <a href="reserver.php" class="mobile-link flex flex-col items-center text-slate-400">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8v4l3 3"/></svg>
    <span class="text-[10px] mt-1 font-bold">Réserver</span>
  </a>

  <a href="reservations.php" class="mobile-link bg-indigo-600 text-white p-3 -mt-10 rounded-full shadow-lg border-4 border-slate-50">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M8 7V3m8 4V3m-9 8h10"/></svg>
  </a>

  <a href="profil.php" class="mobile-link flex flex-col items-center text-slate-400">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M16 7a4 4 0 11-8 0"/></svg>
    <span class="text-[10px] mt-1 font-bold">Profil</span>
  </a>

  <a href="logout.php" class="flex flex-col items-center text-red-400">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 16l4-4m0 0l-4-4"/></svg>
    <span class="text-[10px] mt-1 font-bold">Sortie</span>
  </a>
</div>
<aside class="hidden lg:flex w-72 bg-white border-r border-slate-200 flex-col fixed h-full z-40">

  <div class="p-8">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>
      </div>
      <span class="text-xl font-black text-slate-800 tracking-tighter">SportifHub</span>
    </div>
  </div>

  <nav class="flex-1 px-4 space-y-1">
    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[2px] px-4 py-4">Sportif Panel</p>

    <a href="dashboard.php" class="nav-link flex items-center gap-3 px-4 py-3.5 text-slate-500 hover:bg-slate-50 hover:text-indigo-600 rounded-2xl transition-all font-medium">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7"/></svg>
      Accueil
    </a>

    <a href="reserver.php" class="nav-link flex items-center gap-3 px-4 py-3.5 text-slate-500 hover:bg-slate-50 hover:text-indigo-600 rounded-2xl transition-all font-medium">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8v4l3 3"/></svg>
      Réserver une séance
    </a>

    <a href="reservations.php" class="nav-link flex items-center gap-3 px-4 py-3.5 text-slate-500 hover:bg-slate-50 hover:text-indigo-600 rounded-2xl transition-all font-medium">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M8 7V3m8 4V3m-9 8h10"/></svg>
      Mes réservations
    </a>

    <a href="profil.php" class="nav-link flex items-center gap-3 px-4 py-3.5 text-slate-500 hover:bg-slate-50 hover:text-indigo-600 rounded-2xl transition-all font-medium">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M16 7a4 4 0 11-8 0"/></svg>
      Mon profil
    </a>
  </nav>

  <div class="p-4 mt-auto border-t border-slate-100">
  <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-2xl">
    
    <img src="https://ui-avatars.com/api/?name=Sportif&background=6366f1&color=fff" class="w-10 h-10 rounded-xl">

    <div class="flex-1">
      <p class="text-sm font-bold text-slate-800"><?= htmlspecialchars($fullName) ?></p>
      <p class="text-[10px] uppercase font-bold text-indigo-500"><?= htmlspecialchars($roleName) ?></p>
    </div>

    <!-- Logout -->
    <a href="logout.php" class="text-slate-400 hover:text-red-500 transition" title="Se déconnecter">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
           viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
      </svg>
    </a>

  </div>
</div>

</aside>
<script>
const currentPage = window.location.pathname.split('/').pop();

document.querySelectorAll('.nav-link, .mobile-link').forEach(link => {
  if (link.getAttribute('href') === currentPage) {
    link.classList.add(
      'bg-indigo-50',
      'text-indigo-600',
      'font-bold'
    );
  }
});
</script>
