<?php
/** @var \App\Domain\Entities\Assunto|null $assunto */
$assunto = $assunto ?? null;
$description = $description ?? ($assunto ? $assunto->getDescription() : '');
ob_start();
?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 text-secondary">
          <?= ($assunto && $assunto->getId()) ? 'Editar Assunto #' . $assunto->getId() : 'Novo Assunto' ?>
        </h5>
      </div>
      <div class="card-body p-4">
        <form action="<?= ($assunto && $assunto->getId()) ? '/assuntos/editar' : '/assuntos/novo' ?>" method="POST">
          <?php if ($assunto && $assunto->getId()): ?>
            <input type="hidden" name="id" value="<?= $assunto->getId() ?>">
          <?php endif; ?>

          <div class="mb-3">
            <label for="description" class="form-label fw-semibold">Descrição do Assunto <span
                class="text-danger">*</span></label>
            <input type="text" class="form-control" id="description" name="description" maxlength="20" required
              placeholder="Ex: Arquitetura" value="<?= htmlspecialchars($description) ?>">
            <div class="form-text">Máximo de 20 caracteres (conforme especificado no modelo).</div>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="/assuntos" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-check-lg me-1"></i>Guardar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
$title = ($assunto && $assunto->getId()) ? "Editar Assunto" : "Novo Assunto";
require __DIR__ . '/../layout.php';
?>