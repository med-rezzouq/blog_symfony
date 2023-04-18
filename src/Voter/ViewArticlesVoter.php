<?php

declare(strict_types=1);

namespace App\Voter;

use App\Entity\Article;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ViewArticlesVoter extends Voter
{

    public const VIEW = 'can_view_article';
    // public const ADD = 'can_add_article';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof User && self::VIEW === $attribute;
    }


    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {

        /** @var User $user */
        $user = $token->getUser();

        // only the Owner can edit the given product
        return ($subject === $user || true === in_array('ROLE_ADMIN', $user->getRoles()) ? true : false);
    }
}
