<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;
    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];
    /**
     * @var string Le mot de passe crypté
     * @ORM\Column(type="string")
     */
    private $password;
    /**
     * @ORM\Column(type="string", name="nomComplet",length=255)
     */
    private $nomComplet;
    /**
     * @var string le token qui servira lors de l'oubli de mot de passe
     * @ORM\Column(type="string",name="resetToken", length=255, nullable=true)
     */
    protected $resetToken;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Form", mappedBy="user")
     */
    private $forms;

    /**
     * @return Collection|Forms[]
     */
    public function getForms() :Collection
    {
        return $this->forms;
    }
    public function getId(): ?int
    {
        return $this->id;
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
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }
    /**
     * @see UserInterface
     */
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
    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }
    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }
    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    public function getNomComplet(): ?string
    {
        return $this->nomComplet;
    }
    public function setNomComplet(string $nomComplet): self
    {
        $this->nomComplet = $nomComplet;
        return $this;
    }
    /**
     * @return string
     */
    public function getResetToken(): string
    {
        return $this->resetToken;
    }
    /**
     * @param string $resetToken
     */
    public function setResetToken(?string $resetToken): void
    {
        $this->resetToken = $resetToken;
    }

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MailTrace", mappedBy="user")
     */
    private $mailTraces;

    /**
     * @return mixed
     */
    public function getMailTraces()
    {
        return $this->mailTraces;
    }

    /**
     * @param mixed $mailTraces
     */
    public function setMailTraces($mailTraces): void
    {
        $this->mailTraces = $mailTraces;
    }
}