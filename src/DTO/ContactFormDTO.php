<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints as Assert;

class ContactFormDTO
{
    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    #[Assert\Length(min: 3, max: 255, minMessage: 'L\'email doit avoir au moins {{ limit }} caractères', maxMessage: 'L\'email ne doit pas dépasser {{ limit }} caractères')]
    #[Assert\Email(message: 'L\'email doit être valide')]
    private string $email = '';

    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    #[Assert\Length(min: 3, max: 255, minMessage: 'Le nom doit avoir au moins {{ limit }} caractères', maxMessage: 'Le nom ne doit pas dépasser {{ limit }} caractères')]
    private string $name = '';

    #[Assert\NotBlank(message: 'Le service est obligatoire')]
    private string $service = '';

    #[Assert\NotBlank(message: 'Le message est obligatoire')]
    #[Assert\Length(min: 3, max: 2500, minMessage: 'Le message doit avoir au moins {{ limit }} caractères', maxMessage: 'Le message ne doit pas dépasser {{ limit }} caractères')]
    private string $message = '';


    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }   

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function setService(string $service): static
    {
        $this->service = $service;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }   
}