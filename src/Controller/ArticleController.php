<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\Type\ArticleType;
use App\Form\Type\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Service\ArticleManager;
use App\Voter\EditArticleVoter;
use App\Voter\ViewArticlesVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use DateTimeImmutable;
use Doctrine\DBAL\Driver\PDO\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[Route('/article')]
class ArticleController extends AbstractController
{


    public function __construct(private readonly ArticleManager $articleManager)
    {
    }

    #[Route('/{articleId}', name: 'app_single_article', priority: 1)]
    #[Entity('article', options: ['mapping' => ['articleId' => 'slug']])]
    public function single(Request $request, Article $article, CommentRepository $commentRepository): Response
    {


        // dump($request->getSession()->get('_security.last_username'));
        // dump($request->attributes->get('_route'));
        $related = $this->articleManager->getRelatedArticles($article->getCategory()->getId());
        // dump($related);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $var = $form->get('test')->getData();

            if (null !== $this->getUser()) {
                $comment->setName($this->getUser()->getUsername());
                $comment->setEmail($this->getUser()->getEmail());
                $comment->setVisible(false);
            } else if (null !== $request->getSession()->get('_security.last_username')) {
                $comment->setName($request->getSession()->get('name'));
                $comment->setEmail($request->getSession()->get('_security.last_username'));
                $comment->setVisible(false);
                //setting the user session
            } else if (null === $request->getSession()->get('_security.last_username') && null === $this->getUser()) {
                $request->getSession()->set('_security.last_username', $comment->getEmail());
                $request->getSession()->set('name', $comment->getName());
                $comment->setVisible(false);
            }
            $comment->setArticle($article);


            $article->setDateOfCreation(new DateTimeImmutable());
            $commentRepository->save($comment, true);
            $flashBag = $request->getSession()->getFlashBag();

            // Clear any previous messages
            $flashBag->clear();
            $this->addFlash('warning', 'Your comment is under review');

            return $this->redirectToRoute('app_single_article', ['articleId' => $article->getSlug()]);
        }

        return $this->render('article/single.html.twig', [
            'article' => $article,
            'related' => $related,
            'addCommentForm' => $form->createView()
        ]);
    }

    #[Route('/add', name: 'app_add_article', priority: 2)]
    #[IsGranted('ROLE_USER')]
    public function add(Request $request, ArticleRepository $articleRepository)
    {
        // $this->denyAccessUnlessGranted("ROLE_ADMIN");
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $var = $form->get('test')->getData();
            $article->setUser($this->getUser());
            $article->setDateOfCreation(new DateTimeImmutable());
            $articleRepository->save($article, true);
            $flashBag = $request->getSession()->getFlashBag();

            // Clear any previous messages
            $flashBag->clear();
            $this->addFlash('success', 'Article saved successfully');

            return $this->redirectToRoute('app_add_article');
        }

        return $this->render(
            'Article/add.html.twig',
            ['action' => 'add', 'addArticleForm' => $form->createView()]
        );
    }

    #[Route('/manage', name: 'app_manage_articles', priority: 6)]
    #[IsGranted('ROLE_USER')]
    public function manageArticles(Request $request, ArticleRepository $articleRepository)
    {
        //getAllwebsite Articles in order the admin can manage them
        return $this->render('admin/manage_articles.html.twig', ['articles' => $this->articleManager->getAllArticles()]);
    }

    #[Route('/user/{user}', name: 'app_user_articles', priority: 4)]
    //    #[IsGranted(attribute: EditProductVoter::EDIT, subject: 'product')]

    #[Entity('userObj', options: ['mapping' => ['user' => 'username']])]
    #[Security("is_granted('can_view_article', userObj)")]
    public function userArticles(User $userObj, UserRepository $userRepository)
    {
        // $this->denyAccessUnlessGranted(ViewArticlesVoter::VIEW, $user);

        $userArticles = $this->articleManager->getUserArticles($this->getUser());
        // dump($this->getUser()->getArticles()->toArray());

        return $this->render('article/myarticles.html.twig', ['userArticles' => $userArticles]);
    }

    #[Route('/edit/{id}', name: 'app_edit_article', priority: 3)]
    public function edit(Article $article, Request $request, ArticleRepository $articleRepository)
    {

        $this->denyAccessUnlessGranted(EditArticleVoter::EDIT, $article);
        // $this->denyAccessUnlessGranted("ROLE_ADMIN");
        // $requestRoute = $request->attributes->get('_route');
        // $isAddRoute = 'app_add_article' === $requestRoute;
        // if (true === $isAddRoute) {
        //     $article = new Article();
        // }


        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->request->has('save_draft')) {
                $article->setVisible(false);
            } elseif ($request->request->has('publish')) {
                $article->setVisible(true);
            }
            // $var = $form->get('test')->getData();
            $article->setUser($this->getUser());
            $article->setDateOfCreation(new DateTimeImmutable());
            $articleRepository->save($article, false);
            $flashBag = $request->getSession()->getFlashBag();

            // Clear any previous messages
            $flashBag->clear();
            $this->addFlash('success', 'Article saved successfully');

            return $this->redirectToRoute('app_edit_article', ['id' => $article->getId()]);
        }

        return $this->render(
            'Article/add.html.twig',
            [
                'action' => 'edit',
                'addArticleForm' => $form->createView()
            ]
        );
    }


    /**
     * @param Article $article
     * @param Request $request
     * @param ArticleRepository $articleRepository
     * 
     * @return response
     */
    #[Route('/delete/{id}', name: 'app_delete_article', priority: 5)]
    public function delete(Article $article, Request $request, ArticleRepository $articleRepository): Response
    {

        try {
            $articleRepository->remove($article, true);
        } catch (\Exception $e) {
            dump($e->getMessage());
            throw new Exception('we ecnountered the following error', $e->getMessage());
        }

        return $this->redirect($request->headers->get('referer', '/'));
    }
}
