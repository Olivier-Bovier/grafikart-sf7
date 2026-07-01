<?php

namespace App\Controller\admin;

use App\Entity\Recipe;
use App\Entity\User;
use App\Form\RecipeType;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use App\Security\Voter\RecipeVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Turbo\TurboBundle;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

#[Route("/admin/recettes", name: 'admin.recipe.')]
final class RecipeController extends AbstractController
{
    #[Route('/', name: 'index')]
    #[IsGranted(RecipeVoter::LIST)]
    public function index(RecipeRepository $repository, Request $request): Response
    {
        //$recipes = $repository->findAll();
        //$recipes = $repository->findWithDurationLowerThan(20);

        $page = $request->query->getInt('page', 1);
        /** @var User $user */
        $user = $this->getUser();
        $userId = $user->getId();
        $canListAll = $this->isGranted(RecipeVoter::LIST_ALL);

        $recipes = $repository->paginateRecipes($page, $canListAll ? null : $userId);

        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/create', name: 'create')]
    #[IsGranted(RecipeVoter::CREATE)]
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
    #[IsGranted(RecipeVoter::EDIT, subject: 'recipe')]
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
    #[IsGranted(RecipeVoter::EDIT, subject: 'recipe')]
    public function remove(Request $request, Recipe $recipe, EntityManagerInterface $em): Response
    {
        $recipeId = $recipe->getId();
        $message = 'La recette a été supprimée avec succès !';
        $em->remove($recipe);
        $em->flush();

        if ($request->getPreferredFormat() === TurboBundle::STREAM_FORMAT) {
            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
            return $this->render('admin/recipe/delete.html.twig', ['recipeId' => $recipeId, 'messages' => $message]);
        }

        $this->addFlash('success', $message);

        return $this->redirectToRoute('admin.recipe.index');
    }
}
