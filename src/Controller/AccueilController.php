<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    private ArticleRepository $articleRepository;

    //Demander à symfony d'injecter une instance de Article Repository
    // à la création du co,ntroller (instance de ArticleController)

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    #[Route('/', name: 'app_accueil')]
    public function index(): Response
    {
        $articles = $this->articleRepository->findBy([],['createdAt' => 'DESC'],10);



        return $this->render('accueil/index.html.twig', [
            'articles' => $articles
        ]);
    }
}
