<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use App\Entity\User;
use App\Entity\Recipe;

final class RecipeVoter extends Voter
{
    public const CREATE     = 'RECIPE_CREATE';
    public const EDIT       = 'RECIPE_EDIT';
    public const VIEW       = 'RECIPE_VIEW';
    public const LIST       = 'RECIPE_LIST';
    public const LIST_ALL   = 'RECIPE_ALL';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array(
            $attribute,
            [self::CREATE, self::LIST, self::LIST_ALL]
        ) ||
            (
                in_array($attribute, [self::EDIT, self::VIEW])
                && $subject instanceof \App\Entity\Recipe
            );
    }

    /**
     *
     * @param string $attribute
     * @param Recipe|null $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            $vote?->addReason('The user must be logged in to access this resource.');

            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                return $subject->getUser()->getId() === $user->getId();

            case self::VIEW:
            case self::LIST:
            case self::CREATE:
                return true;
        }

        return false;
    }
}
