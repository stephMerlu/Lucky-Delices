<?php

namespace App\Entity;

use App\Repository\LikedRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikedRepository::class)]
class Liked
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'likeds')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'likeds')]
    #[ORM\JoinColumn(name: "recipe_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?Recipe $recipe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function isLikedByUser(User $user): bool
    {
        foreach ($this->likeds as $liked) {
            if ($liked->getUser() === $user) {
                return true;
            }
        }

        return false;
    }
}
