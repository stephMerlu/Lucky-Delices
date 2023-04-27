<?php

namespace App\Entity;

use App\Repository\UserProfileRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserProfileRepository::class)]
class UserProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'userProfile')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;
    
    #[ORM\ManyToOne(targetEntity: UserProfile::class, inversedBy: 'userProfiles')]
    #[ORM\JoinColumn(name: 'parent_profile_id', referencedColumnName: 'id')]
    private ?UserProfile $parentProfile = null;

    #[ORM\OneToMany(targetEntity: UserProfile::class, mappedBy: 'parentProfile')]
    private Collection $userProfiles;

    public function __construct()
    {
        $this->userProfiles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
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

    public function getParentProfile(): ?self
    {
        return $this->parentProfile;
    }

    public function setParentProfile(?self $parentProfile): self
    {
        $this->parentProfile = $parentProfile;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getUserProfiles(): Collection
    {
        return $this->userProfiles;
    }

    public function addUserProfile(self $userProfile): self
    {
        if (!$this->userProfiles->contains($userProfile)) {
            $this->userProfiles[] = $userProfile;
            $userProfile->setParentProfile($this);
        }

        return $this;
    }

    public function removeUserProfile(self $userProfile): self
    {
        if ($this->userProfiles->contains($userProfile)) {
            $this->userProfiles->removeElement($userProfile);
            if ($userProfile->getParentProfile() === $this) {
                $userProfile->setParentProfile(null);
            }
        }

        return $this;
    }

    public function getUsername(): string
    {
        if ($this->getUser() instanceof User) {
            return $this->getUser()->getUsername();
        }
        return '';
    }

    public function getEmail(): ?string
{
    $user = $this->getUser();
    if ($user instanceof User) {
        return $user->getEmail();
    }
    return null;
}
}
