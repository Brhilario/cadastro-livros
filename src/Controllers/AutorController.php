<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Domain\Entities\Autor;
use App\Domain\Exceptions\EntityException;
use App\Domain\Exceptions\NotFoundException;
use App\Domain\Exceptions\ValidationException;
use App\Models\AutorModel;
use Throwable;

final class AutorController
{
  private AutorModel $autorModel;

  public function __construct()
  {
    $this->autorModel = new AutorModel();
  }

  public function index(): void
  {
    $autores = $this->autorModel->findAll();
    $successMessage = $_SESSION['flash_success'] ?? null;
    $errorMessage = $_SESSION['flash_error'] ?? null;
    unset($_SESSION['flash_success'], $_SESSION['flash_error']);

    require __DIR__ . '/../Views/autores/index.php';
  }

  public function create(): void
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $name = (string) ($_POST['name'] ?? '');
      try {
        $autor = new Autor(null, $name);
        $this->autorModel->save($autor);

        $_SESSION['flash_success'] = 'Autor cadastrado com sucesso!';
        header('Location: /autores');
        exit;
      } catch (ValidationException $e) {
        $errorMessage = $e->getMessage();
        $autor = null;
        require __DIR__ . '/../Views/autores/form.php';
        return;
      }
    }

    $autor = null;
    $name = '';
    require __DIR__ . '/../Views/autores/form.php';
  }

  public function edit(): void
  {
    $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

    try {
      $autor = $this->autorModel->findById($id);
      if (!$autor) {
        throw new NotFoundException('Autor não encontrado.');
      }

      $name = $autor->getName();

      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = (string) ($_POST['name'] ?? '');
        $autor->setName($name);
        $this->autorModel->save($autor);

        $_SESSION['flash_success'] = 'Autor atualizado com sucesso!';
        header('Location: /autores');
        exit;
      }

      require __DIR__ . '/../Views/autores/form.php';
    } catch (ValidationException $e) {
      $errorMessage = $e->getMessage();
      require __DIR__ . '/../Views/autores/form.php';
    } catch (NotFoundException $e) {
      $_SESSION['flash_error'] = $e->getMessage();
      header('Location: /autores');
      exit;
    }
  }

  public function delete(): void
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = (int) ($_POST['id'] ?? 0);

      try {
        $this->autorModel->delete($id);
        $_SESSION['flash_success'] = 'Autor excluído com sucesso!';
      } catch (EntityException $e) {
        $_SESSION['flash_error'] = $e->getMessage();
      } catch (NotFoundException $e) {
        $_SESSION['flash_error'] = $e->getMessage();
      } catch (Throwable $e) {
        $_SESSION['flash_error'] = 'Erro inesperado ao excluir o autor.';
      }
    }

    header('Location: /autores');
    exit;
  }

  public function resumo(): void
  {
    $id = (int) ($_GET['id'] ?? 0);

    $resumo = $this->autorModel->getResumoPorAutor($id);

    header('Content-Type: application/json; charset=utf-8');

    if (!$resumo) {
      http_response_code(404);
      echo json_encode(['error' => 'Autor não encontrado ou sem dados vinculados.']);
      return;
    }

    echo json_encode([
      'autor_id' => $resumo['autor_id'],
      'autor_nome' => $resumo['autor_nome'],
      'total_livros' => $resumo['total_livros'],
      'valor_total_acervo' => $resumo['valor_total_acervo'],
      'valor_total_formatado' => number_format($resumo['valor_total_acervo'], 2, ',', '.'),
      'preco_medio_livro' => $resumo['preco_medio_livro'],
      'preco_medio_formatado' => number_format($resumo['preco_medio_livro'], 2, ',', '.'),
    ]);
  }
}