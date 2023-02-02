<?php

namespace App\Security\Voter;

use App\Entity\Main\Users\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class NoLockedAdminVoter extends Voter
{
    protected $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['ROLE_ADMIN', 'ROLE_SUPERADMIN']);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($user->getLocked()) {
            return false;
        }

        return in_array($user->getRoles()[0], ['ROLE_ADMIN', 'ROLE_SUPERADMIN']);
    }
}
