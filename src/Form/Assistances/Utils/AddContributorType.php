<?php

namespace App\Form\Assistances\Utils;

use App\Entity\Assistances\AddContributor;
use App\Entity\Assistances\AddContributorAssistance;
use App\Entity\Main\Users\User;
use App\Form\AppAbstractType;
use App\Form\Extensions\EntityType;
use App\Form\Main\Users\UserType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddContributorType  extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('isSelected', CheckboxType::class, $this->optionsMerge('isSelected', [
            'label' => false,
            'bloc_name' => 'contributor_checkbox_widget'
        ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AddContributor::class,
        ]);
    }
}
