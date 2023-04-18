<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PhpParser\Node\Stmt\TryCatch;

class ArticleManager
{
    public function __construct(private readonly EntityManagerInterface $manager, private readonly CategoryManager $categoryManager)
    {
    }

    /**
     * filter all articles.
     *
     * @return array $articles
     */
    public function getAllArticles()
    {
        try {
            $articles = $this->manager->getRepository(Article::class)->findAll();
            if (null !== $articles) {
                return $articles;
            }
        } catch (\Exception $e) {
            throw new \Exception();
        }
    }

    /**
     * filter all home articles that have show in home enabled.
     *
     * @param array $articles
     *
     * @return array $homearticles
     */
    public function filterHomeArticles($articles): array
    {
        return \array_filter($articles, function (Article $item) {
            return true === $item->isHome();
        });
    }

    /**
     * filter all trending articles.
     *
     * @param array $articles
     *
     * @return array $trendingarticles
     */
    public function filterTrendingArticles($articles): array
    {
        return \array_filter($articles, function (Article $item) {
            return true === $item->isTrending();
        });
    }

    /**
     * filter all popular articles.
     *
     * @param array $articles
     *
     * @return array $populararticles
     */
    public function filterPopularArticles($articles): array
    {
        return \array_filter($articles, function (Article $item) {
            return true === $item->isPopular();
        });
    }

    /**
     * @param int $id
     */
    public function getRelatedArticles($id)
    {
        $categoryArticles = $this->categoryManager->categoryArticles($id);
        if (null !== $categoryArticles) {
        } else {
            return new \Exception('Couldn\'t perform this action with database');
        }
    }


    public function getUserArticles(User $user): mixed
    {
        try {
            $userArticles = $user->getArticles();
            return $userArticles;
        } catch (\Exception $e) {
            return new \Exception('Couldn\'t perform this action with database');
        }
    }
}
