<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use App\Domain\Entities\Livro;
use App\Domain\Exceptions\NotFoundException;
use PDO;
use Throwable;

final class LivroModel
{
  private PDO $pdo;

  public function __construct()
  {
    $this->pdo = Database::getConnection();
  }

  public function findAll(): array
  {
    $sql = "
            SELECT 
                l.Codl,
                l.Titulo,
                l.Editora,
                l.Edicao,
                l.AnoPublicacao,
                l.Valor,
                GROUP_CONCAT(DISTINCT a.Nome ORDER BY a.Nome SEPARATOR ', ') AS autores,
                GROUP_CONCAT(DISTINCT s.Descricao ORDER BY s.Descricao SEPARATOR ', ') AS assuntos
            FROM Livro l
            LEFT JOIN Livro_Autor la ON l.Codl = la.Livro_Codl
            LEFT JOIN Autor a ON la.Autor_CodAu = a.CodAu
            LEFT JOIN Livro_Assunto las ON l.Codl = las.Livro_Codl
            LEFT JOIN Assunto s ON las.Assunto_codAs = s.codAs
            GROUP BY l.Codl, l.Titulo, l.Editora, l.Edicao, l.AnoPublicacao, l.Valor
            ORDER BY l.Titulo ASC
        ";

    return $this->pdo->query($sql)->fetchAll();
  }

  public function findById(int $id): ?Livro
  {
    $stmt = $this->pdo->prepare('SELECT Codl, Titulo, Editora, Edicao, AnoPublicacao, Valor FROM Livro WHERE Codl = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();

    if (!$row) {
      return null;
    }

    // Buscar IDs de autores associados
    $stmtAuthors = $this->pdo->prepare('SELECT Autor_CodAu FROM Livro_Autor WHERE Livro_Codl = :id');
    $stmtAuthors->execute([':id' => $id]);
    $authorIds = $stmtAuthors->fetchAll(PDO::FETCH_COLUMN);

    // Buscar IDs de assuntos associados
    $stmtSubjects = $this->pdo->prepare('SELECT Assunto_codAs FROM Livro_Assunto WHERE Livro_Codl = :id');
    $stmtSubjects->execute([':id' => $id]);
    $subjectIds = $stmtSubjects->fetchAll(PDO::FETCH_COLUMN);

    return new Livro(
      (int) $row['Codl'],
      $row['Titulo'],
      $row['Editora'],
      (int) $row['Edicao'],
      $row['AnoPublicacao'],
      (float) $row['Valor'],
      $authorIds,
      $subjectIds
    );
  }

  public function save(Livro $livro): Livro
  {
    $this->pdo->beginTransaction();

    try {
      if ($livro->getId() === null) {
        $livro = $this->insert($livro);
      } else {
        $livro = $this->update($livro);
      }

      $this->syncRelations($livro);
      $this->pdo->commit();

      return $livro;
    } catch (Throwable $e) {
      $this->pdo->rollBack();
      throw $e;
    }
  }

  private function insert(Livro $livro): Livro
  {
    $stmt = $this->pdo->prepare("
            INSERT INTO Livro (Titulo, Editora, Edicao, AnoPublicacao, Valor)
            VALUES (:title, :publisher, :edition, :year, :price)
        ");

    $stmt->execute([
      ':title' => $livro->getTitle(),
      ':publisher' => $livro->getPublisher(),
      ':edition' => $livro->getEdition(),
      ':year' => $livro->getPublicationYear(),
      ':price' => $livro->getPrice(),
    ]);

    $newId = (int) $this->pdo->lastInsertId();

    return new Livro(
      $newId,
      $livro->getTitle(),
      $livro->getPublisher(),
      $livro->getEdition(),
      $livro->getPublicationYear(),
      $livro->getPrice(),
      $livro->getAuthorIds(),
      $livro->getSubjectIds()
    );
  }

  private function update(Livro $livro): Livro
  {
    $stmt = $this->pdo->prepare("
            UPDATE Livro 
            SET Titulo = :title, Editora = :publisher, Edicao = :edition, AnoPublicacao = :year, Valor = :price
            WHERE Codl = :id
        ");

    $stmt->execute([
      ':title' => $livro->getTitle(),
      ':publisher' => $livro->getPublisher(),
      ':edition' => $livro->getEdition(),
      ':year' => $livro->getPublicationYear(),
      ':price' => $livro->getPrice(),
      ':id' => $livro->getId(),
    ]);

    return $livro;
  }

  private function syncRelations(Livro $livro): void
  {
    $livroId = $livro->getId();

    // 1. Limpar e recriar vínculos com autores
    $stmtDelAuthors = $this->pdo->prepare('DELETE FROM Livro_Autor WHERE Livro_Codl = :id');
    $stmtDelAuthors->execute([':id' => $livroId]);

    $stmtInsAuthor = $this->pdo->prepare('INSERT INTO Livro_Autor (Livro_Codl, Autor_CodAu) VALUES (:book_id, :author_id)');
    foreach ($livro->getAuthorIds() as $authorId) {
      $stmtInsAuthor->execute([
        ':book_id' => $livroId,
        ':author_id' => $authorId,
      ]);
    }

    // 2. Limpar e recriar vínculos com assuntos
    $stmtDelSubjects = $this->pdo->prepare('DELETE FROM Livro_Assunto WHERE Livro_Codl = :id');
    $stmtDelSubjects->execute([':id' => $livroId]);

    $stmtInsSubject = $this->pdo->prepare('INSERT INTO Livro_Assunto (Livro_Codl, Assunto_codAs) VALUES (:book_id, :subject_id)');
    foreach ($livro->getSubjectIds() as $subjectId) {
      $stmtInsSubject->execute([
        ':book_id' => $livroId,
        ':subject_id' => $subjectId,
      ]);
    }
  }

  public function delete(int $id): void
  {
    $livro = $this->findById($id);
    if (!$livro) {
      throw new NotFoundException('Livro não encontrado para exclusão.');
    }

    $stmt = $this->pdo->prepare('DELETE FROM Livro WHERE Codl = :id');
    $stmt->execute([':id' => $id]);
  }
}