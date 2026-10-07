<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\Exceptions\ValidationException;

final class Assunto
{
  private ?int $id;
  private string $description;

  public function __construct(?int $id, string $description)
  {
    $this->id = $id;
    $this->setDescription($description);
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getDescription(): string
  {
    return $this->description;
  }

  public function setDescription(string $description): void
  {
    $trimmed = trim($description);
    if ($trimmed === '') {
      throw new ValidationException('A descrição do assunto é obrigatória.');
    }

    if (mb_strlen($trimmed) > 20) {
      throw new ValidationException('A descrição do assunto não pode exceder 20 caracteres.');
    }

    $this->description = $trimmed;
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'description' => $this->description,
    ];
  }
}