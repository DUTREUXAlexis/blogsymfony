<?php

namespace App\DataFixtures;

use App\Entity\Commentaire;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Reference;

class CommentaireFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");


        for($i = 1; $i <= 500; $i++)
        {
            $commentaire = new Commentaire();
            $commentaire->setContenu($faker->paragraph());
            $commentaire->setCreatedAt($faker->dateTimeBetween('-6 month','now'));

            $numarticle = $faker->numberBetween(1,100);
            $numutilisateur = $faker->numberBetween(1,200);

            $commentaire->setArticle($this->getReference("article".$numarticle));
            if(random_int(1,500 )== $i)
            {
                $commentaire->setUtilisateur(null);
            }
            else
            {
                $commentaire->setUtilisateur($this->getReference("utilisateur".$numutilisateur));
            }



            $manager->persist($commentaire);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ArticleFixtures::class,
            UtilisateurFixtures::class
        ];
    }
}
