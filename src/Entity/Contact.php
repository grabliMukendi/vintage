<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContactRepository")
 */
class Contact 
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank(message="Veuillez entrer votre nom.")
     * @var string
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank(message="Veuillez entrer votre prénom.")
     * @var string
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Veuillez remplir ce champs.")
     * @Assert\Email(checkMX="true", message="Veuillez entrer un email valide.")
     * @var string
     */
    private $email;
    
    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @var string
     */
    private $entreprise;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @var string
     */
    private $fonction;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Veuillez entrer un numéro de téléphone.")
     * @Assert\Regex(
     *      pattern="#^[0-9]{9,10}$#",
     *      message="Veuillez entrer un numéro de téléphone valide."
     * )
     * @var string
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=250)
     * @var string
     */
    private $produit;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Veuillez saisir un message.")
     * @Assert\Length(min="20", minMessage="Votre message ne peut avoir moins de 20 caractères.")
     * @var string
     */
    private $message;

    /**
     * Get the value of nom
     *
     * @return  string
     */ 
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set the value of nom
     *
     * @param  string  $nom
     *
     * @return  self
     */ 
    public function setNom(string $nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get the value of prenom
     *
     * @return  string
     */ 
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set the value of prenom
     *
     * @param  string  $prenom
     *
     * @return  self
     */ 
    public function setPrenom(string $prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get the value of email
     *
     * @return  string
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @param  string  $email
     *
     * @return  self
     */ 
    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of entreprise
     *
     * @return  string
     */ 
    public function getEntreprise()
    {
        return $this->entreprise;
    }

    /**
     * Set the value of entreprise
     *
     * @param  string  $entreprise
     *
     * @return  self
     */ 
    public function setEntreprise(string $entreprise)
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    /**
     * Get the value of fonction
     *
     * @return  string
     */ 
    public function getFonction()
    {
        return $this->fonction;
    }

    /**
     * Set the value of fonction
     *
     * @param  string  $fonction
     *
     * @return  self
     */ 
    public function setFonction(string $fonction)
    {
        $this->fonction = $fonction;

        return $this;
    }

    /**
     * Get )
     *
     * @return  integer
     */ 
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set )
     *
     * @param  integer  $telephone  )
     *
     * @return  self
     */ 
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get the value of produit
     *
     * @return  string
     */ 
    public function getProduit()
    {
        return $this->produit;
    }

    /**
     * Set the value of produit
     *
     * @param  string  $produit
     *
     * @return  self
     */ 
    public function setProduit(string $produit)
    {
        $this->produit = $produit;

        return $this;
    }

    /**
     * Get the value of message
     *
     * @return  string
     */ 
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the value of message
     *
     * @param  string  $message
     *
     * @return  self
     */ 
    public function setMessage(string $message)
    {
        $this->message = $message;

        return $this;
    }

}