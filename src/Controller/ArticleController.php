<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    private ArticleRepository $articleRepository;

    //Demander à symfony d'injecter une instance de Article Repository
    // à la création du co,ntroller (instance de ArticleController)

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }


    #[Route('/articles', name: 'app_articles')]
    // A l'appel de la méthoide getArticle symfony va créer un objet de la classe articleRepository
    //et le passer en paramètrede la méthode
        // Mecanisme : INJECTION DE DEPENDANCES
    public function getArticles(): Response
    {
        //Récuperer les infos dans la BDD
        //le controlleur fait appel au modèle ( une classe du modèle )
        // Afin de récuperer la liste des articles
        //$repository = new ArticleRepository();
        $articles = $this->articleRepository->findBy([],['createdAt' => 'DESC']);


        return $this->render('article/article.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/articles/{slug}', name: 'app_article_slug')]
    // A l'appel de la méthoide getArticle symfony va créer un objet de la classe articleRepository
        //et le passer en paramètrede la méthode
        // Mecanisme : INJECTION DE DEPENDANCES
    public function getArticle($slug): Response
    {
        //Récuperer les infos dans la BDD
        //le controlleur fait appel au modèle ( une classe du modèle )
        // Afin de récuperer la liste des articles
        //$repository = new ArticleRepository();
        $articles = $this->articleRepository->findOneBy(["slug"=>$slug]);
        $categorie = $articles->getCategorie()->getTitre();

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'categorie' => $categorie
        ]);
    }


}
