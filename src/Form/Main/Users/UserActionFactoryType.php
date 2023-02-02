<?php

namespace App\Form\Main\Users;

use App\Form\AppAbstractType;
use Symfony\Component\Form\AbstractType;
use App\Entity\Main\Users\UserActionFactory;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class UserActionFactoryType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('roles', CollectionType::class, [
                'entry_type' => RoleActionType::class,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserActionFactory::class,
        ]);
    }
}
