<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Erreur')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased">
    <main class="mx-auto flex min-h-screen max-w-3xl items-center justify-center px-6 py-12">
        <section class="w-full rounded-2xl bg-white p-8 shadow-xl ring-1 ring-slate-200 sm:p-12">
            <p class="mb-2 text-sm font-semibold uppercase tracking-wide text-indigo-600">Erreur @yield('code')</p>
            <h1 class="mb-4 text-3xl font-bold text-slate-900 sm:text-4xl">@yield('title')</h1>
            <p class="mb-8 text-base leading-relaxed text-slate-600 sm:text-lg">@yield('message')</p>

            <a href="{{ url('/login') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                Retour en arrière
            </a>
        </section>
    </main>
</body>
</html>
