<?php

namespace App\Controller\Recipe;

use App\Entity\Mark;
use App\Entity\User;
use App\Entity\Media;
use App\Entity\Recipe;
use App\Form\MarkType;
use App\Form\RecipeType;
use App\Repository\MarkRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AjouterRecipeController extends AbstractController
{
    // controller for a new recipe
    #[IsGranted('ROLE_USER')]
    #[Route('/recette/ajouter', name: 'recipe.ajouter', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $manager,
        ) : Response {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        
        $form-> handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

    /*        //Attention code expérimental 
            $media = $form->get('media')->getData();

        if ($media) {

            // Déplacez le fichier vers le répertoire approprié
            try {
                $media->move(
                    $this->getParameter('media_directory'),
            
                );
            } catch (FileException $e) {
                // Gérer l'erreur d'upload
            }

            // Associez le nom du fichier à la propriété 'chemin' de la classe Media
            $media = new Media();
            //$media->setChemin($newFilename);

            // Associez le média à la recette
            $recipe= $form->getData();
            $media = $form["media"]->getData();

            $manager->flush();
        }

            //Fin de l'experimentation */

      /*      $infos = $form["media"]->getData();
            $media->setName($infos->getClientOriginalName());
            $media->setTaille($infos->getSize());
            
            $media->setExtension($infos->getMimeType());
            $infos->move(
                "uploads/image",
                $media->getName()
            );

            $recipe->setMedia($media);
            $manager->persist($media);
            $manager->flush();

            a reussir
           
*/
            
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
}