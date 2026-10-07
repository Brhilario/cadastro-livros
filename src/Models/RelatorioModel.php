<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class RelatorioModel
{
  private PDO $pdo;

  public function __construct(?PDO $pdo = null)
  {
    $this->pdo = $pdo ?? Database::getConnection();
  }

  /**
   * Consulta os dados a partir da VIEW vw_relatorio_livros_por_autor
   * e agrupa a estrutura por autor.
   *
   * @return array<int, array{autor_nome: string, total_livros: int, valor_total: float, livros: array}>
   */
  public function getLivrosAgrupadosPorAutor(): array
  {
    $sql = "
            SELECT 
                autor_id,
                autor_nome,
                livro_id,
                livro_titulo,
                livro_editora,
                livro_edicao,
                livro_ano,
                livro_valor,
                assuntos,
                todos_autores
            FROM vw_relatorio_livros_por_autor
            ORDER BY autor_nome ASC, livro_titulo ASC
        ";

    $stmt = $this->pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $grouped = [];

    foreach ($rows as $row) {
      $authorId = (int) $row['autor_id'];

      if (!isset($grouped[$authorId])) {
        $grouped[$authorId] = [
          'autor_id' => $authorId,
          'autor_nome' => $row['autor_nome'],
          'total_livros' => 0,
          'valor_total' => 0.0,
          'livros' => [],
        ];
      }

      $grouped[$authorId]['total_livros']++;
      $grouped[$authorId]['valor_total'] += (float) $row['livro_valor'];

      $grouped[$authorId]['livros'][] = [
        'id' => (int) $row['livro_id'],
        'titulo' => $row['livro_titulo'],
        'editora' => $row['livro_editora'],
        'edicao' => (int) $row['livro_edicao'],
        'ano' => $row['livro_ano'],
        'valor' => (float) $row['livro_valor'],
        'assuntos' => $row['assuntos'],
        'todos_autores' => $row['todos_autores'],
      ];
    }

    return $grouped;
  }

  /**
   * Calcula os totais consolidados do acervo a partir dos dados agrupados por autor,
   * garantindo que livros associados a múltiplos autores sejam contabilizados
   * apenas uma vez no total de obras registradas e no valor total do acervo.
   *
   * @param array<int, array{autor_id: int, autor_nome: string, total_livros: int, valor_total: float, livros: array}> $reportData
   * @return array{total_livros: int, valor_total: float}
   */
  public function calcularTotaisGerais(array $reportData): array
  {
    $livrosUnicos = [];

    foreach ($reportData as $authorGroup) {
      foreach ($authorGroup['livros'] as $book) {
        $livrosUnicos[$book['id']] = (float) $book['valor'];
      }
    }

    return [
      'total_livros' => count($livrosUnicos),
      'valor_total' => array_sum($livrosUnicos),
    ];
  }
}