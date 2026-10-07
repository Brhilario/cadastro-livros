<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

final class Database
{
  private static ?PDO $instance = null;

  private function __construct()
  {
  }

  private function __clone()
  {
  }

  public static function getConnection(): PDO
  {
    if (self::$instance === null) {
      $host = getenv('DB_HOST') ?: 'db';
      // Na rede interna do Docker (host 'db'), a porta do MySQL é 3306.
      // O DB_PORT no .env geralmente é a porta mapeada para a máquina host (ex: 3307).
      $port = ($host === 'db') ? '3306' : (getenv('DB_PORT') ?: '3306');
      $dbname = getenv('DB_DATABASE') ?: 'cadastro_livros';
      $user = getenv('DB_USERNAME') ?: 'user';
      $pass = getenv('DB_PASSWORD') ?: '';

      $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

      $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
      ];

      try {
        self::$instance = new PDO($dsn, $user, $pass, $options);
      } catch (PDOException $e) {
        // Em produção, nunca exponha dados de conexão diretamente
        throw new PDOException("Falha na ligação à base de dados: " . $e->getMessage(), (int) $e->getCode());
      }
    }

    return self::$instance;
  }
}