<?php

namespace App\Controller;

use App\Form\ContactFormType;
use App\DTO\ContactFormDTO;
use App\Event\ContactRequestEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    function contact(Request $request, MailerInterface $mailer, EventDispatcherInterface $dispatcher): Response
    {
        $data = new ContactFormDTO();

        $form = $this->createForm(ContactFormType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $dispatcher->dispatch(new ContactRequestEvent($data));
                $this->addFlash('success', 'Votre message a été envoyé avec succès !');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Une erreur est survenue lors de l\'envoi de votre message. Veuillez réessayer plus tard.');
            }

            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
