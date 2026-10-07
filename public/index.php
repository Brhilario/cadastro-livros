<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\AutorController;
use App\Controllers\HomeController;
use App\Controllers\AssuntoController;
use App\Controllers\LivroController;
use App\Controllers\RelatorioController;
use App\Core\Router;

$router = new Router();

// Home
$router->get('/', [HomeController::class, 'index']);

// CRUD Autores
$router->get('/autores', [AutorController::class, 'index']);
$router->get('/autores/novo', [AutorController::class, 'create']);
$router->post('/autores/novo', [AutorController::class, 'create']);
$router->get('/autores/editar', [AutorController::class, 'edit']);
$router->post('/autores/editar', [AutorController::class, 'edit']);
$router->post('/autores/excluir', [AutorController::class, 'delete']);

// CRUD Assuntos
$router->get('/assuntos', [AssuntoController::class, 'index']);
$router->get('/assuntos/novo', [AssuntoController::class, 'create']);
$router->post('/assuntos/novo', [AssuntoController::class, 'create']);
$router->get('/assuntos/editar', [AssuntoController::class, 'edit']);
$router->post('/assuntos/editar', [AssuntoController::class, 'edit']);
$router->post('/assuntos/excluir', [AssuntoController::class, 'delete']);

// CRUD Livros
$router->get('/livros', [LivroController::class, 'index']);
$router->get('/livros/novo', [LivroController::class, 'create']);
$router->post('/livros/novo', [LivroController::class, 'create']);
$router->get('/livros/editar', [LivroController::class, 'edit']);
$router->post('/livros/editar', [LivroController::class, 'edit']);
$router->post('/livros/excluir', [LivroController::class, 'delete']);

// Rota do Relatório (VIEW)
$router->get('/relatorio', [RelatorioController::class, 'index']);

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$router->dispatch($method, $uri);