<?php

namespace App\Domain\User;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'users')]
final class UserEntity
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[Column(type: 'string', length: 64, unique: true)]
    private string $name;

    #[Column(type: 'string', length: 256, unique: true)]
    private string $email;

    #[Column(type: 'text', nullable: true)]
    private string $notes;

    #[Column(name: 'created', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $created;

    #[Column(name: 'deleted', type: 'datetimetz_immutable', nullable: true)]
    private DateTimeImmutable $deleted;

    public function __construct()
    {
        $this->created = new DateTimeImmutable('now');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): UserEntity
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): UserEntity
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): UserEntity
    {
        $this->email = $email;
        return $this;
    }
}