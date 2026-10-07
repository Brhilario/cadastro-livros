<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\RelatorioModel;


final class RelatorioController
{
  private RelatorioModel $relatorioModel;

  public function __construct()
  {
    $this->relatorioModel = new RelatorioModel();
  }

  public function index(): void
  {
    $reportData = $this->relatorioModel->getLivrosAgrupadosPorAutor();

    $totaisGerais = $this->relatorioModel->calcularTotaisGerais($reportData);

    $grandTotalLivros = $totaisGerais['total_livros'];
    $grandTotalValue = $totaisGerais['valor_total'];

    require __DIR__ . '/../Views/relatorios/index.php';
  }
}