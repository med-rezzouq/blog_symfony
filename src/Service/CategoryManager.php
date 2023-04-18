<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

class CategoryManager
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    // public function getArticles($category_name): array
    // {
    //     $category_articles = $this->manager->getRepository(Category::class)->findAll()->getArticles();
    //     dump($category_articles);

    //     return \array_filter($category_articles, function (Article $item) {
    //         return $item->isHome() === true;
    //     });
    // }

    /**
     * @param int $id
     *
     * @return array|\exception
     */
    public function categoryArticles($id)
    {
        try {
            $articles = $this->manager->getRepository(Category::class)->find($id)->getArticles();

            return $articles;
        } catch (\Exception $e) {
            throw new \Exception('error while establishing database connexion');
        }
    }

    /**
     * list of categories.
     */
    public function getCategories(): array
    {
        return $this->manager->getRepository(Category::class)->findAll();
    }
}
