<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion Pressing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-3"><img src="{{ asset('logo-pressing.svg') }}" alt="Logo" width="72" height="72"></div>
                    <h1 class="h4 fw-bold mb-1">Connexion</h1>
                    <p class="text-muted small mb-4">Admin, propriétaire ou employé.</p>

                    @if($errors->any())
                        <div class="alert alert-danger py-2">Identifiants incorrects. Vérifiez votre email/mot de passe.</div>
                    @endif

                    <form method="POST" action="/login" class="vstack gap-3">
                        @csrf
                        <div>
                            <label class="form-label">Email</label>
                            <input class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus>
                        </div>
                        <div>
                            <label class="form-label">Mot de passe</label>
                            <input class="form-control" type="password" name="password" required>
                        </div>
                        <button class="btn btn-primary w-100" type="submit">Se connecter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
