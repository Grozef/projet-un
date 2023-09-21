<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//déprécié
//use Symfony\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class IngredientController extends AbstractController

    //this controller displays all ingredients

{
    //annotation pour restreindre la fonction uniquement aux ROLE_USER
    #[IsGranted('ROLE_USER')]
    #[Route('/ingredient', name: 'ingredient.index', methods: ['GET'])]
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
    //annotation pour restreindre la fonction uniquement aux ROLE_USER
    #[IsGranted('ROLE_USER')]
    #[Route('/ingredient/nouveau', 'ingredient.new')]
    public function new(
        Request $request,
        EntityManagerInterface $manager,

        ) : Response {
            $ingredient = new Ingredient ();
            $form = $this->createForm(IngredientType::class, $ingredient);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form ->isValid()) {
                // ligne inutile, trouver son utilité
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

    //bonne pratique dans la route, lier le nom a l'objet
    //corriger la chaine url id
    #[IsGranted('ROLE_USER')]
    #[Route('/ingredient/edition{id}', 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(
        /*IngredientRepository $repository, int $id*/ 
        Ingredient $ingredient, 
        Request $request, 
        EntityManagerInterface $manager,
        ) : Response {
            //verifie si le user est bien connecté doublon avec IsGranted a corriger
     /*   if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }
        */
        //verifie si le user est bien le user proprietaire de l'ingredient
        if ($this->getUser() !== $ingredient->getUser()) {

            $this->addFlash(
            'warning',
            'Vous essayez de modifier l\'ingrédient de quelqu\'un d\'autre !!');
            return $this->redirectToRoute('ingredient.index');

        }else{

        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //persist a integrer  => bonne pratique
            $manager->persist($ingredient);
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
