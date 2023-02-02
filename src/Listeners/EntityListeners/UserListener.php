<?php

namespace App\Listeners\EntityListeners;

use App\Entity\Main\Funds\Account;
use App\Entity\Main\Users\User;
use App\Entity\Tontines\Tontineur;
use App\Listeners\Exceptions\EntityExceptions\UserListenerException;
use App\Repository\Main\Users\RoleRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserListener
{
    protected $roleRepository;

    protected $encoder;

    protected $manager;

    public function __construct(RoleRepository $roleRepository, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager)
    {
        $this->roleRepository = $roleRepository;
        $this->encoder = $encoder;
        $this->manager = $manager;
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        /** @var User $user */
        $user = $event->getObject();
        $isUser = $user instanceof User;
        if ($isUser and $user->getPassword()) {
            $user->setPassword(
                $this->encoder->encodePassword($user, $user->getPassword())
            );
        }
        if ($isUser && $user->getUpdatedAt() === null) {
            $user->setUpdatedAt(new DateTime());
        }
        if ($isUser and is_null($user->getRole())) {
            $userRole = $this->roleRepository->findOneBy(['name' => 'ROLE_USER']);

            if ($userRole) {
                $user->setRole($userRole);
                return;
            }
            throw new UserListenerException("Le role utilisateur n'existe pas dans la abse de donnees");
        }
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        $user = $event->getObject();
        if ($user instanceof User) {
            $tontineur = $this->manager->getRepository(Tontineur::class)->findOneBy(['user' => $user]);
            if ($tontineur->getName() !== $user->getName()) {
                $tontineur->setName($user->getName());
                $this->manager->flush();
            }
        }
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        /** @var User $user */
        $user = $event->getObject();
        if ($user instanceof User) {

            /** Tontineur creation event */
            $this->manager->persist((new Tontineur())
                ->setUser($user)
                ->setAdmin($user->getAdmin())
                ->setName($user->getName()));

            /** Account creation event */
            $this->manager->persist(
                $account = (new Account)
                    ->setUser($user)
                    ->setAdmin($user->getAdmin())
            );
            $user->setAccount($account);
            $this->manager->flush();
        }
    }
}
