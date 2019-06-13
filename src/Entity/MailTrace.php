<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MailTraceRepository")
 */
class MailTrace
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sender;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $receiver;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Mailbody;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subject;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="$mailtrace", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false,name="user_id")
     */
    private $user;

    /**
     * MailTrace constructor.
     * @param $sender
     * @param $receiver
     * @param $Mailbody
     * @param $subject
     */
    public function __construct($sender, $receiver, $Mailbody, $subject)
    {
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->Mailbody = $Mailbody;
        $this->subject = $subject;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function setSender(string $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): ?string
    {
        return $this->receiver;
    }

    public function setReceiver(string $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getMailbody(): ?string
    {
        return $this->Mailbody;
    }

    public function setMailbody(string $Mailbody): self
    {
        $this->Mailbody = $Mailbody;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }
}
