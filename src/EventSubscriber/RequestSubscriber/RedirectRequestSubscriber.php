<?php

namespace App\EventSubscriber\RequestSubscriber;


use App\Entity\Main\Users\Role;
use App\Entity\Main\Users\User;
use Doctrine\Persistence\ObjectManager;
use App\Repository\Main\Users\RoleRepository;
use App\Repository\Main\Users\UserRepository;
use App\Tools\Entity\BaseRoleProperties;
use App\Tools\Languages\LangResolver;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;

class RedirectRequestSubscriber implements EventSubscriberInterface
{
    protected $manager;

    protected $router;

    protected $langResolver;

    public function __construct(ObjectManager $manager, RouterInterface $router, LangResolver $langResolver)
    {
        $this->manager = $manager;
        $this->router = $router;
        $this->langResolver = $langResolver;
    }


    public function onKernelRequest(RequestEvent $event)
    {
        /** @var RoleRepository $rolesRepo */
        $rolesRepo = $this->manager->getRepository(Role::class);

        /** @var UserRepository $usersRepo */
        $usersRepo = $this->manager->getRepository(User::class);

        $request = $event->getRequest();

        if (empty($rolesRepo->findAll())) {
            foreach ([
                ['name' => BaseRoleProperties::ROLE_USER, 'title' => BaseRoleProperties::USER_TITLE, 'is_root' => false],
                ['name' => BaseRoleProperties::ROLE_ADMIN, 'title' => BaseRoleProperties::ADMIN_TITLE, 'is_root' => false],
                ['name' => BaseRoleProperties::ROLE_SUPERADMIN, 'title' => BaseRoleProperties::SUPERADMIN_TITLE, 'is_root' => true],
            ] as $item) {
                $role = (new Role())
                    ->setName($item['name'])
                    ->setTitle($item['title'])
                    ->setIsDeletable(false);
                $this->manager->persist($role);
                if ($item['is_root']) {
                    $roleAdmin = $role;
                }
            }
            $this->manager->flush();
        } else {
            $roleAdmin = $rolesRepo->findOneBy(['name' => 'ROLE_SUPERADMIN']);
        }

        $user = $usersRepo->findOneBy(['role' => $roleAdmin]);
        if (empty($usersRepo->findAll()) || !$user) {
            $adminRoute = $this->router->generate(
                'first_admin_registration',
                ['lang' => $this->langResolver->getLanguage($request->getLocale())]
            );
            /*$event->getRequest()->getPathInfo() !== $adminRoute*/
            if ($request->attributes->get('_route') !== 'first_admin_registration' and stripos($request->attributes->get('_controller'), 'error') === false) {
                $event->setResponse(new RedirectResponse(
                    $adminRoute
                ));
                $event->getRequest()->getSession()->set('has_first_admin', false);
            }
        } else {
            $event->getRequest()->getSession()->set('has_first_admin', true);
        }
    }


    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest'
        ];
    }
}
