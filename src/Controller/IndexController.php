<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ArticleManager;
use App\Service\CategoryManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route('/', name: 'app_index')]
    public function index(ArticleManager $articleManager, CategoryManager $categoryManager): Response
    {
        $articles = $articleManager->getAllArticles();
        $home = $articleManager->filterHomeArticles($articles);
        $trending = $articleManager->filterTrendingArticles($articles);
        $popular = $articleManager->filterPopularArticles($articles);


        return $this->render('index/index.html.twig', [
            'articles' => $articles,
            'home' => $home,
            'trending' => $trending,
            'popular' => $popular,
        ]);
    }
}
