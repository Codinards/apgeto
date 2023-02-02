<?php

namespace App\Form\Main\Users;

use App\Form\AppAbstractType;
use App\Entity\Main\Users\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserPasswordType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, $this->optionsMerge('password', [
                'type' => PasswordType::class,
                'attr' => [
                    'value' => '',
                ],
                'invalid_message' => $this->trans('password field must match.'),
                'first_options' => ['label' => $this->trans("password")],
                'second_options' => ['label' => $this->trans("password.confirmation")],
            ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
