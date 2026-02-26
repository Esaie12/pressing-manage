<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Erreur')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center justify-content-center vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5 text-center">

                        <p class="text-uppercase text-primary fw-semibold mb-2">
                            Erreur @yield('code')
                        </p>

                       <div class="d-flex justify-content-center">
                            <h1 class="fw-bold mb-4">
                                @yield('title')
                            </h1>
                       </div>

                        <div class="d-flex  justify-content-cente">
                            <p class="text-muted mb-4">
                                @yield('message')
                            </p>
                        </div>

                        <a href="{{ url('/login') }}" 
                           class="btn btn-primary px-4 py-2 rounded-pill">
                            Retour en arrière
                        </a>

                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>