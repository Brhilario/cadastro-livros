<?php
/** @var \App\Domain\Entities\Autor|null $autor */
$autor = $autor ?? $author ?? null;
$name = $name ?? ($autor ? $autor->getName() : '');
ob_start();
?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 text-secondary">
          <?= ($autor && $autor->getId()) ? 'Editar Autor #' . $autor->getId() : 'Novo Autor' ?>
        </h5>
      </div>
      <div class="card-body p-4">
        <form action="<?= ($autor && $autor->getId()) ? '/autores/editar' : '/autores/novo' ?>" method="POST">
          <?php if ($autor && $autor->getId()): ?>
            <input type="hidden" name="id" value="<?= $autor->getId() ?>">
          <?php endif; ?>

          <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Nome do Autor <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name" name="name" maxlength="40" required
              placeholder="Ex: Martin Fowler" value="<?= htmlspecialchars($name) ?>">
            <div class="form-text">Máximo de 40 caracteres (conforme modelo de dados).</div>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="/autores" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-check-lg me-1"></i>Salvar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
$title = ($autor && $autor->getId()) ? "Editar Autor" : "Novo Autor";
require __DIR__ . '/../layout.php';
?>