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
          <th class="text-end" style="width: 200px;">Ações</th>
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
                <button type="button" class="btn btn-sm btn-outline-info me-1"
                  title="Estatísticas do Autor (Procedure)"
                  onclick="carregarResumoAutor(<?= $autor->getId() ?>)">
                  <i class="bi bi-bar-chart-line-fill"></i>
                </button>
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

<!-- Modal Resumo do Autor (alimentado pela Stored Procedure sp_obter_resumo_autor) -->
<div class="modal fade" id="modalResumoAutor" tabindex="-1" aria-labelledby="modalResumoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow border-0">
      <div class="modal-header bg-light">
        <div>
          <h5 class="modal-title fw-bold text-dark mb-0" id="modalResumoLabel">
            <i class="bi bi-bar-chart-line-fill text-info me-2"></i>Estatísticas do Autor
          </h5>
          <small class="text-muted">Origem: <code>sp_obter_resumo_autor</code></small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body p-4">
        <div id="modalResumoLoading" class="text-center py-4">
          <div class="spinner-border text-info" role="status">
            <span class="visually-hidden">Carregando...</span>
          </div>
          <p class="text-muted small mt-2 mb-0">Consultando procedure no banco de dados...</p>
        </div>

        <div id="modalResumoContent" style="display: none;">
          <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
            <h5 id="resumoAutorNome" class="fw-bold text-dark mb-0"></h5>
            <span class="badge bg-secondary">Cód: #<span id="resumoAutorId"></span></span>
          </div>

          <div class="row g-3">
            <div class="col-12">
              <div class="card border-0 bg-light p-3 border-start border-primary border-4 shadow-sm">
                <div class="text-muted small">Total de Obras Registadas</div>
                <div class="h4 fw-bold mb-0 text-dark"><span id="resumoTotalLivros">0</span> livro(s)</div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="card border-0 bg-light p-3 border-start border-success border-4 shadow-sm">
                <div class="text-muted small">Valor Total do Acervo</div>
                <div class="h5 fw-bold mb-0 text-success">R$ <span id="resumoValorAcervo">0,00</span></div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="card border-0 bg-light p-3 border-start border-info border-4 shadow-sm">
                <div class="text-muted small">Preço Médio por Obra</div>
                <div class="h5 fw-bold mb-0 text-info">R$ <span id="resumoPrecoMedio">0,00</span></div>
              </div>
            </div>
          </div>
        </div>

        <div id="modalResumoError" class="alert alert-danger mb-0" style="display: none;">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>Erro ao carregar os dados estatísticos deste autor.
        </div>
      </div>
      <div class="modal-footer bg-light border-0">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<script>
function carregarResumoAutor(autorId) {
  const modalEl = document.getElementById('modalResumoAutor');
  const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

  const loading = document.getElementById('modalResumoLoading');
  const content = document.getElementById('modalResumoContent');
  const errorAlert = document.getElementById('modalResumoError');

  loading.style.display = 'block';
  content.style.display = 'none';
  errorAlert.style.display = 'none';

  modal.show();

  fetch('/autores/resumo?id=' + encodeURIComponent(autorId))
    .then(response => {
      if (!response.ok) {
        throw new Error('Falha ao obter resumo do autor');
      }
      return response.json();
    })
    .then(data => {
      document.getElementById('resumoAutorId').textContent = data.autor_id;
      document.getElementById('resumoAutorNome').textContent = data.autor_nome;
      document.getElementById('resumoTotalLivros').textContent = data.total_livros;
      document.getElementById('resumoValorAcervo').textContent = data.valor_total_formatado;
      document.getElementById('resumoPrecoMedio').textContent = data.preco_medio_formatado;

      loading.style.display = 'none';
      content.style.display = 'block';
    })
    .catch(() => {
      loading.style.display = 'none';
      errorAlert.style.display = 'block';
    });
}
</script>

<?php
$content = ob_get_clean();
$title = "Listagem de Autores";
require __DIR__ . '/../layout.php';
?>