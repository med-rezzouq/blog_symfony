<?php

namespace App\DataFixtures;

use App\Factory\ArticleFactory;
use App\Factory\CategoryFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    private const COUNT_CATEGORIES = 5;
    private const COUNT_ARTICLES = 25;
    private const COUNT_USERS = 10;


    public function load(ObjectManager $manager): void
    {
        CategoryFactory::createMany(self::COUNT_CATEGORIES);
        UserFactory::createMany(self::COUNT_USERS);
        ArticleFactory::createMany(
            self::COUNT_ARTICLES,
            function () {
                return ['category' => CategoryFactory::random(), 'user' => UserFactory::random()];
            }
        );


        $manager->flush();
    }
}
