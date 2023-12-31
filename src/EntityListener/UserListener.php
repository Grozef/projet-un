<?php

namespace App\EntityListener;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
class UserListener
{
    private UserPasswordHasherInterface $hasher;
    
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function prePersist(User $user)
    {
        $this->encodePassword($user);
    }
/*
        bug symfony, le preUpdate ne flush pas la donnée
        soluce apprendre #Symfony6-Edition du profil et du mot de passe #13 de développeur Musclé sur YouTube
*/
    public function preUpdate(User $user)
    {
        $this->encodePassword($user);
    }

        //Encode password based on plainPassword
    public function encodePassword(User $user)
    {
        if($user->getPlainPassword() === null) {
            return;
        }

        $user->setPassword(
            $this->hasher->hashPassword(
                $user,
                $user->getPlainPassword()
            )
        );
    }
}

