<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Entities\Livro;
use App\Domain\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

final class LivroTest extends TestCase
{
  public function testDeveInstanciarLivroCorretamente(): void
  {
    $livro = new Livro(
      id: 1,
      title: 'Design Patterns',
      publisher: 'Addison-Wesley',
      edition: 1,
      publicationYear: '1994',
      price: 280.50,
      authorIds: [1, 2],
      subjectIds: [1]
    );

    $this->assertSame('Design Patterns', $livro->getTitle());
    $this->assertSame(280.50, $livro->getPrice());
    $this->assertCount(2, $livro->getAuthorIds());
  }

  public function testNaoDevePermitirLivroSemAutor(): void
  {
    $this->expectException(ValidationException::class);
    $this->expectExceptionMessage('Selecione pelo menos um autor para o livro.');

    new Livro(
      id: null,
      title: 'Livro Isolado',
      publisher: 'Editora X',
      edition: 1,
      publicationYear: '2023',
      price: 50.00,
      authorIds: [],
      subjectIds: [1]
    );
  }

  public function testAnoDePublicacaoDeveTerQuatroDigitos(): void
  {
    $this->expectException(ValidationException::class);
    $this->expectExceptionMessage('O ano de publicação deve conter exatamente 4 dígitos numéricos.');

    new Livro(
      id: null,
      title: 'Livro Ano Invalido',
      publisher: 'Editora X',
      edition: 1,
      publicationYear: '99',
      price: 50.00,
      authorIds: [1],
      subjectIds: [1]
    );
  }
}