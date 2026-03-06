<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pannello Amministratore CRUD</title>
    <link href="/css/output.css" rel="stylesheet"> 
</head>
<body class="bg-slate-100 text-slate-800 font-sans flex h-screen overflow-hidden selection:bg-blue-200">
    
    <aside class="w-80 bg-slate-900 text-slate-300 flex flex-col h-full shadow-2xl z-20 shrink-0">
        <div class="p-6 bg-slate-950 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded bg-indigo-600 flex items-center justify-center text-white font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white tracking-wide">Area Admin</h1>
                    <p class="text-xs text-slate-500 uppercase tracking-widest mt-0.5">Gestione Globale</p>
                </div>
            </div>
        </div>
        
        <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            <a href="/1" class="group flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium transition-all duration-200 hover:bg-slate-800 hover:text-white mb-4 border border-slate-700">
                <svg class="w-5 h-5 text-slate-400 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Torna all'Explorer Dati
            </a>
            <a href="#pezzi" class="block px-3 py-2 text-sm hover:bg-slate-800 rounded">1. Gestione Pezzi</a>
            <a href="#fornitori" class="block px-3 py-2 text-sm hover:bg-slate-800 rounded">2. Gestione Fornitori</a>
            <a href="#catalogo" class="block px-3 py-2 text-sm hover:bg-slate-800 rounded">3. Gestione Catalogo</a>
        </div>
        <div class="p-4 border-t border-slate-800">
            <a href="/logout" class="flex justify-center items-center gap-2 w-full bg-rose-900/50 hover:bg-rose-800 text-rose-300 py-2 rounded transition-colors text-sm font-bold">Esci</a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50/50 scroll-smooth">
        <header class="bg-white border-b border-gray-200 px-8 py-6 shadow-sm z-10 shrink-0">
            <h2 class="text-2xl font-extrabold text-slate-800">Operazioni Database (CRUD)</h2>
            <p class="text-sm text-slate-500 mt-1">Aggiungi, modifica o rimuovi record dal database.</p>
        </header>

        <div class="flex-1 overflow-auto p-8 custom-scrollbar">
            
            <?php if (isset($message)): ?>
                <div class="mb-8 p-4 rounded-lg border flex items-start gap-3 shadow-sm <?= $status === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800' ?>">
                    <div class="mt-0.5 font-bold"> <?= $status === 'success' ? '✓' : '✗' ?> </div>
                    <div>
                        <h4 class="font-bold text-sm"><?= $status === 'success' ? 'Operazione completata' : 'Errore' ?></h4>
                        <p class="text-sm opacity-90"><?= htmlspecialchars($message) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <div id="pezzi" class="mb-12">
                <h3 class="text-xl font-bold border-b border-slate-300 pb-2 text-slate-700 mb-6">1. Gestione Pezzi</h3>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="bg-slate-800 p-4 border-b border-slate-200"><h3 class="font-bold text-white">Crea Pezzo</h3></div>
                        <form method="POST" class="p-5 space-y-4">
                            <input type="hidden" name="action" value="add_pezzo">
                            <div><label class="text-xs font-bold text-slate-500 uppercase block">ID (Es. P1)</label><input type="text" name="pid" pattern="^P[0-9]+$" title="Deve iniziare con P seguito da numeri" required placeholder="P1" class="w-full border border-slate-300 rounded p-2 mt-1"></div>
                            <div><label class="text-xs font-bold text-slate-500 uppercase block">Nome</label><input type="text" name="pnome" required class="w-full border border-slate-300 rounded p-2 mt-1"></div>
                            <div><label class="text-xs font-bold text-slate-500 uppercase block">Colore</label><input type="text" name="colore" class="w-full border border-slate-300 rounded p-2 mt-1"></div>
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded font-semibold text-sm">Crea</button>
                        </form>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="bg-indigo-600 p-4 border-b border-slate-200"><h3 class="font-bold text-white">Modifica Pezzo</h3></div>
                        <form method="POST" class="p-5 space-y-4">
                            <input type="hidden" name="action" value="edit_pezzo">
                            <div>
                                <label class="text-xs font-bold text-slate-500 uppercase block">Seleziona Pezzo</label>
                                <select name="pid" required class="w-full border border-slate-300 rounded p-2 mt-1 bg-white">
                                    <option value="" disabled selected>-- Seleziona --</option>
                                    <?php foreach($listaPezzi as $p): ?>
                                        <option value="<?= htmlspecialchars($p['pid']) ?>"><?= htmlspecialchars($p['pid'] . ' - ' . $p['pnome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div><label class="text-xs font-bold text-slate-500 uppercase block">Nuovo Nome</label><input type="text" name="pnome" class="w-full border border-slate-300 rounded p-2 mt-1"></div>
                            <div><label class="text-xs font-bold text-slate-500 uppercase block">Nuovo Colore</label><input type="text" name="colore" class="w-full border border-slate-300 rounded p-2 mt-1"></div>
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded font-semibold text-sm">Aggiorna</button>
                        </form>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                        <div class="bg-rose-900 p-4 border-b border-slate-200"><h3 class="font-bold text-white">Elimina Pezzo</h3></div>
                        <form method="POST" class="p-5 space-y-4 flex flex-col flex-1">
                            <input type="hidden" name="action" value="delete_pezzo">
                            <div>
                                <label class="text-xs font-bold text-slate-500 uppercase block">Seleziona Pezzo</label>
                                <select name="pid" required class="w-full border border-slate-300 rounded p-2 mt-1 bg-white">
                                    <option value="" disabled selected>-- Seleziona --</option>
                                    <?php foreach($listaPezzi as $p): ?>
                                        <option value="<?= htmlspecialchars($p['pid']) ?>"><?= htmlspecialchars($p['pid'] . ' - ' . $p['pnome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mt-auto pt-4">
                                <button type="submit" onclick="return confirm('Sicuro?')" class="w-full bg-rose-600 hover:bg-rose-700 text-white py-2 rounded font-semibold text-sm">Elimina</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="fornitori" class="mb-12">
                <h3 class="text-xl font-bold border-b border-slate-300 pb-2 text-slate-700 mb-6">2. Gestione Fornitori</h3>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="bg-slate-800 p-4 border-b border-slate-200"><h3 class="font-bold text-white">Crea Fornitore</h3></div>
                        <form method="POST" class="p-5 space-y-4">
                            <input type="hidden" name="action" value="add_fornitore">
                            <div><label class="text-xs font-bold text-slate-500 uppercase block">ID (Es. F1)</label><input type="text" name="fid" pattern="^F[0-9]+$" title="Deve iniziare con F seguito da numeri" required placeholder="F1" class="w-full border border-slate-300 rounded p-2 mt-1"></div>
                            <div><label class="text-xs font-bold text-slate-500 uppercase block">Nome Azienda</label><input type="text" name="fnome" required class="w-full border border-slate-300 rounded p-2 mt-1"></div>
                            <div><label class="text-xs font-bold text-slate-500 uppercase block">Indirizzo</label><input type="text" name="indirizzo" class="w-full border border-slate-300 rounded p-2 mt-1"></div>
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded font-semibold text-sm">Crea</button>
                        </form>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="bg-indigo-600 p-4 border-b border-slate-200"><h3 class="font-bold text-white">Modifica Fornitore</h3></div>
                        <form method="POST" class="p-5 space-y-4">
                            <input type="hidden" name="action" value="edit_fornitore">
                            <div>
                                <label class="text-xs font-bold text-slate-500 uppercase block">Seleziona Fornitore</label>
                                <select name="fid" required class="w-full border border-slate-300 rounded p-2 mt-1 bg-white">
                                    <option value="" disabled selected>-- Seleziona --</option>
                                    <?php foreach($listaFornitori as $f): ?>
                                        <option value="<?= htmlspecialchars($f['fid']) ?>"><?= htmlspecialchars($f['fid'] . ' - ' . $f['fnome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div><label class="text-xs font-bold text-slate-500 uppercase block">Nuovo Nome</label><input type="text" name="fnome" class="w-full border border-slate-300 rounded p-2 mt-1"></div>
                            <div><label class="text-xs font-bold text-slate-500 uppercase block">Nuovo Indirizzo</label><input type="text" name="indirizzo" class="w-full border border-slate-300 rounded p-2 mt-1"></div>
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded font-semibold text-sm">Aggiorna</button>
                        </form>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                        <div class="bg-rose-900 p-4 border-b border-slate-200"><h3 class="font-bold text-white">Elimina Fornitore</h3></div>
                        <form method="POST" class="p-5 space-y-4 flex flex-col flex-1">
                            <input type="hidden" name="action" value="delete_fornitore">
                            <div>
                                <label class="text-xs font-bold text-slate-500 uppercase block">Seleziona Fornitore</label>
                                <select name="fid" required class="w-full border border-slate-300 rounded p-2 mt-1 bg-white">
                                    <option value="" disabled selected>-- Seleziona --</option>
                                    <?php foreach($listaFornitori as $f): ?>
                                        <option value="<?= htmlspecialchars($f['fid']) ?>"><?= htmlspecialchars($f['fid'] . ' - ' . $f['fnome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mt-auto pt-4">
                                <button type="submit" onclick="return confirm('Sicuro?')" class="w-full bg-rose-600 hover:bg-rose-700 text-white py-2 rounded font-semibold text-sm">Elimina</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="catalogo" class="mb-12">
                <h3 class="text-xl font-bold border-b border-slate-300 pb-2 text-slate-700 mb-6">3. Gestione Catalogo (Associazioni)</h3>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="bg-slate-800 p-4 border-b border-slate-200"><h3 class="font-bold text-white">Aggiungi a Catalogo</h3></div>
                        <form method="POST" class="p-5 space-y-4">
                            <input type="hidden" name="action" value="add_catalogo">
                            <div>
                                <label class="text-xs font-bold text-slate-500 uppercase block">Fornitore</label>
                                <select name="fid" required class="w-full border border-slate-300 rounded p-2 mt-1 bg-white">
                                    <option value="" disabled selected>-- Seleziona Fornitore --</option>
                                    <?php foreach($listaFornitori as $f): ?>
                                        <option value="<?= htmlspecialchars($f['fid']) ?>"><?= htmlspecialchars($f['fid'] . ' - ' . $f['fnome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-500 uppercase block">Pezzo</label>
                                <select name="pid" required class="w-full border border-slate-300 rounded p-2 mt-1 bg-white">
                                    <option value="" disabled selected>-- Seleziona Pezzo --</option>
                                    <?php foreach($listaPezzi as $p): ?>
                                        <option value="<?= htmlspecialchars($p['pid']) ?>"><?= htmlspecialchars($p['pid'] . ' - ' . $p['pnome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div><label class="text-xs font-bold text-slate-500 uppercase block">Costo (€)</label><input type="number" step="0.01" name="costo" required class="w-full border border-slate-300 rounded p-2 mt-1"></div>
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded font-semibold text-sm">Associa</button>
                        </form>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="bg-indigo-600 p-4 border-b border-slate-200"><h3 class="font-bold text-white">Modifica Costo</h3></div>
                        <form method="POST" class="p-5 space-y-4">
                            <input type="hidden" name="action" value="edit_catalogo">
                            <div>
                                <label class="text-xs font-bold text-slate-500 uppercase block">Fornitore</label>
                                <select name="fid" id="edit_catalogo_fid" required class="w-full border border-slate-300 rounded p-2 mt-1 bg-white">
                                    <option value="" disabled selected>-- Seleziona Fornitore --</option>
                                    <?php foreach($listaFornitori as $f): ?>
                                        <option value="<?= htmlspecialchars($f['fid']) ?>"><?= htmlspecialchars($f['fid'] . ' - ' . $f['fnome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-500 uppercase block">Pezzo Fornito</label>
                                <select name="pid" id="edit_catalogo_pid" required class="w-full border border-slate-300 rounded p-2 mt-1 bg-slate-50 disabled:opacity-50">
                                    <option value="" disabled selected>-- Prima seleziona un Fornitore --</option>
                                </select>
                            </div>
                            <div><label class="text-xs font-bold text-slate-500 uppercase block">Nuovo Costo (€)</label><input type="number" step="0.01" name="costo" required class="w-full border border-slate-300 rounded p-2 mt-1"></div>
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded font-semibold text-sm">Aggiorna Costo</button>
                        </form>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                        <div class="bg-rose-900 p-4 border-b border-slate-200"><h3 class="font-bold text-white">Rimuovi Associazione</h3></div>
                        <form method="POST" class="p-5 space-y-4 flex flex-col flex-1">
                            <input type="hidden" name="action" value="delete_catalogo">
                            <div>
                                <label class="text-xs font-bold text-slate-500 uppercase block">Fornitore</label>
                                <select name="fid" id="del_catalogo_fid" required class="w-full border border-slate-300 rounded p-2 mt-1 bg-white">
                                    <option value="" disabled selected>-- Seleziona Fornitore --</option>
                                    <?php foreach($listaFornitori as $f): ?>
                                        <option value="<?= htmlspecialchars($f['fid']) ?>"><?= htmlspecialchars($f['fid'] . ' - ' . $f['fnome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-slate-500 uppercase block">Pezzo Fornito</label>
                                <select name="pid" id="del_catalogo_pid" required class="w-full border border-slate-300 rounded p-2 mt-1 bg-slate-50 disabled:opacity-50">
                                    <option value="" disabled selected>-- Prima seleziona un Fornitore --</option>
                                </select>
                            </div>
                            <div class="mt-auto pt-4">
                                <button type="submit" onclick="return confirm('Sicuro?')" class="w-full bg-rose-600 hover:bg-rose-700 text-white py-2 rounded font-semibold text-sm">Rimuovi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const listaCatalogo = <?= json_encode($listaCatalogo ?? []) ?>;
        const listaPezzi = <?= json_encode($listaPezzi ?? []) ?>;

        function collegaDropdownFornitorePezzo(fidElementId, pidElementId) {
            const fidSelect = document.getElementById(fidElementId);
            const pidSelect = document.getElementById(pidElementId);

            if (!fidSelect || !pidSelect) return;

            fidSelect.addEventListener('change', function() {
                const selectedFid = this.value;
                
                // Resetta la tendina dei pezzi
                pidSelect.innerHTML = '<option value="" disabled selected>-- Seleziona Pezzo --</option>';
                pidSelect.classList.remove('bg-slate-50', 'disabled:opacity-50');
                pidSelect.classList.add('bg-white');

                // Trova tutti gli ID dei pezzi forniti dal fornitore selezionato
                const pezziFornitiIds = listaCatalogo
                    .filter(item => item.fid === selectedFid)
                    .map(item => item.pid);

                let pezziAggiunti = 0;

                // Aggiungi le nuove <option> solo se il pezzo è nella lista dei forniti
                listaPezzi.forEach(pezzo => {
                    if (pezziFornitiIds.includes(pezzo.pid)) {
                        const opt = document.createElement('option');
                        opt.value = pezzo.pid;
                        opt.textContent = pezzo.pid + ' - ' + pezzo.pnome;
                        pidSelect.appendChild(opt);
                        pezziAggiunti++;
                    }
                });

                // Se questo fornitore non fornisce nessun pezzo
                if (pezziAggiunti === 0) {
                    pidSelect.innerHTML = '<option value="" disabled selected>Nessun pezzo nel catalogo</option>';
                    pidSelect.classList.add('bg-rose-50', 'text-rose-600');
                } else {
                    pidSelect.classList.remove('bg-rose-50', 'text-rose-600');
                }
            });
        }

        // Applica il filtro ai due form specifici
        collegaDropdownFornitorePezzo('edit_catalogo_fid', 'edit_catalogo_pid');
        collegaDropdownFornitorePezzo('del_catalogo_fid', 'del_catalogo_pid');
    });
    </script>
</body>
</html>