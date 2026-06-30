<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Event\ContactRequestEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class MailingSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MailerInterface $mailer) {}

    public function onContactRequestEvent(ContactRequestEvent $event): void
    {
        $data = $event->contact;

        $email = (new TemplatedEmail())
            ->from($data->getEmail())
            ->to($data->getService())
            ->subject('Demande de contact')
            ->htmlTemplate('emails/contact.html.twig')
            ->context(['contactFormDTO' => $data]);

        $this->mailer->send($email);
    }

    public function onLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        if (!$user instanceof User) {
            return;
        }

        $email = (new Email())
            ->from('support@demo.fr')
            ->to($user->getEmail())
            ->subject('Connexion')
            ->text('Vous vous êtes connecté avec succès !');

        $this->mailer->send($email);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContactRequestEvent::class => 'onContactRequestEvent',
            InteractiveLoginEvent::class => 'onLogin',
        ];
    }
}
