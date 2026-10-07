<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\AutorModel;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

final class AutorTest extends TestCase
{
  public function testDeveObterResumoDoAutorViaProcedureCorretamente(): void
  {
    $stmtMock = $this->createMock(PDOStatement::class);
    $stmtMock->expects($this->once())
      ->method('execute')
      ->with([':id' => 1])
      ->willReturn(true);

    $stmtMock->expects($this->once())
      ->method('fetch')
      ->with(PDO::FETCH_ASSOC)
      ->willReturn([
        'autor_id' => '1',
        'autor_nome' => 'Martin Fowler',
        'total_livros' => '1',
        'valor_total_acervo' => '215.50',
        'preco_medio_livro' => '215.500000',
      ]);

    $stmtMock->expects($this->once())
      ->method('closeCursor')
      ->willReturn(true);

    $pdoMock = $this->createMock(PDO::class);
    $pdoMock->expects($this->once())
      ->method('prepare')
      ->with('CALL sp_obter_resumo_autor(:id)')
      ->willReturn($stmtMock);

    $model = new AutorModel($pdoMock);
    $resumo = $model->getResumoPorAutor(1);

    $this->assertNotNull($resumo);
    $this->assertSame(1, $resumo['autor_id']);
    $this->assertSame('Martin Fowler', $resumo['autor_nome']);
    $this->assertSame(1, $resumo['total_livros']);
    $this->assertSame(215.50, $resumo['valor_total_acervo']);
    $this->assertSame(215.50, $resumo['preco_medio_livro']);
  }

  public function testDeveRetornarNullQuandoAutorNaoEncontradoNaProcedure(): void
  {
    $stmtMock = $this->createMock(PDOStatement::class);
    $stmtMock->expects($this->once())
      ->method('execute')
      ->with([':id' => 999])
      ->willReturn(true);

    $stmtMock->expects($this->once())
      ->method('fetch')
      ->with(PDO::FETCH_ASSOC)
      ->willReturn(false);

    $stmtMock->expects($this->once())
      ->method('closeCursor')
      ->willReturn(true);

    $pdoMock = $this->createMock(PDO::class);
    $pdoMock->expects($this->once())
      ->method('prepare')
      ->with('CALL sp_obter_resumo_autor(:id)')
      ->willReturn($stmtMock);

    $model = new AutorModel($pdoMock);
    $resumo = $model->getResumoPorAutor(999);

    $this->assertNull($resumo);
  }
}
