<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Domain\Entities\Autor;
use App\Domain\Exceptions\EntityException;
use App\Domain\Exceptions\NotFoundException;
use PDO;
use PDOException;

final class AutorModel
{
  private PDO $pdo;

  public function __construct(?PDO $pdo = null)
  {
    $this->pdo = $pdo ?? Database::getConnection();
  }

  /**
   * @return Autor[]
   */
  public function findAll(): array
  {
    $stmt = $this->pdo->query('SELECT CodAu, Nome FROM Autor ORDER BY Nome ASC');
    $rows = $stmt->fetchAll();

    $authors = [];
    foreach ($rows as $row) {
      $authors[] = new Autor((int) $row['CodAu'], $row['Nome']);
    }

    return $authors;
  }

  public function findById(int $id): ?Autor
  {
    $stmt = $this->pdo->prepare('SELECT CodAu, Nome FROM Autor WHERE CodAu = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();

    if (!$row) {
      return null;
    }

    return new Autor((int) $row['CodAu'], $row['Nome']);
  }

  public function save(Autor $autor): Autor
  {
    if ($autor->getId() === null) {
      return $this->insert($autor);
    }

    return $this->update($autor);
  }

  private function insert(Autor $autor): Autor
  {
    $stmt = $this->pdo->prepare('INSERT INTO Autor (Nome) VALUES (:name)');
    $stmt->execute([':name' => $autor->getName()]);
    $newId = (int) $this->pdo->lastInsertId();

    return new Autor($newId, $autor->getName());
  }

  private function update(Autor $autor): Autor
  {
    $stmt = $this->pdo->prepare('UPDATE Autor SET Nome = :name WHERE CodAu = :id');
    $stmt->execute([
      ':name' => $autor->getName(),
      ':id' => $autor->getId(),
    ]);

    return $autor;
  }

  public function delete(int $id): void
  {
    $autor = $this->findById($id);
    if (!$autor) {
      throw new NotFoundException('Autor não encontrado para exclusão.');
    }

    try {
      $stmt = $this->pdo->prepare('DELETE FROM Autor WHERE CodAu = :id');
      $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
      // Código SQLSTATE 23000 = Violação de integridade referencial (FK)
      if ($e->getCode() === '23000') {
        throw new EntityException('Não é possível excluir este autor pois ele possui livros vinculados.');
      }
      throw $e;
    }
  }

  /**
   * Obtém o resumo estatístico do autor através da Stored Procedure sp_obter_resumo_autor.
   *
   * @param int $id ID do autor (CodAu)
   * @return array{autor_id: int, autor_nome: string, total_livros: int, valor_total_acervo: float, preco_medio_livro: float}|null
   */
  public function getResumoPorAutor(int $id): ?array
  {
    $stmt = $this->pdo->prepare('CALL sp_obter_resumo_autor(:id)');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    if (!$row) {
      return null;
    }

    return [
      'autor_id' => (int) $row['autor_id'],
      'autor_nome' => (string) $row['autor_nome'],
      'total_livros' => (int) $row['total_livros'],
      'valor_total_acervo' => (float) $row['valor_total_acervo'],
      'preco_medio_livro' => (float) $row['preco_medio_livro'],
    ];
  }
}