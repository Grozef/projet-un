<?php

namespace App\Controller;

use App\Form\RecipeType;
use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    //this controller display all recipes
    #[Route('/recette', name: 'recipe.index', methods: ['GET'])]
    public function index(
        RecipeRepository $repository, 
        PaginatorInterface $paginator, 
        Request $request
        ): Response{
        $recipes = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1), 
            10 
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recette/creation', name: 'recipe.new', methods: ['GET', 'POST'])]
    public function new() : Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
