<?php

namespace App\Controller\Admin;

use App\Controller\ArticleController;
use App\Entity\Article;
use App\Entity\Categorie;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Menu\SubMenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        //Génération d'un url afin d'accéder à la page des articles
        $url = $adminUrlGenerator
            ->setController(ArticleCrudController::class)
            ->generateUrl();
        //rediriger vers cette url
        return $this->redirect($url);





















        // Option 1. You can make your dashboard redirect to some common page of your backend
        //

        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Administration');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa-solid fa-home');
        yield MenuItem::section('Article');
        //sous menu d'articles
        yield MenuItem::subMenu('Actions', 'fa-solid fa-a')
            ->setSubItems([
                MenuItem::linkToCrud('Ajouter Article','fa-solid fa-plus', Article::class)
                ->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud('Lister Article','fa-solid fa-eye', Article::class)
                    ->setDefaultSort(['createdAt' => 'DESC'])
                    ->setAction(Crud::PAGE_INDEX),
            ]);

        yield MenuItem::section('Catégorie');
        yield MenuItem::subMenu('Actions', 'fa-solid fa-a')
            ->setSubItems([
                MenuItem::linkToCrud('Ajouter Catégorie','fa-solid fa-plus', Categorie::class)

                    ->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud('Lister Catégorie','fa-solid fa-eye', Categorie::class)
                    ->setAction(Crud::PAGE_INDEX),
            ]);
        yield MenuItem::linkToUrl('Exit','fa-solid fa-x',$this->generateUrl('app_accueil'));
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }


}
