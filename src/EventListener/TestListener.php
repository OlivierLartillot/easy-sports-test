<?php

namespace App\EventListener;

use App\Entity\Test;
use Symfony\Component\String\Slugger\SluggerInterface;


class TestListener
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function updateSlug(Test $test, $slugger)
    {
        // chaine à slugger
        $testSlug = $test->getName();

        // "sluggify" la chaine
        $slug = $this->slugger->slug($testSlug)->lower();

        // modification du slug dans l'entity
        $test->setSlug($slug);
    }

}