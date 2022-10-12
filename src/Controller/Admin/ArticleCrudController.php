<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Config\Framework\WebLinkConfig;

class ArticleCrudController extends AbstractCrudController
{
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }


    public static function getEntityFqcn(): string
    {
        return Article::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('Titre')->setLabel('Titre de l\'article'),
            TextEditorField::new('contenu')->setSortable(false)->hideOnIndex(),
            DateField::new('createdAt','Créé le')->hideOnForm(),
            AssociationField::new('categorie')->setRequired(false),
            BooleanField::new('publie'),
            TextField::new('slug')->hideOnForm()
        ];
    }

    //redéfniir la méthode persisst entity qui va être appellée lors de la création de bddd

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // V2RIFIER QUE L'objet ENTITY INSTANCE EST BIEN LINSTANCE DE LA CLASSE ARTICLE
        if(!$entityInstance instanceof Article) return;
        $entityInstance->setCreatedAt(new \DateTime());
        $entityInstance->setSlug($this->slugger->slug($entityInstance->getTitre())->lower());

        //Appelle de la méthod héritée afin de persisté l'entité :
        parent::persistEntity($entityManager,$entityInstance);

    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setPaginatorPageSize(10);
        $crud->setPageTitle(Crud::PAGE_INDEX,"liste des articles");
        $crud->setPageTitle(Crud::PAGE_NEW,"Créer des articles");
        $crud->setDefaultSort(["createdAt"=>'DESC
        ']);
        return $crud;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->update(Crud::PAGE_INDEX, Action::NEW,
            function (Action $action){
            return $action->setLabel('Ajouter article')
                ->setIcon("fa fa-plus");
            }
        );
        $actions->update(Crud::PAGE_NEW,Action::SAVE_AND_RETURN,
        function (Action $action){
           return $action->setLabel('Valider')
               ->setIcon("fa-solid fa-check");
        });
        $actions->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER);
        $actions->add(Crud::PAGE_INDEX, Action::DETAIL,
            'Details'
        );

        return $actions;
    }

    public function configureFilters(Filters $filters): Filters
    {
        $filters->add("titre");
        $filters->add("createdAt");
        return $filters;
    }


}
