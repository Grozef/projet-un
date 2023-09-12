<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IngredientController extends AbstractController

    //this controller displays all ingredients

{
    #[Route('/ingredient', name: 'app_ingredient', methods: ['GET'])]
    //annotation pour restreindre la fonction uniquement aux ROLE_USER
    #[IsGranted('ROLE_USER')]

    public function index(IngredientRepository $repository, 
    PaginatorInterface $paginator, 
    Request $request
    ): Response{
        $ingredients = $paginator->paginate(
            $repository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1), 
            10 
        );

        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients
        ]);
    }

    //This controller show a form wich create an ingredient

    //#[Route('/ingredient', name: 'ingredient.index', methods: ['GET'])]
    #[Route('/ingredient/nouveau', 'ingredient.new', methods: ['GET', 'POST'])]
    //annotation pour restreindre la fonction uniquement aux ROLE_USER
    #[IsGranted('ROLE_USER')]
    public function new(
        Request $request,
        EntityManagerInterface $manager,

        ) : Response {
            $ingredient = new Ingredient ();
            $form = $this->createForm(IngredientType::class, $ingredient);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form ->isValid()) {
                $ingredient = $form ->getData();
                $ingredient->setUser($this->getUser());

                $manager->persist($ingredient);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Vous avez bien ajouté votre ingredient !'
                );

                /*return $this->redirectToRoute('app_ingredient'); */
                 return $this->redirectToRoute('ingredient.index'); 
            }

        return $this ->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView()
        ]);

    }
    
        //controller wich allow us to modify an ingredient

    // Sécurité pour verifier que l'utilisateur a bien un role et que l'ingredient lui appartienne   
    //#[Security("is_granted('ROLE_USER') and user === ingredient.getUser()")]
    #[Route('/ingredient/edition{id}', 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(
        /*IngredientRepository $repository, int $id*/ 
        Ingredient $ingredient, 
        Request $request, 
        EntityManagerInterface $manager
        ) :Response {
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre ingredient a été modifié avec succés !'
            );

            return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

        //controller wich allow to delete an ingredient

    #[Route('/ingredient/suppression/{id}', 'ingredient.delete', methods: ['GET'])]
    public function delete(
        EntityManagerInterface $manager, 
        Ingredient $ingredient
        ) : Response {

        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre ingredient a été supprimé avec succés !'
            //faire une confirmation de suppression
        );

        return $this->redirectToRoute('ingredient.index');

    }

}
