<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class RelatorioModel
{
  private PDO $pdo;

  public function __construct()
  {
    $this->pdo = Database::getConnection();
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
}