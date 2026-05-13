<?php

namespace App\Controller\admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

#[Route("/admin/recettes", name: 'admin.recipe.')]
final class RecipeController extends AbstractController
{
    #[Route('/', name: 'index')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(RecipeRepository $repository, CategoryRepository $categoryRepository, EntityManagerInterface $em): Response
    {
        //$recipes = $repository->findAll();
        $recipes = $repository->findWithDurationLowerThan(20);

        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'La recette a été créée avec succès !');

            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('admin/recipe/create.html.twig', [
            'form'   => $form
        ]);
    }

    #[Route('/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => requirement::DIGITS])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em, UploaderHelper $uploaderHelper): Response
    {
        //dd($uploaderHelper->asset($recipe, 'thumbnailFile'));    
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'La recette a été modifiée avec succès !');

            return $this->redirectToRoute('admin.recipe.index');
        }


        return $this->render('admin/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form'   => $form
        ]);
    }

    #[Route('/{id}', name: 'remove', methods: ['DELETE'], requirements: ['id' => requirement::DIGITS])]
    public function remove(Recipe $recipe, EntityManagerInterface $em): Response
    {
        $em->remove($recipe);
        $em->flush();
        $this->addFlash('success', 'La recette a été supprimée avec succès !');

        return $this->redirectToRoute('admin.recipe.index');
    }
}
