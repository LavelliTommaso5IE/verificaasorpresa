<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pannello Amministratore</title>
    <link href="/css/output.css" rel="stylesheet">
</head>
<body class="bg-slate-100 text-slate-800 font-sans flex h-screen overflow-hidden selection:bg-blue-200">
    
    <aside class="w-80 bg-slate-900 text-slate-300 flex flex-col h-full shadow-2xl z-20">
        <div class="p-6 bg-slate-950 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded bg-rose-600 flex items-center justify-center text-white font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white tracking-wide">Area Admin</h1>
                    <p class="text-xs text-slate-500 uppercase tracking-widest mt-0.5">Gestione Sistema</p>
                </div>
            </div>
        </div>
        
        <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1 custom-scrollbar">
            <a href="/1" class="group flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-slate-800 hover:text-white mb-4 border border-slate-700">
                <svg class="w-5 h-5 text-slate-400 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Torna all'Explorer Dati
            </a>
            
            <a href="/logout" class="group flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-bold text-rose-400 hover:bg-rose-950 hover:text-rose-300 mb-8 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Disconnettiti
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50/50">
        <header class="bg-white border-b border-gray-200 px-8 py-6 shadow-sm z-10">
            <h2 class="text-2xl font-extrabold text-slate-800">Operazioni Database</h2>
            <p class="text-sm text-slate-500 mt-1">Aggiungi o rimuovi entità forzatamente a livello globale.</p>
        </header>

        <div class="flex-1 overflow-auto p-8 custom-scrollbar">
            
            <?php if (isset($message)): ?>
                <div class="mb-8 p-4 rounded-lg border flex items-start gap-3 shadow-sm <?= $status === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800' ?>">
                    <div class="mt-0.5">
                        <?php if($status === 'success'): ?>
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <?php else: ?>
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm"><?= $status === 'success' ? 'Operazione completata' : 'Errore' ?></h4>
                        <p class="text-sm opacity-90"><?= htmlspecialchars($message) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div class="col-span-1 lg:col-span-2"><h3 class="text-xl font-bold border-b border-slate-300 pb-2 text-slate-700">1. Gestione Fornitori</h3></div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-slate-800 p-5 border-b border-slate-200"><h3 class="font-bold text-white text-lg">Crea Nuovo Fornitore</h3></div>
                    <form method="POST" action="/admin/gestione" class="p-6 space-y-4">
                        <input type="hidden" name="action" value="add_fornitore">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-1">
                                <label class="text-xs font-bold text-slate-500 mb-1.5 uppercase block">ID (FID)</label>
                                <input type="number" name="fid" required class="w-full border border-slate-300 rounded-md px-3 py-2 bg-slate-50 focus:bg-white">
                            </div>
                            <div class="col-span-2">
                                <label class="text-xs font-bold text-slate-500 mb-1.5 uppercase block">Nome Azienda</label>
                                <input type="text" name="fnome" required class="w-full border border-slate-300 rounded-md px-3 py-2 bg-slate-50 focus:bg-white">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1.5 uppercase block">Indirizzo Sede (Opzionale)</label>
                            <input type="text" name="indirizzo" class="w-full border border-slate-300 rounded-md px-3 py-2 bg-slate-50 focus:bg-white">
                        </div>
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 rounded-lg font-semibold mt-4">Salva Fornitore</button>
                    </form>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-rose-900 p-5 border-b border-slate-200"><h3 class="font-bold text-white text-lg">Elimina Fornitore</h3></div>
                    <form method="POST" action="/admin/gestione" class="p-6 space-y-4 h-full flex flex-col">
                        <input type="hidden" name="action" value="delete_fornitore">
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1.5 uppercase block">ID Fornitore (FID)</label>
                            <input type="number" name="fid" required class="w-full border border-slate-300 rounded-md px-3 py-2 bg-slate-50 focus:bg-white">
                        </div>
                        <p class="text-xs text-rose-600 font-semibold mt-2">ATTENZIONE: Eliminare il fornitore rimuoverà automaticamente tutti i pezzi a lui associati dal catalogo.</p>
                        <div class="mt-auto">
                            <button type="submit" onclick="return confirm('Sei sicuro?')" class="w-full bg-rose-600 hover:bg-rose-700 text-white py-2.5 rounded-lg font-semibold mt-4">Elimina Definitivamente</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="col-span-1 lg:col-span-2"><h3 class="text-xl font-bold border-b border-slate-300 pb-2 text-slate-700">2. Gestione Catalogo</h3></div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-slate-800 p-5 border-b border-slate-200"><h3 class="font-bold text-white text-lg">Aggiungi Pezzo a Catalogo</h3></div>
                    <form method="POST" action="/admin/gestione" class="p-6 space-y-4">
                        <input type="hidden" name="action" value="add">
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="text-xs font-bold text-slate-500 mb-1.5 block">ID Fornitore (FID)</label><input type="number" name="fid" required class="w-full border border-slate-300 rounded-md px-3 py-2 bg-slate-50"></div>
                            <div><label class="text-xs font-bold text-slate-500 mb-1.5 block">ID Pezzo (PID)</label><input type="number" name="pid" required class="w-full border border-slate-300 rounded-md px-3 py-2 bg-slate-50"></div>
                        </div>
                        <div><label class="text-xs font-bold text-slate-500 mb-1.5 block">Costo (€)</label><input type="number" step="0.01" name="costo" required class="w-full border border-slate-300 rounded-md px-3 py-2 bg-slate-50"></div>
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 rounded-lg font-semibold">Salva in Catalogo</button>
                    </form>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-rose-900 p-5 border-b border-slate-200"><h3 class="font-bold text-white text-lg">Rimuovi Pezzo</h3></div>
                    <form method="POST" action="/admin/gestione" class="p-6 space-y-4 h-full flex flex-col">
                        <input type="hidden" name="action" value="delete">
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="text-xs font-bold text-slate-500 mb-1.5 block">ID Fornitore (FID)</label><input type="number" name="fid" required class="w-full border border-slate-300 rounded-md px-3 py-2 bg-slate-50"></div>
                            <div><label class="text-xs font-bold text-slate-500 mb-1.5 block">ID Pezzo (PID)</label><input type="number" name="pid" required class="w-full border border-slate-300 rounded-md px-3 py-2 bg-slate-50"></div>
                        </div>
                        <div class="mt-auto">
                            <button type="submit" onclick="return confirm('Sei sicuro?')" class="w-full bg-rose-600 hover:bg-rose-700 text-white py-2.5 rounded-lg font-semibold mt-4">Rimuovi da Catalogo</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>
</body>
</html>