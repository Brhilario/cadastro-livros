<?php ob_start(); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="h4 mb-0 text-secondary"><i class="bi bi-tags-fill me-2"></i>Assuntos</h2>
  <a href="/assuntos/novo" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Novo Assunto</a>
</div>

<div class="card shadow-sm border-0">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th style="width: 100px;">Código</th>
          <th>Descrição</th>
          <th class="text-end" style="width: 160px;">Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($assuntos)): ?>
          <tr>
            <td colspan="3" class="text-center py-4 text-muted">Nenhum assunto registado.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($assuntos as $assunto): ?>
            <tr>
              <td class="fw-semibold text-secondary">#<?= $assunto->getId() ?></td>
              <td><?= htmlspecialchars($assunto->getDescription()) ?></td>
              <td class="text-end">
                <a href="/assuntos/editar?id=<?= $assunto->getId() ?>" class="btn btn-sm btn-outline-primary me-1"
                  title="Editar">
                  <i class="bi bi-pencil"></i>
                </a>
                <form action="/assuntos/excluir" method="POST" class="d-inline"
                  onsubmit="return confirm('Tem a certeza de que deseja remover este assunto?');">
                  <input type="hidden" name="id" value="<?= $assunto->getId() ?>">
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
$title = "Listagem de Assuntos";
require __DIR__ . '/../layout.php';
?>