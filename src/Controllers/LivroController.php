<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Domain\Entities\Livro;
use App\Domain\Exceptions\NotFoundException;
use App\Domain\Exceptions\ValidationException;
use App\Models\AutorModel;
use App\Models\LivroModel;
use App\Models\AssuntoModel;
use Throwable;

final class LivroController
{
  private LivroModel $livroModel;
  private AutorModel $autorModel;
  private AssuntoModel $assuntoModel;

  public function __construct()
  {
    $this->livroModel = new LivroModel();
    $this->autorModel = new AutorModel();
    $this->assuntoModel = new AssuntoModel();
  }

  public function index(): void
  {
    $livros = $this->livroModel->findAll();
    $successMessage = $_SESSION['flash_success'] ?? null;
    $errorMessage = $_SESSION['flash_error'] ?? null;
    unset($_SESSION['flash_success'], $_SESSION['flash_error']);

    require __DIR__ . '/../Views/livros/index.php';
  }

  public function create(): void
  {
    $autores = $this->autorModel->findAll();
    $assuntos = $this->assuntoModel->findAll();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      try {
        $livro = $this->buildLivroFromRequest(null);
        $this->livroModel->save($livro);

        $_SESSION['flash_success'] = 'Livro registado com sucesso!';
        header('Location: /livros');
        exit;
      } catch (ValidationException $e) {
        $errorMessage = $e->getMessage();
        $livro = null;
        $formData = $_POST;
        require __DIR__ . '/../Views/livros/form.php';
        return;
      }
    }

    $livro = null;
    $formData = [];
    require __DIR__ . '/../Views/livros/form.php';
  }

  public function edit(): void
  {
    $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
    $autores = $this->autorModel->findAll();
    $assuntos = $this->assuntoModel->findAll();

    try {
      $livro = $this->livroModel->findById($id);
      if (!$livro) {
        throw new NotFoundException('Livro não encontrado.');
      }

      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $updatedLivro = $this->buildLivroFromRequest($id);
        $this->livroModel->save($updatedLivro);

        $_SESSION['flash_success'] = 'Livro atualizado com sucesso!';
        header('Location: /livros');
        exit;
      }

      $formData = [];
      require __DIR__ . '/../Views/livros/form.php';
    } catch (ValidationException $e) {
      $errorMessage = $e->getMessage();
      $formData = $_POST;
      require __DIR__ . '/../Views/livros/form.php';
    } catch (NotFoundException $e) {
      $_SESSION['flash_error'] = $e->getMessage();
      header('Location: /livros');
      exit;
    }
  }

  public function delete(): void
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = (int) ($_POST['id'] ?? 0);

      try {
        $this->livroModel->delete($id);
        $_SESSION['flash_success'] = 'Livro removido com sucesso!';
      } catch (NotFoundException $e) {
        $_SESSION['flash_error'] = $e->getMessage();
      } catch (Throwable $e) {
        $_SESSION['flash_error'] = 'Erro inesperado ao remover o livro.';
      }
    }

    header('Location: /livros');
    exit;
  }

  private function parseCurrency(string $value): float
  {
    $trimmed = trim($value);
    // Se contém vírgula, assume formato brasileiro (ex: "1.250,50" ou "150,50")
    if (str_contains($trimmed, ',')) {
      $clean = preg_replace('/[^\d,]/', '', $trimmed);
      $clean = str_replace(',', '.', (string) $clean);
      return (float) $clean;
    }
    // Caso contrário (ex: "150.50" ou "150")
    $clean = preg_replace('/[^\d.]/', '', $trimmed);
    return (float) $clean;
  }

  private function buildLivroFromRequest(?int $id): Livro
  {
    $title = (string) ($_POST['title'] ?? '');
    $publisher = (string) ($_POST['publisher'] ?? '');
    $edition = (int) ($_POST['edition'] ?? 1);
    $year = (string) ($_POST['publicationYear'] ?? '');
    $price = $this->parseCurrency((string) ($_POST['price'] ?? '0'));
    $authorIds = (array) ($_POST['author_ids'] ?? []);
    $subjectIds = (array) ($_POST['subject_ids'] ?? []);

    return new Livro($id, $title, $publisher, $edition, $year, $price, $authorIds, $subjectIds);
  }
}