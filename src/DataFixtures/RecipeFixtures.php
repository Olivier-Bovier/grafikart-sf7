<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use FakerRestaurant\Provider\fr_FR\Restaurant;
use Symfony\Component\String\Slugger\SluggerInterface;

class RecipeFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(private readonly SluggerInterface $slugger) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Restaurant($faker));

        $categories = ['Entrée froide', 'Entrée chaude', 'Plat chaud', 'Dessert'];
        foreach ($categories as $c) {
            $category = (new Category())
                ->setName($c)
                ->setSlug($this->slugger->slug($c))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime));

            $manager->persist($category);
            $this->addReference($c, $category);
        }

        for ($i = 1; $i <= 10; $i++) {
            $title = $faker->foodName();
            $recipe = new Recipe();
            $recipe->setTitle($title)
                ->setSlug($this->slugger->slug($title))
                ->setContent($faker->paragraph(10, true))
                ->setCategory($this->getReference($faker->randomElement($categories), Category::class))
                ->setUser($this->getReference('USER' . $faker->numberBetween(1, 10), User::class))
                ->setDuration($faker->numberBetween(5, 60))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime));
            $manager->persist($recipe);

            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
