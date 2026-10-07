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

    $grandTotalLivros = 0;
    $grandTotalValue = 0.0;

    foreach ($reportData as $authorGroup) {
      $grandTotalLivros += $authorGroup['total_livros'];
      $grandTotalValue += $authorGroup['valor_total'];
    }

    require __DIR__ . '/../Views/relatorios/index.php';
  }
}