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

  public function __construct()
  {
    $this->pdo = Database::getConnection();
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
}