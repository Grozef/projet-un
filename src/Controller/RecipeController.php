<?php

namespace App\Controller;

use App\Form\RecipeType;
use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
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
            $repository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1), 
            10 
        );

        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }
        // controller for a new recipe
    #[Route('/recette/creation', name: 'recipe.new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $manager,
        ) : Response {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form-> handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe= $form->getData();
            $recipe->setUser($this->getUser());

            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash(
                'success',
                'Vous avez bien ajouté votre recette !'
            );

            return $this->redirectToRoute('recipe.index');

        }

        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

            //controller wich allow us to edit a recipe

            #[Route('/recipe/edition{id}', 'recipe.edit', methods: ['GET', 'POST'])]
            public function edit(
                recipe $recipe, 
                Request $request, 
                EntityManagerInterface $manager
                ) :Response {
                $form = $this->createForm(RecipeType::class, $recipe);
                $form->handleRequest($request);
        
                if ($form->isSubmitted() && $form->isValid()) {
                    $manager->flush();
                    $this->addFlash(
                        'success',
                        'Votre recette a été modifié avec succés !'
                    );
        
                    return $this->redirectToRoute('recipe.index');
                }
        
                return $this->render('pages/recipe/edit.html.twig', [
                    'form' => $form->createView()
                ]);
            }
        
                //controller wich allow to delete a recipe
        
            #[Route('/recipe/suppression/{id}', 'recipe.delete', methods: ['GET'])]
            public function delete(
                EntityManagerInterface $manager, 
                recipe $recipe
                ) : Response {
        
                $manager->remove($recipe);
                $manager->flush();
        
                $this->addFlash(
                    'success',
                    'Votre recette a été supprimé avec succés !'
                    //faire une confirmation de suppression
                );
        
                return $this->redirectToRoute('recipe.index');
        
            }
        

}
