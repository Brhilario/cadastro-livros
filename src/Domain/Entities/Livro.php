<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\Exceptions\ValidationException;

final class Livro
{
  private ?int $id;
  private string $title;
  private string $publisher;
  private int $edition;
  private string $publicationYear;
  private float $price;
  /** @var int[] */
  private array $authorIds = [];
  /** @var int[] */
  private array $subjectIds = [];

  public function __construct(
    ?int $id,
    string $title,
    string $publisher,
    int $edition,
    string $publicationYear,
    float $price,
    array $authorIds = [],
    array $subjectIds = []
  ) {
    $this->id = $id;
    $this->setTitle($title);
    $this->setPublisher($publisher);
    $this->setEdition($edition);
    $this->setPublicationYear($publicationYear);
    $this->setPrice($price);
    $this->setAuthorIds($authorIds);
    $this->setSubjectIds($subjectIds);
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getTitle(): string
  {
    return $this->title;
  }

  public function setTitle(string $title): void
  {
    $trimmed = trim($title);
    if ($trimmed === '') {
      throw new ValidationException('O título do livro é obrigatório.');
    }
    if (mb_strlen($trimmed) > 40) {
      throw new ValidationException('O título não pode ter mais de 40 caracteres.');
    }
    $this->title = $trimmed;
  }

  public function getPublisher(): string
  {
    return $this->publisher;
  }

  public function setPublisher(string $publisher): void
  {
    $trimmed = trim($publisher);
    if ($trimmed === '') {
      throw new ValidationException('A editora é obrigatória.');
    }
    if (mb_strlen($trimmed) > 40) {
      throw new ValidationException('A editora não pode ter mais de 40 caracteres.');
    }
    $this->publisher = $trimmed;
  }

  public function getEdition(): int
  {
    return $this->edition;
  }

  public function setEdition(int $edition): void
  {
    if ($edition <= 0) {
      throw new ValidationException('A edição deve ser um número inteiro positivo.');
    }
    $this->edition = $edition;
  }

  public function getPublicationYear(): string
  {
    return $this->publicationYear;
  }

  public function setPublicationYear(string $year): void
  {
    $trimmed = trim($year);
    if (!preg_match('/^\d{4}$/', $trimmed)) {
      throw new ValidationException('O ano de publicação deve conter exatamente 4 dígitos numéricos.');
    }
    $this->publicationYear = $trimmed;
  }

  public function getPrice(): float
  {
    return $this->price;
  }

  public function setPrice(float $price): void
  {
    if ($price < 0) {
      throw new ValidationException('O valor do livro não pode ser negativo.');
    }
    $this->price = round($price, 2);
  }

  public function getAuthorIds(): array
  {
    return $this->authorIds;
  }

  public function setAuthorIds(array $authorIds): void
  {
    $cleaned = array_unique(array_filter(array_map('intval', $authorIds)));
    if (empty($cleaned)) {
      throw new ValidationException('Selecione pelo menos um autor para o livro.');
    }
    $this->authorIds = $cleaned;
  }

  public function getSubjectIds(): array
  {
    return $this->subjectIds;
  }

  public function setSubjectIds(array $subjectIds): void
  {
    $cleaned = array_unique(array_filter(array_map('intval', $subjectIds)));
    if (empty($cleaned)) {
      throw new ValidationException('Selecione pelo menos um assunto para o livro.');
    }
    $this->subjectIds = $cleaned;
  }
}