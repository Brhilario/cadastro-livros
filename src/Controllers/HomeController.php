<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use PDOException;

final class HomeController
{
  public function index(): void
  {
    $dbStatus = 'Desconectado';
    try {
      $pdo = Database::getConnection();
      $stmt = $pdo->query("SELECT VERSION()");
      $version = $stmt->fetchColumn();
      $dbStatus = "Conectado (MySQL {$version})";
    } catch (PDOException $e) {
      $dbStatus = "Erro na ligação: " . $e->getMessage();
    }

    echo "<!DOCTYPE html>
        <html lang='pt-BR'>
        <head>
            <meta charset='UTF-8'>
            <title>Sistema de Gestão de Livros</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>
        </head>
        <body class='bg-light'>
            <div class='container py-5'>
                <div class='card shadow-sm p-4'>
                    <h1 class='h3 mb-3 text-primary'>Gestão de Livros</h1>
                    <p class='text-muted'>Arquitetura nativa em PHP 8.2 sem recurso a frameworks.</p>
                    <div class='alert alert-info'><strong>Estado da BD:</strong> {$dbStatus}</div>
                    <div class='list-group mt-3'>
                        <a href='/autores' class='list-group-item list-group-item-action'>Gerir Autores</a>
                        <a href='/assuntos' class='list-group-item list-group-item-action'>Gerir Assuntos</a>
                        <a href='/livros' class='list-group-item list-group-item-action'>Gerir Livros</a>
                        <a href='/relatorio' class='list-group-item list-group-item-action list-group-item-warning'>Relatório Agrupado por Autor</a>
                    </div>
                </div>
            </div>
        </body>
        </html>";
  }
}