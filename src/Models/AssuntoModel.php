<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Domain\Entities\Assunto;
use App\Domain\Exceptions\EntityException;
use App\Domain\Exceptions\NotFoundException;
use PDO;
use PDOException;

final class AssuntoModel
{
  private PDO $pdo;

  public function __construct()
  {
    $this->pdo = Database::getConnection();
  }

  /**
   * @return Assunto[]
   */
  public function findAll(): array
  {
    $stmt = $this->pdo->query('SELECT codAs, Descricao FROM Assunto ORDER BY Descricao ASC');
    $rows = $stmt->fetchAll();

    $assuntos = [];
    foreach ($rows as $row) {
      $assuntos[] = new Assunto((int) $row['codAs'], $row['Descricao']);
    }

    return $assuntos;
  }

  public function findById(int $id): ?Assunto
  {
    $stmt = $this->pdo->prepare('SELECT codAs, Descricao FROM Assunto WHERE codAs = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();

    if (!$row) {
      return null;
    }

    return new Assunto((int) $row['codAs'], $row['Descricao']);
  }

  public function save(Assunto $assunto): Assunto
  {
    if ($assunto->getId() === null) {
      return $this->insert($assunto);
    }

    return $this->update($assunto);
  }

  private function insert(Assunto $assunto): Assunto
  {
    $stmt = $this->pdo->prepare('INSERT INTO Assunto (Descricao) VALUES (:description)');
    $stmt->execute([':description' => $assunto->getDescription()]);
    $newId = (int) $this->pdo->lastInsertId();

    return new Assunto($newId, $assunto->getDescription());
  }

  private function update(Assunto $assunto): Assunto
  {
    $stmt = $this->pdo->prepare('UPDATE Assunto SET Descricao = :description WHERE codAs = :id');
    $stmt->execute([
      ':description' => $assunto->getDescription(),
      ':id' => $assunto->getId(),
    ]);

    return $assunto;
  }

  public function delete(int $id): void
  {
    $assunto = $this->findById($id);
    if (!$assunto) {
      throw new NotFoundException('Assunto não encontrado para eliminação.');
    }

    try {
      $stmt = $this->pdo->prepare('DELETE FROM Assunto WHERE codAs = :id');
      $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
      if ($e->getCode() === '23000') {
        throw new EntityException('Não é possível eliminar este assunto porque existem livros associados ao mesmo.');
      }
      throw $e;
    }
  }
}