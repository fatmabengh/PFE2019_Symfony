<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategorieRepository")
 */
class Categorie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name_categorie;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameCategorie(): ?string
    {
        return $this->name_categorie;
    }

    public function setNameCategorie(?string $name_categorie): self
    {
        $this->name_categorie = $name_categorie;

        return $this;
    }

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Form", mappedBy="form_id")
     */
    private $form;
}
