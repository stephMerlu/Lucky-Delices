<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $starter = null;

    #[ORM\Column(length: 255)]
    private ?string $dish = null;

    #[ORM\Column(length: 255)]
    private ?string $dessert = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStarter(): ?string
    {
        return $this->starter;
    }

    public function setStarter(string $starter): self
    {
        $this->starter = $starter;

        return $this;
    }

    public function getDish(): ?string
    {
        return $this->dish;
    }

    public function setDish(string $dish): self
    {
        $this->dish = $dish;

        return $this;
    }

    public function getDessert(): ?string
    {
        return $this->dessert;
    }

    public function setDessert(string $dessert): self
    {
        $this->dessert = $dessert;

        return $this;
    }
}
