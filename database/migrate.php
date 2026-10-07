<?php

declare(strict_types=1);

$host = getenv('DB_HOST') ?: 'db';
// Na rede interna do Docker (host 'db'), o MySQL sempre escuta na porta 3306.
// O DB_PORT do .env geralmente é a porta mapeada no host externo (ex: 3307).
$port = getenv('DB_INTERNAL_PORT') ?: (($host === 'db') ? '3306' : (getenv('DB_PORT') ?: '3306'));
$dbname = getenv('DB_DATABASE') ?: (getenv('DB_NAME') ?: 'cadastro_livros');
$user = getenv('DB_ROOT_USER') ?: (getenv('DB_USER') ?: 'root');
$pass = getenv('DB_ROOT_PASSWORD') ?: (getenv('DB_PASS') ?: (getenv('DB_PASSWORD') ?: 'root'));

echo "=== Iniciando Migration Runner ===\n";
echo "Conectando em {$host}:{$port} (Database: {$dbname}, User: {$user})...\n";

try {
  $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  ]);

  // Garante que o banco exista
  $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
  $pdo->exec("USE `{$dbname}`;");

  // Tabela de controle de versão das migrations
  $pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        migration VARCHAR(255) NOT NULL UNIQUE,
        executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");

  // Busca migrations já executadas
  $stmt = $pdo->query("SELECT migration FROM migrations");
  $executed = $stmt->fetchAll(PDO::FETCH_COLUMN);

  // Escaneia a pasta de migrations
  $files = glob(__DIR__ . '/migrations/*.sql');
  sort($files);

  $count = 0;
  foreach ($files as $file) {
    $filename = basename($file);

    if (in_array($filename, $executed, true)) {
      continue;
    }

    echo "Executando: {$filename}... ";
    $sql = file_get_contents($file);

    // Remove comandos DELIMITER se existirem (DELIMITER é apenas da CLI do MySQL)
    $sql = preg_replace('/^\s*DELIMITER\s+.*$/mi', '', $sql);
    $sql = str_replace('$$', ';', $sql);

    $pdo->beginTransaction();
    try {
      $pdo->exec($sql);
      $insert = $pdo->prepare("INSERT INTO migrations (migration) VALUES (:migration)");
      $insert->execute([':migration' => $filename]);
      if ($pdo->inTransaction()) {
        $pdo->commit();
      }
      echo "OK\n";
      $count++;
    } catch (Throwable $e) {
      if ($pdo->inTransaction()) {
        $pdo->rollBack();
      }
      echo "FALHOU!\n";
      echo "Erro: " . $e->getMessage() . "\n";
      exit(1);
    }
  }

  if ($count === 0) {
    echo "Nenhuma nova migration pendente.\n";
  } else {
    echo "Sucesso! Total de {$count} migration(s) executada(s).\n";
  }

  // Se passou a flag --seed, executa os seeds
  if (in_array('--seed', $argv, true)) {
    echo "Executando Seeds...\n";
    $seedFiles = glob(__DIR__ . '/seeds/*.sql');
    foreach ($seedFiles as $seed) {
      $seedName = basename($seed);
      echo "Executando seed: {$seedName}... ";
      $sql = file_get_contents($seed);
      $pdo->exec($sql);
      echo "OK\n";
    }
  }

} catch (PDOException $e) {
  echo "Erro de conexão ao banco: " . $e->getMessage() . "\n";
  exit(1);
}