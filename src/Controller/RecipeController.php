<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    //this controller display all recipes
    #[IsGranted('ROLE_USER')]
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

    //controller showRecipe
    #[IsGranted('ROLE_USER')]
    #[Route('/recette/{id}', 'recipe.show', methods: ['GET'])]
    public function show(Recipe $recipe) : Response
        {
            if ($recipe-> getIsPublic() == true ) {
                $this->addFlash(
                    'warning',
                    'Cette recette n\'est pas publique.');
                    return $this->redirectToRoute('recipe.index');
            } else {
            return $this->render('pages/recipe/show.html.twig', [
                'recipe' => $recipe
                ]);
            }
        }

    // controller for a new recipe
    #[IsGranted('ROLE_USER')]
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
            //#[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]  déprécié condition à mettre dans le code
            #[IsGranted('ROLE_USER')]
            #[Route('/recipe/edition{id}', 'recipe.edit', methods: ['GET', 'POST'])]
            public function edit(
                recipe $recipe, 
                Request $request, 
                EntityManagerInterface $manager,
                ) :Response {
/*            //verifie si le user est connecté
                if (!$this->getUser()) {
                    return $this->redirectToRoute('security.login');
                }
        */
                //recuperer le user via la recipe / A affiner, ne marche pas mais la logique est pas loin
                if ($this->getUser() !== $recipe->getUser()) {
                    //return $this->redirectToRoute('security.login');
                    //mettre un message alert pour dire ceci n'est pas votre recette
                    $this->addFlash(
                        'warning',
                        'Vous essayez de modifier la recette de quelqu\'un d\'autre !!');
                        return $this->redirectToRoute('recipe.index');
                } else {

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
