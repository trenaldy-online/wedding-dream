<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin Wedding</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<section class="min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h1 class="h4 fw-bold mb-1">Login Admin</h1>
                            <p class="text-muted mb-0">
                                Masuk untuk mengelola wedding app.
                            </p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    value="{{ old('email') }}"
                                    placeholder="admin@wedding.test"
                                    autofocus
                                >
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="Masukkan password"
                                >
                            </div>

                            <div class="form-check mb-3">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    value="1"
                                    class="form-check-input"
                                    id="remember"
                                >
                                <label class="form-check-label" for="remember">
                                    Ingat saya
                                </label>
                            </div>

                            <button class="btn btn-primary w-100">
                                Login
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-center text-muted small mt-3">
                    Wedding App Admin Panel
                </div>
            </div>
        </div>
    </div>
</section>
</body>
</html>