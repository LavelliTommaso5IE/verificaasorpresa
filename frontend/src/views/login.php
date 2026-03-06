<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - API Explorer</title>
    <link href="/css/output.css" rel="stylesheet">
</head>
<body class="bg-slate-50 flex items-center justify-center h-screen font-sans selection:bg-blue-200">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-slate-100">
        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-600/30">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
        </div>
        
        <h1 class="text-2xl font-extrabold text-center text-slate-800 mb-2">Bentornato</h1>
        <p class="text-center text-slate-500 text-sm mb-8">Accedi al pannello di controllo dell'API</p>

        <?php if (!empty($error)): ?>
            <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm text-center font-medium mb-6 border border-red-100">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/login" class="space-y-5">
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Email</label>
                <input type="email" name="email" required placeholder="admin@admin.com" 
                       class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-slate-50 focus:bg-white transition-all outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Password</label>
                <input type="password" name="password" required placeholder="••••••••" 
                       class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-slate-50 focus:bg-white transition-all outline-none">
            </div>
            
            <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 rounded-lg transition-colors shadow-md mt-4">
                Accedi alla Dashboard
            </button>
        </form>
    </div>

</body>
</html>