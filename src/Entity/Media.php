<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Recipe;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[UniqueEntity(
    fields: ['user', 'recipe'],
)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 150)]
    private ?string $name;

    #[ORM\Column(type: 'string', length: 150)]
    private ?string $chemin;

    #[ORM\Column(type: 'string', length: 150)]
    private ?string $taille;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $extension;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeImmutable $createdAt;

    #[ORM\ManyToOne( targetEntity: User::class, inversedBy: 'media')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user;

    #[ORM\ManyToOne(targetEntity: Recipe::class, inversedBy: 'media')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Recipe $recipe;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

       // Getter et setter pour la propriété 'chemin'
   public function getChemin(): ?string
   {
       return $this->chemin;
   }

   public function setChemin(?string $chemin): static
   {
       $this->chemin = $chemin;

       return $this;
   }

   public function getExtension()
   {
       return $this->extension;
   }

   public function setExtension($extension)
   {
       $this->extension = $extension;

       return $this;
   }

   public function getCreatedAt(): ?\DateTimeImmutable
   {
       return $this->createdAt;
   }

   public function setCreatedAt(\DateTimeImmutable $createdAt): static
   {
       $this->createdAt = $createdAt;

       return $this;
   }

   public function __construct()
   {
       $this->createdAt = new \DateTimeImmutable();
   }

   public function getUser(): ?User
   {
       return $this->user;
   }

   public function setUser(?User $user): static
   {
       $this->user = $user;

       return $this;
   }

   public function getRecipe(): ?Recipe
   {
       return $this->recipe;
   }

   public function setRecipe(?Recipe $recipe): static
   {
       $this->recipe = $recipe;

       return $this;
   }


   
}