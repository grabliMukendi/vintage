<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *      fields={"username"},
 *      message="Désolé, ce nom d'utilisateur est déjà utilisé.")
 * @UniqueEntity(
 *      fields={"email"},
 *      message="Désolé, cet email est déjà utilisé.")
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank(message="Veuillez saisir votre nom d'utilisateur.")
     * @Assert\Regex(
     *      pattern="#^[\w.-]{3,20}$#", 
     *      message="Votre nom d'utilisateur doit comporter entre 3 et 20 caractères (a à z, A à Z, 0 à 9 et .,-,_).")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez saisir votre mot de passe.")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank(message="Veuillez remplir ce champs.")
     * @Assert\Regex(
     *      pattern="~^[a-zA-ZÀ-ÖØ-öø-ÿœŒ ]+$~u", 
     *      message="Votre nom doit comporter entre 3 et 20 caractères.")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank(message="Veuillez remplir ce champs.")
     * @Assert\Regex(
     *      pattern="~^[a-zA-ZÀ-ÖØ-öø-ÿœŒ ]+$~u", 
     *      message="Votre prénom doit comporter entre 3 et 20 caractères.")
     */
    private $prenom;
    
    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Veuillez remplir ce champs.")
     * @Assert\Email(checkMX="true", message="Veuillez entrer un email valide.")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $role = 'ROLE_USER'; // !IMPORTANT

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastLogin;

    /**
     * @ORM\Column(type="datetime")
     */
    private $registeredAt;

    /**
     * Not ORM\Property
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $civilite;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commandes", mappedBy="user", cascade={"persist", "remove"})
     */
    private $commandes;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserAdresses", mappedBy="user", cascade={"persist", "remove"})
     */
    private $userAdress;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comments", mappedBy="author", orphanRemoval=true)
     */
    private $comments;
    
    /**
     * @ORM\PrePersist
     * @return void
     */
    public function prePersist() 
    {
        if(empty($this->lastLogin)) 
        {
            $this->lastLogin = new \DateTime;
        }
        if(empty($this->registeredAt)) 
        {
            $this->registeredAt = new \DateTime;
        }
        
    }

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getRoles(): array
    {
        // guarantee every user at least has ROLE_USER
        $roles[] = $this->role;

        return array_unique($roles);
    }
    public function getSalt(){}
    public function eraseCredentials(){}

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(\DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getRegisteredAt(): ?\DateTimeInterface
    {
        return $this->registeredAt;
    }

    public function setRegisteredAt(\DateTimeInterface $registeredAt): self
    {
        $this->registeredAt = $registeredAt;

        return $this;
    }

    public function getPlainPassword() 
    {
        return $this->plainPassword;
    }
    public function setPlainPassword($plainPassword) 
    {
        return $this->plainPassword = $plainPassword;

    }

    public function getCivilite(): ?string
    {
        return $this->civilite;
    }

    public function setCivilite(string $civilite): self
    {
        $this->civilite = $civilite;

        return $this;
    }

    /**
     * @return Collection|Commandes[]
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommandes(Commandes $commandes): self
    {
        if (!$this->commandes->contains($commandes)) {
            $this->commandes[] = $commandes;
            $commandes->setUser($this);
        }

        return $this;
    }

    public function removeCommandes(Commandes $commandes): self
    {
        if ($this->commandes->contains($commandes)) {
            $this->commandes->removeElement($commandes);
            // set the owning side to null (unless already changed)
            if ($commandes->getUser() === $this) {
                $commandes->setUser(null);
            }
        }

        return $this;
    }

    public function getUserAdress(): ?UserAdresses
    {
        return $this->userAdress;
    }

    public function setUserAdress(?UserAdresses $userAdress): self
    {
        $this->userAdress = $userAdress;

        // set (or unset) the owning side of the relation if necessary
        $newUser = null === $userAdress ? null : $this;
        if ($userAdress->getUser() !== $newUser) {
            $userAdress->setUser($newUser);
        }

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }
    
}
