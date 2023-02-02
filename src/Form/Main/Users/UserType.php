<?php

namespace App\Form\Main\Users;

use App\Form\AppAbstractType;
use App\Entity\Main\Users\User;
use App\Form\Extensions\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', null, $this->optionsMerge('pseudo'))
            ->add('username', null, $this->optionsMerge('username'))
            ->add('address', null, $this->optionsMerge('address'))
            ->add('telephone', null, $this->optionsMerge('telephone'))
            ->add('parrain', EntityType::class, $this->optionsMerge('parrain', [
                'class' => User::class,
                'required' => false,
                'choice_filter' => fn (?User $user) => is_null($user) ? $user : $user->getId() !== $builder->getData()->getId(),
            ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
