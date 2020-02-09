<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProduitsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Produits
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Veuillez remplir ce champs.")
     */
    private $titre;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Veuillez remplir ce champs.")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $public;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="Veuillez remplir ce champs.")
     */
    private $prix;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Veuillez remplir ce champs.")
     */
    private $disponible;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tva", inversedBy="produits", cascade={"persist", "remove"})
     */
    private $tva;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Couleur", inversedBy="produits", cascade={"persist", "remove"})
     */
    private $couleur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Taille", inversedBy="produits", cascade={"persist", "remove"})
     */
    private $taille;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Media", mappedBy="produits", cascade={"persist", "remove"})
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comments", mappedBy="produit", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Categories", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $categorie;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $rubrique;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Promotion", cascade={"persist", "remove"})
     */
    private $promotion;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function initializeSlug() 
    {
        if($this->slug === null) 
        {
            $this->slug = $this->titre;
        }
    }

    public function __construct()
    {
        $this->image = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

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

    public function getPublic(): ?string
    {
        return $this->public;
    }

    public function setPublic(string $public): self
    {
        $this->public = $public;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDisponible()
    {
        return $this->disponible;
    }

    public function setDisponible($disponible): self
    {
        $this->disponible = $disponible;

        return $this;
    }

    public function getTva(): ?Tva
    {
        return $this->tva;
    }

    public function setTva(?Tva $tva): self
    {
        $this->tva = $tva;

        return $this;
    }

    public function getCouleur(): ?Couleur
    {
        return $this->couleur;
    }

    public function setCouleur(?Couleur $couleur): self
    {
        $this->couleur = $couleur;

        return $this;
    }

    public function getTaille(): ?Taille
    {
        return $this->taille;
    }

    public function setTaille(?Taille $taille): self
    {
        $this->taille = $taille;

        return $this;
    }

    public function getSlug(): ?string
    {
        return str_replace(' ', '-', $this->slug);
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Media[]
     */
    public function getImage(): Collection
    {
        return $this->image;
    }

    public function addImage(Media $image): self
    {
        if (!$this->image->contains($image)) {
            $this->image[] = $image;
            $image->setProduits($this);
        }

        return $this;
    }

    public function removeImage(Media $image): self
    {
        if ($this->image->contains($image)) {
            $this->image->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getProduits() === $this) {
                $image->setProduits(null);
            }
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
            $comment->setProduit($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getProduit() === $this) {
                $comment->setProduit(null);
            }
        }

        return $this;
    }

    public function getCategorie(): ?Categories
    {
        return $this->categorie;
    }

    public function setCategorie(Categories $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getRubrique(): ?string
    {
        return $this->rubrique;
    }

    public function setRubrique(string $rubrique): self
    {
        $this->rubrique = $rubrique;

        return $this;
    }

    public function getPromotion(): ?Promotion
    {
        return $this->promotion;
    }

    public function setPromotion(?Promotion $promotion): self
    {
        $this->promotion = $promotion;

        return $this;
    }

    /**
     * Permet de récupérer le commentaire d'un auteur par rapport à un produit
     * @param User $author
     * @return Comment | null
     */
    public function getCommentFromAuthor(User $author) 
    {
        foreach($this->comments as $comment) 
        {
            if($comment->getAuthor() === $author) return $comment;
        }

        return null;
    }

    /**
     * Cette méthode permet de calculer la moyenne des notes
     * @return float
     */
    public function getAvgRatings() 
    {
        $sum = array_reduce($this->comments->toArray(), function($total, $comment) 
        {
            return $total + $comment->getRating();
        }, 0);

        if(count($this->comments) != null) {
            return $sum / count($this->comments);
        } else {
            return 0;
        }
    }
    
}
