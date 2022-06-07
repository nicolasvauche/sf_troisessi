<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    private $manager;

    /**
     * @param $manager
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->manager = $managerRegistry;
    }


    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'posts' => $this->manager->getRepository(Post::class)->findBy([], ['title' => 'DESC']),
        ]);
    }
}
