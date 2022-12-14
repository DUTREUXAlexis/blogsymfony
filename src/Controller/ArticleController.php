<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Form\ArticleType;
use App\Form\CommentaireType;
use App\Repository\ArticleRepository;
use App\Repository\CommentaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{
    private ArticleRepository $articleRepository;
    private CommentaireRepository $commentaireRepository;

    //Demander à symfony d'injecter une instance de Article Repository
    // à la création du co,ntroller (instance de ArticleController)

    public function __construct(ArticleRepository $articleRepository, CommentaireRepository $commentaireRepository)
    {
        $this->articleRepository = $articleRepository;
        $this->commentaireRepository = $commentaireRepository;
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
        $articles = $this->articleRepository->findBy(['publie'=>'true'],['createdAt' => 'DESC']);


        return $this->render('article/article.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/articles/{slug}', name: 'app_article_slug')]
    // A l'appel de la méthoide getArticle symfony va créer un objet de la classe articleRepository
        //et le passer en paramètrede la méthode
        // Mecanisme : INJECTION DE DEPENDANCES
    public function getArticle($slug, Request $request): Response
    {
        //Récuperer les infos dans la BDD
        //le controlleur fait appel au modèle ( une classe du modèle )
        // Afin de récuperer la liste des articles
        //$repository = new ArticleRepository();
        $articles = $this->articleRepository->findOneBy(["slug"=>$slug]);

        $commentaire = new Commentaire();
        $formCommentaire = $this->createForm(CommentaireType::class,$commentaire);
        $formCommentaire->handleRequest($request);

        if($formCommentaire->isSubmitted() && $formCommentaire->isValid()){
            $commentaire->setCreatedAt(new \DateTime())
                ->setArticle($articles);
            $this->commentaireRepository->add($commentaire,true);
            return $this->redirectToRoute("app_articles");
        }



        return $this->renderForm('article/index.html.twig', [
            'articles' => $articles,
            'form' => $formCommentaire
        ]);
    }

    #[Route('/articles/nouveau', name: 'app_article_insert',methods:['GET','POST'],priority: 1)]
    public function insert(SluggerInterface $slugger, Request $request): Response
    {
        $article = new Article();

        $formArticle = $this->createForm(ArticleType::class,$article);

        $formArticle->handleRequest($request);
        if($formArticle->isSubmitted() && $formArticle->isValid()){
            $article->setSlug($slugger->slug($article->getTitre())->lower());
            $article->setCreatedAt(new \DateTime());
            $this->articleRepository->add($article,true);
            return $this->redirectToRoute("app_articles");
        }
        return $this->renderForm('article/nouveau.html.twig',[
            'formArticle' => $formArticle
        ]);
    }

    #[Route('/articles/modifier/{slug}', name: 'app_article_modifier',methods:['GET','POST'],priority: 1)]
    public function modifier(SluggerInterface $slugger, Request $request, $slug): Response
    {
        $article = $this->articleRepository->findOneBy(["slug"=>$slug]);

        $formArticle = $this->createForm(ArticleType::class,$article);

        $formArticle->handleRequest($request);
        if($formArticle->isSubmitted() && $formArticle->isValid()){
            $article->setSlug($slugger->slug($article->getTitre())->lower());
            $article->setCreatedAt(new \DateTime());
            $this->articleRepository->add($article,true);
            return $this->redirectToRoute("app_articles");
        }
        return $this->renderForm('article/modifier.html.twig',[
            'formArticle' => $formArticle
        ]);
    }

    #[Route('/admin', name: 'app_dashboard')]
    public function goDashBoard(): Response
    {
        return $this->render('article/index.html.twig');
    }

}
