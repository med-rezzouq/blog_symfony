<?php

declare(strict_types=1);

namespace App\Voter;

use App\Entity\Article;
use App\Entity\Product;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EditArticleVoter extends Voter
{

    public const EDIT = 'can_edit_article';
    // public const ADD = 'can_add_article';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Article && self::EDIT === $attribute;
    }


    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {

        /** @var User $user */
        $user = $token->getUser();

        // $subject est Product
        /** @var Article $product */
        $article = $subject;

        $author = $article->getUser();



        // only the Owner can edit the given product
        return ($author === $user || true === in_array('ROLE_ADMIN', $user->getRoles()) ? true : false);
    }
}
