<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\RelatorioModel;
use PHPUnit\Framework\TestCase;

final class RelatorioTest extends TestCase
{
  public function testNaoDeveDuplicarLivrosComMultiplosAutoresNoCalculoDeTotaisGerais(): void
  {
    // Instancia o modelo sem conexão PDO para teste unitário puro da lógica de cálculo
    $model = new RelatorioModel($this->createMock(\PDO::class));

    // Simula dados agrupados onde o livro com id=1 ("Refactoring", R$ 215.50)
    // pertence a dois autores distintos (Martin Fowler e Kent Beck)
    $reportData = [
      1 => [
        'autor_id' => 1,
        'autor_nome' => 'Martin Fowler',
        'total_livros' => 1,
        'valor_total' => 215.50,
        'livros' => [
          [
            'id' => 1,
            'titulo' => 'Refactoring',
            'editora' => 'Addison-Wesley',
            'edicao' => 2,
            'ano' => '2018',
            'valor' => 215.50,
            'assuntos' => 'Eng. de Software',
            'todos_autores' => 'Martin Fowler, Kent Beck',
          ],
        ],
      ],
      2 => [
        'autor_id' => 2,
        'autor_nome' => 'Kent Beck',
        'total_livros' => 2,
        'valor_total' => 365.50,
        'livros' => [
          [
            'id' => 1, // Mesmo livro presente no autor anterior
            'titulo' => 'Refactoring',
            'editora' => 'Addison-Wesley',
            'edicao' => 2,
            'ano' => '2018',
            'valor' => 215.50,
            'assuntos' => 'Eng. de Software',
            'todos_autores' => 'Martin Fowler, Kent Beck',
          ],
          [
            'id' => 2,
            'titulo' => 'Extreme Programming Explained',
            'editora' => 'Addison-Wesley',
            'edicao' => 2,
            'ano' => '2004',
            'valor' => 150.00,
            'assuntos' => 'Metodologias Ágeis',
            'todos_autores' => 'Kent Beck',
          ],
        ],
      ],
    ];

    $totais = $model->calcularTotaisGerais($reportData);

    // Devem existir 2 livros distintos no total (id 1 e id 2), e não 3
    $this->assertSame(2, $totais['total_livros']);

    // O valor deve ser 215.50 + 150.00 = 365.50, e não somar o livro 1 duas vezes (581.00)
    $this->assertSame(365.50, $totais['valor_total']);
  }

  public function testDeveRetornarZerosQuandoNaoHouverDados(): void
  {
    $model = new RelatorioModel($this->createMock(\PDO::class));

    $totais = $model->calcularTotaisGerais([]);

    $this->assertSame(0, $totais['total_livros']);
    $this->assertSame(0.0, (float) $totais['valor_total']);
  }
}
