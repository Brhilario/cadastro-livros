<?php
/** @var \App\Domain\Entities\Livro|null $livro */
/** @var array $formData */
/** @var array $autores */
/** @var array $assuntos */
$livro = $livro ?? null;
$formData = $formData ?? [];

$id = $livro?->getId();
$titleValue = $formData['title'] ?? ($livro ? $livro->getTitle() : '');
$publisherValue = $formData['publisher'] ?? ($livro ? $livro->getPublisher() : '');
$editionValue = (int) ($formData['edition'] ?? ($livro ? $livro->getEdition() : 1));
$publicationYearValue = $formData['publicationYear'] ?? ($livro ? $livro->getPublicationYear() : date('Y'));
$priceValue = $formData['price'] ?? ($livro ? number_format($livro->getPrice(), 2, ',', '.') : '0,00');

$selectedAuthorIds = isset($formData['author_ids']) 
    ? array_map('intval', (array) $formData['author_ids']) 
    : ($livro ? $livro->getAuthorIds() : []);

$selectedSubjectIds = isset($formData['subject_ids']) 
    ? array_map('intval', (array) $formData['subject_ids']) 
    : ($livro ? $livro->getSubjectIds() : []);

ob_start();
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 text-secondary">
                    <?= ($livro && $livro->getId()) ? 'Editar Livro #' . $livro->getId() : 'Registar Novo Livro' ?>
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= ($livro && $livro->getId()) ? '/livros/editar' : '/livros/novo' ?>" method="POST" id="bookForm">
                    <?php if ($livro && $livro->getId()): ?>
                        <input type="hidden" name="id" value="<?= $livro->getId() ?>">
                    <?php endif; ?>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="title" class="form-label fw-semibold">Título do Livro <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" maxlength="40" required
                                   value="<?= htmlspecialchars((string) $titleValue) ?>" placeholder="Ex: Clean Code">
                            <div class="form-text">Máx. 40 carateres.</div>
                        </div>

                        <div class="col-md-4">
                            <label for="publisher" class="form-label fw-semibold">Editora <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="publisher" name="publisher" maxlength="40" required
                                   value="<?= htmlspecialchars((string) $publisherValue) ?>" placeholder="Ex: Prentice Hall">
                        </div>

                        <div class="col-md-4">
                            <label for="edition" class="form-label fw-semibold">Edição <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="edition" name="edition" min="1" required
                                   value="<?= $editionValue > 0 ? $editionValue : 1 ?>">
                        </div>

                        <div class="col-md-4">
                            <label for="publicationYear" class="form-label fw-semibold">Ano de Publicação <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="publicationYear" name="publicationYear" maxlength="4" required
                                   value="<?= htmlspecialchars((string) $publicationYearValue) ?>" placeholder="Ex: 2008" pattern="\d{4}">
                        </div>

                        <div class="col-md-4">
                            <label for="price" class="form-label fw-semibold">Valor (R$) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="price" name="price" required
                                   value="<?= htmlspecialchars((string) $priceValue) ?>" placeholder="R$ 0,00">
                        </div>

                        <!-- Autores (Seleção Múltipla) -->
                        <div class="col-md-6 mt-4">
                            <label class="form-label fw-semibold">Autores <span class="text-danger">*</span></label>
                            <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                                <?php if (empty($autores)): ?>
                                    <span class="text-muted small">Nenhum autor disponível. <a href="/autores/novo">Cadastre um autor</a> primeiro.</span>
                                <?php else: ?>
                                    <?php foreach ($autores as $a): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="author_ids[]" 
                                                   value="<?= $a->getId() ?>" id="author_<?= $a->getId() ?>"
                                                   <?= in_array($a->getId(), $selectedAuthorIds, true) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="author_<?= $a->getId() ?>">
                                                <?= htmlspecialchars($a->getName()) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <div class="form-text">Pode selecionar um ou mais autores.</div>
                        </div>

                        <!-- Assuntos (Seleção Múltipla) -->
                        <div class="col-md-6 mt-4">
                            <label class="form-label fw-semibold">Assuntos <span class="text-danger">*</span></label>
                            <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                                <?php if (empty($assuntos)): ?>
                                    <span class="text-muted small">Nenhum assunto disponível. <a href="/assuntos/novo">Cadastre um assunto</a> primeiro.</span>
                                <?php else: ?>
                                    <?php foreach ($assuntos as $s): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="subject_ids[]" 
                                                   value="<?= $s->getId() ?>" id="subject_<?= $s->getId() ?>"
                                                   <?= in_array($s->getId(), $selectedSubjectIds, true) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="subject_<?= $s->getId() ?>">
                                                <?= htmlspecialchars($s->getDescription()) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <div class="form-text">Pode selecionar um ou mais assuntos.</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="/livros" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Guardar Livro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script nativo de máscara para moeda e ano -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const priceInput = document.getElementById('price');
    const yearInput = document.getElementById('publicationYear');

    // Máscara de Ano (apenas 4 dígitos)
    if (yearInput) {
        yearInput.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/\D/g, '').slice(0, 4);
        });
    }

    // Máscara de Moeda Simples e Robusta
    if (priceInput) {
        priceInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (!value) {
                e.target.value = '0,00';
                return;
            }
            value = (parseInt(value, 10) / 100).toFixed(2);
            e.target.value = value.replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        });
    }
});
</script>

<?php 
$content = ob_get_clean(); 
$title = ($livro && $livro->getId()) ? "Editar Livro" : "Novo Livro";
require __DIR__ . '/../layout.php';
?>