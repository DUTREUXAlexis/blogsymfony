<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    private SluggerInterface $slugger;

    //Demander à symfony d'injecter le slugger au niveau du constructeur

    public function __construct(SluggerInterface $slugger){
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);


        // initialiser faker
        $faker = Factory::create("fr_FR");



        for($i = 1; $i <= 100; $i++)
        {
            $article = new Article();
            $article->setTitre($faker->words($faker->numberBetween(3,7),true));
            $article->setcontenu($faker->paragraph(3,true));
            $article->setCreatedAt($faker->dateTimeBetween('-6 month','now'));
            $article->setSlug($this->slugger->slug($article->getTitre())->lower());
            //INSERT INTO artilces Values ...

            //Associer l'article à une cat
            //Récuperer une ref d'une catégorie
            $numcat = $faker->numberBetween(0,8);
            $article->setCategorie($this->getReference("categorie".$numcat));
            $this->addReference("article".$i,$article);
            $manager->persist($article);
        }
        //Envoyer l'ordre INSERT
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategorieFixtures::class
        ];
        // TODO: Implement getDependencies() method.
    }
}
