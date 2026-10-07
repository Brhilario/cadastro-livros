<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\Exceptions\ValidationException;

final class Autor
{
  private ?int $id;
  private string $name;

  public function __construct(?int $id, string $name)
  {
    $this->id = $id;
    $this->setName($name);
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function setName(string $name): void
  {
    $trimmed = trim($name);
    if ($trimmed === '') {
      throw new ValidationException('O nome do autor é obrigatório.');
    }

    if (mb_strlen($trimmed) > 40) {
      throw new ValidationException('O nome do autor não pode exceder 40 caracteres.');
    }

    $this->name = $trimmed;
  }

  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'name' => $this->name,
    ];
  }
}