<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use App\Service\CommentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use DateTimeImmutable;

class CommentController extends AbstractController
{

    #[Route('/comments', name: 'app_comments', priority: 2)]
    #[IsGranted('ROLE_ADMIN')]
    public function index(Request $request, CommentManager $CommentManager)
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $comments = $CommentManager->getAllComments();


        return $this->render(
            'admin/comments.html.twig',
            ['comments' => $comments]
        );
    }

    #[Route('/comments/{id}', name: 'publish_comment', priority: 1)]
    #[IsGranted('ROLE_ADMIN')]
    public function publish(Comment $comment, CommentManager $CommentManager)
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        $comment->setVisible(true);
        dump($comment);
        $comments = $CommentManager->publish($comment);


        return $this->redirectToRoute('app_comments');
    }
}
