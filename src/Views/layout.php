<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Sistema de Livros') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light d-flex flex-column min-vh-100">
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
    <div class="container">
      <a class="navbar-brand fw-bold" href="/"><i class="bi bi-book-half me-2"></i>Biblioteca</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navContent">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link" href="/autores">Autores</a></li>
          <li class="nav-item"><a class="nav-link" href="/assuntos">Assuntos</a></li>
          <li class="nav-item"><a class="nav-link" href="/livros">Livros</a></li>
          <li class="nav-item"><a class="nav-link text-warning fw-semibold" href="/relatorio">Relatório</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <main class="container flex-grow-1">
    <?php if (!empty($errorMessage)): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($errorMessage) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if (!empty($successMessage)): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($successMessage) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?= $content ?>
  </main>

  <footer class="bg-white border-top py-3 mt-auto text-center text-muted small">
    Cadastro de Livros &copy; <?= date('Y') ?> - PHP Puro sem Framework
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>