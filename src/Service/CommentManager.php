<?php

declare(strict_types=1);

namespace App\Service;


use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;

class CommentManager
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
     * publish a comment.
     * @return void
     */
    public function publish(Comment $comment): void
    {
        try {
            $this->manager->getRepository(Comment::class)->save($comment, false);
        } catch (\Exception $e) {
            throw new \Exception();
        }
    }

    /**
     * list of categories.
     */
    public function getAllComments(): array
    {

        try {
            $comments = $this->manager->getRepository(Comment::class)->findAll();

            if (null !== $comments) {
                return \array_filter($comments, function (Comment $item) {
                    return false === $item->isVisible();
                });
            }
        } catch (\Exception $e) {
            throw new \Exception();
        }
    }
}
