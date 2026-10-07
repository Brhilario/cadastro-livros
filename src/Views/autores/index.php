<?php ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="h4 mb-0 text-secondary"><i class="bi bi-people-fill me-2"></i>Autores</h2>
  <a href="/autores/novo" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Novo Autor</a>
</div>

<div class="card shadow-sm border-0">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th style="width: 100px;">Código</th>
          <th>Nome</th>
          <th class="text-end" style="width: 160px;">Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($autores)): ?>
          <tr>
            <td colspan="3" class="text-center py-4 text-muted">Nenhum autor cadastrado.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($autores as $autor): ?>
            <tr>
              <td class="fw-semibold text-secondary">#<?= $autor->getId() ?></td>
              <td><?= htmlspecialchars($autor->getName()) ?></td>
              <td class="text-end">
                <a href="/autores/editar?id=<?= $autor->getId() ?>" class="btn btn-sm btn-outline-primary me-1"
                  title="Editar">
                  <i class="bi bi-pencil"></i>
                </a>
                <form action="/autores/excluir" method="POST" class="d-inline"
                  onsubmit="return confirm('Tem certeza que deseja remover este autor?');">
                  <input type="hidden" name="id" value="<?= $autor->getId() ?>">
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">
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
$title = "Listagem de Autores";
require __DIR__ . '/../layout.php';
?>