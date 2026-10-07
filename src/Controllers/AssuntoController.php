<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Domain\Entities\Assunto;
use App\Domain\Exceptions\EntityException;
use App\Domain\Exceptions\NotFoundException;
use App\Domain\Exceptions\ValidationException;
use App\Models\AssuntoModel;
use Throwable;

final class AssuntoController
{
  private AssuntoModel $model;

  public function __construct()
  {
    $this->model = new AssuntoModel();
  }

  public function index(): void
  {
    $assuntos = $this->model->findAll();
    $successMessage = $_SESSION['flash_success'] ?? null;
    $errorMessage = $_SESSION['flash_error'] ?? null;
    unset($_SESSION['flash_success'], $_SESSION['flash_error']);

    require __DIR__ . '/../Views/assuntos/index.php';
  }

  public function create(): void
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $description = (string) ($_POST['description'] ?? '');
      try {
        $assunto = new Assunto(null, $description);
        $this->model->save($assunto);

        $_SESSION['flash_success'] = 'Assunto registado com sucesso!';
        header('Location: /assuntos');
        exit;
      } catch (ValidationException $e) {
        $errorMessage = $e->getMessage();
        $assunto = null;
        require __DIR__ . '/../Views/assuntos/form.php';
        return;
      }
    }

    $assunto = null;
    $description = '';
    require __DIR__ . '/../Views/assuntos/form.php';
  }

  public function edit(): void
  {
    $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

    try {
      $assunto = $this->model->findById($id);
      if (!$assunto) {
        throw new NotFoundException('Assunto não encontrado.');
      }

      $description = $assunto->getDescription();

      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $description = (string) ($_POST['description'] ?? '');
        $assunto->setDescription($description);
        $this->model->save($assunto);

        $_SESSION['flash_success'] = 'Assunto atualizado com sucesso!';
        header('Location: /assuntos');
        exit;
      }

      require __DIR__ . '/../Views/assuntos/form.php';
    } catch (ValidationException $e) {
      $errorMessage = $e->getMessage();
      require __DIR__ . '/../Views/assuntos/form.php';
    } catch (NotFoundException $e) {
      $_SESSION['flash_error'] = $e->getMessage();
      header('Location: /assuntos');
      exit;
    }
  }

  public function delete(): void
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $id = (int) ($_POST['id'] ?? 0);

      try {
        $this->model->delete($id);
        $_SESSION['flash_success'] = 'Assunto removido com sucesso!';
      } catch (EntityException $e) {
        $_SESSION['flash_error'] = $e->getMessage();
      } catch (NotFoundException $e) {
        $_SESSION['flash_error'] = $e->getMessage();
      } catch (Throwable $e) {
        $_SESSION['flash_error'] = 'Erro inesperado ao remover o assunto.';
      }
    }

    header('Location: /assuntos');
    exit;
  }
}