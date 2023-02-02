<?php

namespace Njeaner\UserRoleBundle\Annotations;

use ReflectionClass;
use ReflectionMethod;
use App\Entity\Main\Users\Role;
use App\Tools\DirectoryResolver;
use App\Controller\HomeController;
use App\Entity\Main\Users\UserAction;
use App\Controller\SecurityController;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Controller\Backend\AdminController;
use App\Entity\Main\Configs\Module;
use App\Repository\Main\Users\RoleRepository;
use App\Repository\Main\Users\UserActionRepository;
use Illuminate\Support\Collection;
use Njeaner\UserRoleBundle\Annotations\Module as AnnotationsModule;

class RouteActionAnnotationReader
{
    protected $annotationReader;

    /**
     * @var Collection
     */
    protected $annotations;

    protected $manager;

    protected $doctrine;

    protected $modules;

    public function __construct(Reader $annotationReader, EntityManagerInterface $manager, ManagerRegistry $doctrine)
    {
        $this->annotationReader = $annotationReader;
        $this->manager = $manager;
        $this->annotations = new Collection([]);
        $this->modules = new Collection([]);
        $this->doctrine = $doctrine;
    }


    public function updateRouteAction(string $annotationName): Collection
    {

        if ($this->annotations->isEmpty()) {
            foreach ($this->iterateData($this->getControllerRepositories()) as $data) {
                if ($data['type'] === 'file') {
                    $this->annotations = $this->update(
                        $data['namespace'],
                        $annotationName,
                        $this->annotations
                    );
                } else {
                    $lists = dir($data['directory']);

                    while (($file = $lists->read()) !== false) {
                        if ($file === "." || $file === ".." || stripos($file, '.php') === false) continue;
                        $class = str_replace('.php', '', $data['namespace'] . '\\' . $file);
                        $this->annotations = $this->update(
                            $class,
                            $annotationName,
                            $this->annotations
                        );
                    }
                }
            }
        }
        return $this->annotations;
    }

    public function update(string $class, string $annotationName, Collection $annotations)
    {
        /** @var UserActionRepository $userActionRepository */
        $userActionRepository = $this->manager->getRepository(UserAction::class);

        /** @var RoleRepository $roleRepository */
        $roleRepository = $this->manager->getRepository(Role::class);
        $moduleRepository = $this->manager->getRepository(Module::class);
        if (class_exists($class)) {
            $reflectionClass = new ReflectionClass($class);
            /** @var Module $moduleAnnotation */
            $moduleAnnotation = $this->annotationReader->getClassAnnotation($reflectionClass, AnnotationsModule::class);
            $module = $moduleRepository->findOneBy(['name' => $moduleAnnotation->getName()]);

            if (!$module) {
                $module = (new Module)
                    ->setName($moduleAnnotation->getName())
                    ->setIsActivated($moduleAnnotation->getIsActivated());
                $this->persistAndFlush($module);
                $this->addModule($module);
                //$this->manager->flush();
            } else {
                $this->addModule($module);
            }
            $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method) {
                /** @var RouteAction $annotation */
                if ($annotation = $this->annotationReader->getMethodAnnotation($method, $annotationName)) {
                    $action = $userActionRepository->findOneBy(['name' => $annotation->getName()]);
                    if (!$action) {

                        $this->persistAndFlushCallable(function () use ($annotation, $roleRepository) {
                            $action = (new UserAction())
                                ->setName($annotation->getName())
                                ->setTitle($annotation->getTitle())
                                ->setIsUpdatable($annotation->getIsUpdatable())
                                ->setModule($this->getModuleFromName($annotation->getModule()))
                                ->setHasAuth($annotation->getHasAuth())
                                ->setIsIndex($annotation->getIsIndex());
                            //$this->persistAndFlush($action);
                            $this->manager->persist($action);

                            $roles = $roleRepository->findAll();
                            /** @var Role $role */
                            foreach ($roles as $role) {
                                if (in_array($role->getName(), $annotation->getTargets())) {
                                    $role->addUserAction($action);
                                } elseif (in_array('admins', $annotation->getTargets()) and stripos($role->getName(), strtoupper('ADMIN'))) {
                                    $role->addUserAction($action);
                                } elseif (in_array('all', $annotation->getTargets())) {
                                    $role->addUserAction($action);
                                }
                                $this->manager->persist($action);
                                //$this->persistAndFlush($role);
                            }
                            $this->manager->flush();
                        });
                    } else {
                        if ($annotation->getIsUpdated()) {

                            $this->persistAndFlushCallable(function () use ($annotation, $roleRepository, $action) {
                                $action = $annotation->updateAction($action);

                                if ($annotation->getUpdatedRole()) {
                                    /** @var Role[] $roles */
                                    $roles = $action->getRoles();
                                    foreach ($roles as $role) {
                                        $role->removeUserAction($action);
                                    }
                                    $roles = $roleRepository->findAll();
                                    /** @var Role $role */
                                    foreach ($roles as $role) {
                                        if (in_array($role->getName(), $annotation->getTargets())) {
                                            $role->addUserAction($action);
                                        } elseif (in_array('admins', $annotation->getTargets()) and stripos($role->getName(), strtoupper('ADMIN'))) {
                                            $role->addUserAction($action);
                                        } elseif (in_array('all', $annotation->getTargets())) {
                                            $role->addUserAction($action);
                                        }
                                    }
                                }
                                $this->manager->flush();
                            });
                        }
                    }

                    $annotations->push($annotation);
                }
            }
        }
        $this->manager->flush();
        return $annotations;
    }


    public function getControllerRepositories(): array
    {
        return [
            HomeController::class => [HomeController::class, 'file'],
            SecurityController::class => [SecurityController::class, 'file'],
            AdminController::class => [AdminController::class, 'file'],
            'App\Controller\Frontend' => [DirectoryResolver::getDirectory('src/Controller/Frontend'), 'folder'],
            'App\Controller\Backend\Users' => [DirectoryResolver::getDirectory('src/Controller/Backend/Users'), 'folder'],
            'App\Controller\Backend\Accounts' => [DirectoryResolver::getDirectory('src/Controller/Backend/Accounts'), 'folder'],
            'App\Controller\Backend\Assistances' => [DirectoryResolver::getDirectory('src/Controller/Backend/Assistances'), 'folder'],
            'App\Controller\Backend\Tontines' => [DirectoryResolver::getDirectory('src/Controller/Backend/Tontines'), 'folder'],
            'App\Controller\Backend\Operations' => [DirectoryResolver::getDirectory('src/Controller/Backend/Operations'), 'folder'],

        ];
    }

    public function iterateData(array $data)
    {
        foreach ($data as $namespace => $dir) {
            yield ['namespace' => $namespace, 'directory' => $dir[0], 'type' => $dir[1]];
        }
    }

    public function persistAndFlush(object $data)
    {
        $em = $this->manager;
        $em->beginTransaction();
        try {
            $em->persist($data);
            $em->flush();
            $em->commit();
        } catch (\Throwable $e) {
            while ($em->getConnection()->getTransactionNestingLevel() > 0) {
                $em->rollback();
            }
            if (!$em->isOpen()) {
                $this->doctrine->resetManager();
            }
            dump($e);
        }
    }

    public function persistAndFlushCallable(callable $callable)
    {
        //$em = $this->manager;
        //$em->beginTransaction();
        try {

            $callable();
            //$em->commit();
        } catch (\Throwable $e) {
            /*while ($em->getConnection()->getTransactionNestingLevel() > 0) {
                $em->rollback();
            }
            if (!$em->isOpen()) {
                //$this->doctrine->resetManager();
            }*/
            dd($e);
        }
    }

    public function addModule(Module $module)
    {
        if (!$this->modules->contains($module)) {
            $this->modules = $this->modules->push($module);
        }
    }

    public function getModuleFromName(string $name): ?Module
    {
        /** @var Module $module */
        foreach ($this->modules as $module) {
            if ($module->getName() === $name) {
                return $module;
            }
        }
        return null;
    }
}
