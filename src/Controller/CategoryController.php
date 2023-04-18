<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/index', name: 'app_categories')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('partials/_categories.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/{categoryId}', name: 'app_category')]
    #[Entity('category', options: ['mapping' => ['categoryId' => 'slug']])]
    public function ArticleByCategories(Category $category): Response
    {


        return $this->render('article/category_articles.html.twig', [
            'category' => $category,
        ]);
    }
}
