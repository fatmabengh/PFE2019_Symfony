<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResponseRepository")
 */
class Response
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Response constructor.
     * @param $form
     * @param $resp
     */
    public function __construct($form, $content)
    {
        $this->form = $form;
        $this->content = $content;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

     /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Form", inversedBy="$response", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false,name="form_id")
     */
    private $form;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $dateResp;

    /**
     * @return mixed
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param mixed $form
     */
    public function setForm($form): void
    {
        $this->form = $form;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getDateResp(): ?string
    {
        return $this->dateResp;
    }

    public function setDateResp(?string $dateResp): self
    {
        $this->dateResp = $dateResp;

        return $this;
    }


}
