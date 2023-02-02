<?php

namespace App\Form\Assistances\Utils;

use App\Entity\Assistances\AddContributorAssistance;
use App\Entity\Main\Users\User;
use App\Form\AppAbstractType;
use App\Form\Extensions\CollectionType;
use App\Form\Extensions\EntityType;
use App\Form\Main\Users\UserType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddContributorAssistanceType  extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('contributors', CollectionType::class, $this->optionsMerge('contributors', [
            'entry_type' => AddContributorType::class,
            'label' => false,
            'entry_options' => ['label' => false]
        ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AddContributorAssistance::class,
        ]);
    }
}
