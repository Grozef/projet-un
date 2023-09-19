<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use DateTimeImmutable;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
        //this controller allows uthe user to edit it's profile
    #[IsGranted('ROLE_USER')]
    //#[Security("is_granted('ROLE_USER') and user === theUser")] déprécié, conditions a mettre dans le code
    #[Route('/utilisateur/edition/{id}', name: 'user.edit', methods: ['GET', 'POST'])]
    public function edit(
    User $user, 
    Request $request,
    EntityManagerInterface $manager,
    UserPasswordHasherInterface $hasher 
    ): Response {

    // conditions rendues obsoletes par l'usage de #Security OU PAS !!!
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }

        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('recipe.index');
        }

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form ->isValid()){
            if($hasher->isPasswordValid($user, $form->getData()->getPlainPassword()))
            {
                $user = $form->getData();
                $manager->persist($user);
                $manager->flush();
    
                $this->addFlash(
                    'success',
                    'Les informations de votre compte ont été modifiées avec succés'
                );

                return $this->redirectToRoute('recipe.index');

            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect');
            }

            
        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

        //this controller allows us to modify an user's password
    //#[Security("is_granted('ROLE_USER' and user === theUser")]
    #[IsGranted('ROLE_USER')]
    #[Route('/utilisateur/edition-mot-de-passe/{id}', 'user.edit.password', methods: ['GET', 'POST'])]    
    public function editPassword(User $user, 
                                Request $request,
                                UserPasswordHasherInterface $hasher,
                                EntityManagerInterface $manager
                                ) : Response {
     /*   if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }
*/
        if ($this->getUser() !== $user) {
            
            //mettre un message d'alerte plutot que de rediriger
            $this->addFlash(
                'warning',
                'Vous essayez de modifier le mot de passe de quelqu\'un d\'autre !!');
               // return $this->redirectToRoute('security.login');
        }
                          
        $form = $this->createForm(UserPasswordType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($user, $form->getData()['plainPassword'])) {
                /*
                Si bug symfony, si le preUpdate ne flush pas la donnée
                $user->setPassword(
                    $hasher->hashPassword(
                    $user,
                    $form->getData()['newPassword']
                    )
                );
                */
                 $user->setUpdatedAt(new DateTimeImmutable());
                 $user->setPlainPassword(
                 $form->getData()['newPassword']
                );
                
                

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre mot de passe a été modifié avec succés'
                );

                return $this->redirectToRoute('recipe.index');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect'
                );
            }
        }

        return $this->render('pages/user/edit_password.html.twig', [
            'form' =>$form->createView()
        ]);
    }
}
