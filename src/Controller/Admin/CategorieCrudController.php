<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use ContainerDZHXbzx\getSluggerService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategorieCrudController extends AbstractCrudController
{
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public static function getEntityFqcn(): string
    {
        return Categorie::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('Titre'),
            TextField::new('slug')->hideOnForm()
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // V2RIFIER QUE L'objet ENTITY INSTANCE EST BIEN LINSTANCE DE LA CLASSE ARTICLE
        if(!$entityInstance instanceof Categorie) return;
        $entityInstance->setSlug($this->slugger->slug($entityInstance->getTitre())->lower());

        //Appelle de la méthod héritée afin de persisté l'entité :
        parent::persistEntity($entityManager,$entityInstance);

    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setPaginatorPageSize(10);
        $crud->setPageTitle(Crud::PAGE_INDEX,"liste des catégories");
        $crud->setPageTitle(Crud::PAGE_NEW,"Créer des catégories");
        return $crud;
    }


}
