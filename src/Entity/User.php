<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $discordId = null;

    #[ORM\Column(length: 500)]
    private ?string $avatarHash = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $username = null;


    #[ORM\Column(type: 'json')] // Utilisation du type JSON pour stocker les rôles
    private array $roles = []; // Initialiser avec un tableau vide ou un tableau avec des rôles par défaut

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiscordId(): ?string
    {
        return $this->discordId;
    }

    public function setDiscordId(string $discordId): static
    {
        $this->discordId = $discordId;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getAvatarHash(): ?string
    {
        return $this->avatarHash;
    }

    public function setAvatarHash(string $avatarHash): static
    {
        $this->avatarHash = $avatarHash;

        return $this;
    }

    public function getRoles(): array
    {
        // S'assurer que l'utilisateur a toujours au moins ROLE_USER
        $roles = $this->roles;
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role): void
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
    }

    public function eraseCredentials(): void
    {
        // Si vous avez des données sensibles, comme un mot de passe en clair, effacez-le ici
    }

    public function getUserIdentifier(): string
    {
        return $this->discordId;
    }
}