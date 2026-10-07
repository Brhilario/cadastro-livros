<?php ob_start(); ?>

<!-- Estilos dedicados à impressão/geração de PDF -->
<style>
  @media print {

    .no-print,
    nav,
    footer {
      display: none !important;
    }

    body {
      background-color: #fff !important;
    }

    .card {
      border: 1px solid #ddd !important;
      box-shadow: none !important;
    }

    .page-break {
      page-break-before: always;
    }
  }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 no-print">
  <div>
    <h2 class="h4 mb-0 text-secondary"><i class="bi bi-file-earmark-text-fill me-2"></i>Relatório Consolidado por Autor
    </h2>
    <small class="text-muted">Origem de dados: <code>VIEW vw_relatorio_livros_por_autor</code></small>
  </div>
  <div class="d-flex gap-2">
    <a href="/" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Voltar</a>
    <button type="button" class="btn btn-primary" onclick="window.print();">
      <i class="bi bi-printer me-1"></i>Imprimir / Gerar PDF
    </button>
  </div>
</div>

<!-- Resumo Executivo -->
<div class="row g-3 mb-4 no-print">
  <div class="col-md-4">
    <div class="card border-0 shadow-sm p-3 bg-white border-start border-primary border-4">
      <div class="text-muted small">Total de Autores com Obras</div>
      <div class="h4 fw-bold mb-0 text-dark"><?= count($reportData) ?></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm p-3 bg-white border-start border-info border-4">
      <div class="text-muted small">Total de Obras Registadas</div>
      <div class="h4 fw-bold mb-0 text-dark"><?= $grandTotalLivros ?></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm p-3 bg-white border-start border-success border-4">
      <div class="text-muted small">Valor Total do Acervo</div>
      <div class="h4 fw-bold mb-0 text-success">R$ <?= number_format($grandTotalValue, 2, ',', '.') ?></div>
    </div>
  </div>
</div>

<?php if (empty($reportData)): ?>
  <div class="alert alert-info shadow-sm">
    <i class="bi bi-info-circle me-2"></i>Nenhum dado encontrado para gerar o relatório.
  </div>
<?php else: ?>
  <?php foreach ($reportData as $author): ?>
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
        <div class="d-flex align-items-center">
          <span class="badge bg-primary me-2">Autor</span>
          <h5 class="mb-0 fw-bold text-dark"><?= htmlspecialchars($author['autor_nome']) ?></h5>
        </div>
        <div class="text-end">
          <span class="badge bg-secondary me-1"><?= $author['total_livros'] ?> livro(s)</span>
          <span class="badge bg-success">Subtotal: R$ <?= number_format($author['valor_total'], 2, ',', '.') ?></span>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle mb-0">
          <thead class="table-light">
            <tr class="small text-secondary">
              <th style="width: 50px;">Cód.</th>
              <th>Título</th>
              <th>Editora</th>
              <th style="width: 70px;">Edição</th>
              <th style="width: 60px;">Ano</th>
              <th>Coautor(es)</th>
              <th>Assunto(s)</th>
              <th class="text-end" style="width: 120px;">Valor Unitário</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($author['livros'] as $book): ?>
              <tr>
                <td class="text-muted fw-semibold">#<?= $book['id'] ?></td>
                <td class="fw-bold"><?= htmlspecialchars($book['titulo']) ?></td>
                <td><?= htmlspecialchars($book['editora']) ?></td>
                <td><?= $book['edicao'] ?>ª</td>
                <td><?= htmlspecialchars($book['ano']) ?></td>
                <td><small class="text-muted"><?= htmlspecialchars($book['todos_autores']) ?></small></td>
                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($book['assuntos']) ?></span></td>
                <td class="text-end fw-semibold text-dark">R$ <?= number_format($book['valor'], 2, ',', '.') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php
$content = ob_get_clean();
$title = "Relatório de Livros por Autor";
require __DIR__ . '/../layout.php';
?>