<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FieldsRepository")
 */
class Fields
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subtitle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $types;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $items;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Form", inversedBy="$fields", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false,name="form_id")
     */
    private $form;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $obligation;

    /**
     * Fields constructor.
     * @param $label
     * @param $subtitle
     * @param $types
     */
    public function __construct($label, $subtitle, $types)
    {
        $this->label = $label;
        $this->subtitle = $subtitle;
        $this->types = $types;
    }


    public function getForm(): ?Form
    {
        return $this->form;
    }

    public function setForm(?Form $form): void
    {
        $this->form = $form;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getTypes(): ?string
    {
        return $this->types;
    }

    public function setTypes(string $types): self
    {
        $this->types = $types;

        return $this;
    }

    public function getItems(): ?string
    {
        return $this->items;
    }

    public function setItems(?string $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function getObligation(): ?bool
    {
        return $this->obligation;
    }

    public function setObligation(?bool $obligation): self
    {
        $this->obligation = $obligation;

        return $this;
    }
}
