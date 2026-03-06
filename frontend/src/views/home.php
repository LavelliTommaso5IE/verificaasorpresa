<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database API Explorer</title>
    <link href="/css/output.css" rel="stylesheet">
</head>
<body class="bg-slate-100 text-slate-800 font-sans flex h-screen overflow-hidden selection:bg-blue-200">
    
    <aside class="w-80 bg-slate-900 text-slate-300 flex flex-col h-full shadow-2xl z-20">
        <div class="p-6 bg-slate-950 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded bg-blue-500 flex items-center justify-center text-white font-bold">API</div>
                <div>
                    <h1 class="text-lg font-bold text-white tracking-wide">Data Explorer</h1>
                    <p class="text-xs text-slate-500 uppercase tracking-widest mt-0.5">Pannello di controllo</p>
                </div>
            </div>
        </div>
        
        <div class="flex-1 overflow-y-auto py-4 px-3 space-y-1 custom-scrollbar">
            <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3 mt-2">Le tue Query</p>
            
            <?php foreach ($queryInfo as $qId => $info): ?>
                <?php $isActive = ($id == $qId); ?>
                <a href="/<?= $qId ?>" 
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 
                          <?= $isActive ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/50' : 'hover:bg-slate-800 hover:text-white' ?>">
                    
                    <span class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold 
                                 <?= $isActive ? 'bg-white text-blue-600' : 'bg-slate-700 text-slate-400 group-hover:bg-slate-600 group-hover:text-white' ?>">
                        <?= $qId ?>
                    </span>
                    
                    <span class="truncate"><?= htmlspecialchars(preg_replace('/^\d+\.\s*/', '', $info['title'])) ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="p-4 border-t border-slate-800 bg-slate-950 flex flex-col gap-2">
            <a href="/admin/gestione" class="flex items-center justify-center gap-2 w-full bg-slate-800 hover:bg-rose-700 text-slate-300 hover:text-white py-2.5 rounded-lg text-sm font-semibold transition-colors border border-slate-700 hover:border-rose-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Pannello Admin
            </a>
            <a href="/logout" class="flex items-center justify-center gap-2 w-full text-slate-500 hover:text-rose-400 py-2 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Esci
            </a>
        </div>

    </aside>

    <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50/50">
        
        <header class="bg-white border-b border-gray-200 px-8 py-6 shadow-sm z-10">
            <div class="flex justify-between items-end">
                <div>
                    <span class="text-sm font-bold text-blue-600 tracking-wider uppercase mb-1 block">Query Selezionata</span>
                    <h2 class="text-2xl font-extrabold text-slate-800"><?= htmlspecialchars($queryInfo[$id]['title']) ?></h2>
                </div>
            </div>
            
            <?php if (!empty($queryInfo[$id]['params'])): ?>
            <div class="mt-6 pt-6 border-t border-slate-100">
                <form method="GET" action="/<?= $id ?>" class="flex flex-wrap gap-4 items-end">
                    <?php foreach ($queryInfo[$id]['params'] as $param): ?>
                        <div class="flex flex-col w-48">
                            <label class="text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wider"><?= htmlspecialchars($param) ?></label>
                            <input type="text" name="<?= htmlspecialchars($param) ?>" 
                                   value="<?= htmlspecialchars($queryParams[$param] ?? '') ?>" 
                                   placeholder="Es. rosso"
                                   class="border border-slate-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all bg-slate-50 focus:bg-white shadow-inner">
                        </div>
                    <?php endforeach; ?>
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2 rounded-md font-semibold text-sm transition-colors shadow-md">
                        Applica Filtri
                    </button>
                    <?php if(!empty($queryParams) && count($queryParams) > (isset($queryParams['page']) ? 1 : 0)): ?>
                        <a href="/<?= $id ?>" class="text-sm text-slate-500 hover:text-slate-800 font-medium px-2 underline decoration-slate-300 underline-offset-4">Resetta</a>
                    <?php endif; ?>
                </form>
            </div>
            <?php endif; ?>
        </header>

        <div class="flex-1 overflow-auto p-8 custom-scrollbar">
            
            <?php if (empty($dati)): ?>
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 h-full flex flex-col items-center justify-center p-12 text-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100">
                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700">Nessun risultato trovato</h3>
                    <p class="text-slate-500 text-sm mt-1 max-w-sm">La query non ha prodotto risultati con i filtri attuali. Prova a modificare i parametri di ricerca o naviga verso un'altra pagina.</p>
                </div>
            <?php else: ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 pb-6">
                    <?php foreach ($dati as $index => $row): ?>
                        <?php
                            // Logica intelligente per trovare il titolo e l'icona della Card
                            $titoloCard = "Dettaglio Record";
                            $sottotitoloCard = "";
                            $icona = "M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"; // Documento base
                            
                            if (isset($row['fnome']) && isset($row['pnome'])) {
                                $titoloCard = $row['pnome'];
                                $sottotitoloCard = "Fornito da: " . $row['fnome'];
                                $icona = "M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"; // Pacco/Pezzo
                            } elseif (isset($row['fnome'])) {
                                $titoloCard = $row['fnome'];
                                $sottotitoloCard = "Azienda Fornitrice";
                                $icona = "M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"; // Palazzo/Azienda
                            } elseif (isset($row['pnome'])) {
                                $titoloCard = $row['pnome'];
                                $sottotitoloCard = "Pezzo in catalogo";
                                $icona = "M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"; // Ingranaggio
                            }
                        ?>
                        <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-shadow duration-300 border border-slate-200 overflow-hidden flex flex-col">
                            
                            <div class="bg-slate-800 p-5 border-b border-slate-200 flex items-start justify-between gap-4">
                                <div class="flex items-center gap-3 overflow-hidden">
                                    <div class="bg-blue-500/20 text-blue-400 p-2.5 rounded-lg shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $icona ?>"></path></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-white text-lg leading-tight truncate" title="<?= htmlspecialchars($titoloCard) ?>"><?= htmlspecialchars($titoloCard) ?></h3>
                                        <?php if($sottotitoloCard): ?>
                                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mt-1 truncate" title="<?= htmlspecialchars($sottotitoloCard) ?>"><?= htmlspecialchars($sottotitoloCard) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if(isset($row['costo'])): ?>
                                    <div class="bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 font-bold px-3 py-1.5 rounded-lg text-sm shrink-0 shadow-sm">
                                        € <?= number_format((float)$row['costo'], 2, ',', '.') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="p-5 flex-1">
                                <div class="grid grid-cols-2 gap-3">
                                    <?php foreach ($row as $key => $cell): ?>
                                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-100 flex flex-col justify-center">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1"><?= htmlspecialchars($key) ?></span>
                                            <span class="text-sm font-semibold text-slate-700 truncate" title="<?= htmlspecialchars((string)$cell) ?>">
                                                <?= htmlspecialchars((string)$cell) ?>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
        
        <?php if (!empty($meta)): ?>
        <?php 
            $currentPage = (int)($meta['page'] ?? 1);
            $limit = (int)($meta['limit'] ?? 10);
            $numResults = is_array($dati) ? count($dati) : 0;
            
            $hasPrev = $currentPage > 1;
            $hasNext = ($numResults === $limit && $numResults > 0); 

            $prevParams = $queryParams; $prevParams['page'] = $currentPage - 1;
            $prevUrl = '/' . $id . '?' . http_build_query($prevParams);

            $nextParams = $queryParams; $nextParams['page'] = $currentPage + 1;
            $nextUrl = '/' . $id . '?' . http_build_query($nextParams);
        ?>
        <div class="bg-white border-t border-slate-200 p-4 px-8 flex items-center justify-between shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] z-10">
            <p class="text-sm text-slate-500 font-medium">
                Mostrando <span class="font-bold text-slate-800"><?= $numResults ?></span> elementi (Limite: <?= $limit ?>)
            </p>
            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                <?php if ($hasPrev): ?>
                    <a href="<?= htmlspecialchars($prevUrl) ?>" class="relative inline-flex items-center rounded-l-md px-3 py-2 text-slate-400 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:z-20 transition-colors">
                        <span class="sr-only">Precedente</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" /></svg>
                    </a>
                <?php else: ?>
                    <button disabled class="relative inline-flex items-center rounded-l-md px-3 py-2 text-slate-300 ring-1 ring-inset ring-slate-200 bg-slate-50 cursor-not-allowed">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" /></svg>
                    </button>
                <?php endif; ?>
                
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-bold text-slate-700 ring-1 ring-inset ring-slate-300">
                    Pagina <?= $currentPage ?>
                </span>

                <?php if ($hasNext): ?>
                    <a href="<?= htmlspecialchars($nextUrl) ?>" class="relative inline-flex items-center rounded-r-md px-3 py-2 text-slate-400 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:z-20 transition-colors">
                        <span class="sr-only">Successiva</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>
                    </a>
                <?php else: ?>
                    <button disabled class="relative inline-flex items-center rounded-r-md px-3 py-2 text-slate-300 ring-1 ring-inset ring-slate-200 bg-slate-50 cursor-not-allowed">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>
                    </button>
                <?php endif; ?>
            </nav>
        </div>
        <?php endif; ?>

    </main>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 8px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb { background-color: #94a3b8; }
        
        aside .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #334155; }
        aside .custom-scrollbar:hover::-webkit-scrollbar-thumb { background-color: #475569; }
    </style>
</body>
</html>