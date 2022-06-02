<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PostFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Création d'un post exemple
        $post = new Post();
        $post->setTitle('Mon article test')
            ->setMedia('kangourou.png')
            ->setContent('<p>Blablabla</p>');
        $manager->persist($post);

        // Création d'un post exemple
        $post = new Post();
        $post->setTitle('Un autre article')
            ->setMedia('kaamelott.png')
            ->setContent('<p>Bliblibli</p>');
        $manager->persist($post);

        // Insertion des posts persistés
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
