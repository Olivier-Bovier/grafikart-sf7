<?php

namespace App\Event;

use App\Entity\ContactRequest;
use Symfony\Contracts\EventDispatcher\Event;
use App\DTO\ContactFormDTO;

class ContactRequestEvent extends Event
{
    public function __construct(public readonly ContactFormDTO $contact)
    {}
}