<?php

namespace App\Form\Assistances;

use App\Entity\Assistances\Assistance;
use App\Form\AppAbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddAssistanceContributorType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contributors', CollectionType::class, $this->optionsMerge('contributors', [
                'entry_type' => AddContributorType::class,
            ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Assistance::class,
        ]);
    }
}
