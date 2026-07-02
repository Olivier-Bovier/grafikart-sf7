<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Ingredient;
use App\Entity\Quantity;
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

        $ingredients = array_map(fn(string $name) => (new Ingredient())
            ->setName($name)
            ->setSlug(strtolower($this->slugger->slug($name))), [
            // Légumes
            'Ail',
            'Oignon',
            'Échalote',
            'Carotte',
            'Courgette',
            'Aubergine',
            'Poivron rouge',
            'Poivron vert',
            'Tomate',
            'Pomme de terre',
            'Champignon de Paris',
            'Épinard',
            'Brocoli',
            'Chou-fleur',
            'Poireau',
            'Céleri',
            'Navet',
            'Betterave',
            'Radis',
            'Concombre',

            // Viandes & Poissons
            'Poulet',
            'Boeuf haché',
            'Filet de porc',
            'Lardons',
            'Saumon',
            'Cabillaud',
            'Crevettes',
            'Thon en conserve',
            'Agneau',
            'Canard',

            // Produits laitiers & Åufs
            'Oeuf',
            'Beurre',
            'Crème fraîche',
            'Lait',
            'Gruyère râpé',
            'Parmesan',
            'Mozzarella',
            'Feta',
            'Fromage de chèvre',
            'Yaourt nature',

            // FÃ©culents & CÃ©rÃ©ales
            'Farine de blé',
            'Riz basmati',
            'Pâtes',
            'Pain de mie',
            'Quinoa',
            'Lentilles',
            'Pois chiches',
            'Haricots blancs',

            // Herbes & Ãpices
            'Persil',
            'Basilic',
            'Thym',
            'Romarin',
            'Coriandre',
            'Cumin',
            'Paprika',
            'Curcuma',
            'Poivre noir',
            'Noix de muscade',
            'Cannelle',
            'Piment de Cayenne',

            // Huiles, Sauces & Condiments
            'Huile d\'olive',
            'Moutarde de Dijon',
            'Vinaigre balsamique',
            'Sauce soja',
            'Jus de citron',
            'Miel',
            'Concentré de tomate',

            // Fruits (pour desserts & plats sucrés-salés)
            'Pomme',
            'Poire',
            'Fraise',
            'Citron',
            'Orange',
            'Banane',
            'Framboises',
            'Myrtilles',
            'Abricot',
            'Mangue',

            // Sucre & Desserts
            'Sucre en poudre',
            'Sucre glace',
            'Chocolat noir',
            'Cacao en poudre',
            'Levure chimique',
            'Extrait de vanille',
            'Amandes en poudre',
        ]);

        foreach ($ingredients as $ingredient) {
            $manager->persist($ingredient);
        }

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

            $shuffled = $ingredients;
            shuffle($shuffled);
            foreach (array_slice($shuffled, 0, $faker->numberBetween(2, 5)) as $ingredient) {
                $recipe->addQuantity((new Quantity())
                    ->setQuantity($faker->numberBetween(1, 500))
                    ->setUnit($faker->randomElement(['g', 'ml', 'tasse', 'cuillère à soupe', 'cuillère à café']))
                    ->setIngredient($ingredient));
            }

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
