<?php ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="h4 mb-0 text-secondary"><i class="bi bi-book-half me-2"></i>Catálogo de Livros</h2>
  <a href="/livros/novo" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Novo Livro</a>
</div>

<div class="card shadow-sm border-0">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th style="width: 70px;">Cód.</th>
          <th>Título</th>
          <th>Editora</th>
          <th>Edição</th>
          <th>Ano</th>
          <th>Valor (R$)</th>
          <th>Autores</th>
          <th>Assuntos</th>
          <th class="text-end" style="width: 130px;">Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($livros)): ?>
          <tr>
            <td colspan="9" class="text-center py-4 text-muted">Nenhum livro registado.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($livros as $livro): ?>
            <tr>
              <td class="fw-semibold text-secondary">#<?= $livro['Codl'] ?></td>
              <td class="fw-bold text-dark"><?= htmlspecialchars($livro['Titulo']) ?></td>
              <td><?= htmlspecialchars($livro['Editora']) ?></td>
              <td><span class="badge bg-secondary"><?= $livro['Edicao'] ?>ª Ed.</span></td>
              <td><?= htmlspecialchars($livro['AnoPublicacao']) ?></td>
              <td class="fw-bold text-success">R$ <?= number_format((float) $livro['Valor'], 2, ',', '.') ?></td>
              <td><small class="text-muted"><?= htmlspecialchars($livro['autores'] ?? 'Sem autor') ?></small></td>
              <td><small class="text-muted"><?= htmlspecialchars($livro['assuntos'] ?? 'Sem assunto') ?></small></td>
              <td class="text-end">
                <a href="/livros/editar?id=<?= $livro['Codl'] ?>" class="btn btn-sm btn-outline-primary me-1"
                  title="Editar">
                  <i class="bi bi-pencil"></i>
                </a>
                <form action="/livros/excluir" method="POST" class="d-inline"
                  onsubmit="return confirm('Tem a certeza de que deseja remover este livro?');">
                  <input type="hidden" name="id" value="<?= $livro['Codl'] ?>">
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$content = ob_get_clean();
$title = "Catálogo de Livros";
require __DIR__ . '/../layout.php';
?>