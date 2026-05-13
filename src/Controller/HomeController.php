<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]    
    function index (Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, Security $security): Response {
/*         $user = new User();
        $user->setEmail('john@doe.fr')
            ->setUsername('JohnDoe')
            ->setPassword($hasher->hashPassword($user, 'password'))
            ->setRoles([]);

        $em->persist($user);
        $em->flush();  */

        //dd($security->getUser());
        //dd($security->getToken());

        return $this->render('home/index.html.twig');
    }
}

