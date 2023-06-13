<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $fullName = null;

    #[ORM\Column(length: 255)]
    private ?string $userName = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $birthdate = null;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Publication::class)]
    private Collection $id_publication;

    public function __construct()
    {
        $this->id_publication = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getfullName(): ?string
    {
        return $this->fullName;
    }

    public function setfullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getuserName(): ?string
    {
        return $this->userName;
    }

    public function setuserName(string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getBirthdate(): ?string
    {
        return $this->birthdate;
    }

    public function setBirthdate(?string $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }


    public function getSalt(): ?string
    {
        return null;
    }


    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Publication>
     */
    public function getIdPublication(): Collection
    {
        return $this->id_publication;
    }

    public function addIdPublication(Publication $idPublication): self
    {
        if (!$this->id_publication->contains($idPublication)) {
            $this->id_publication->add($idPublication);
            $idPublication->setUser($this);
        }

        return $this;
    }

    public function removeIdPublication(Publication $idPublication): self
    {
        if ($this->id_publication->removeElement($idPublication)) {
            // set the owning side to null (unless already changed)
            if ($idPublication->getUser() === $this) {
                $idPublication->setUser(null);
            }
        }

        return $this;
    }


}
