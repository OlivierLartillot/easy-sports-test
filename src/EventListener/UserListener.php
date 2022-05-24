<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\String\Slugger\SluggerInterface;


class UserListener
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function updateSlug(User $user, $slugger)
    {
        // chaine à slugger
        $userSlug = $user->getFirstname() . ' - '. $user->getLastname();

        // "sluggify" la chaine
        $slug = $this->slugger->slug($userSlug)->lower();

        // modification du slug dans l'entity
        $user->setSlug($slug);

    }

}