<?php

namespace App\Form\Utils\SeveralFundOperations;

use App\Entity\Utils\SeveralFundsOperations\MultiCashOutFlows;
use App\Form\AppAbstractType;
use App\Form\Extensions\CollectionType;
use App\Form\Utils\CashOutFlowsType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MultiCashOutFlowsType extends AppAbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('targets', CollectionType::class, $this->optionsMerge("members", [
            "entry_type" => CashOutFlowsType::class,
            "entry_options" => [
                "user_required" => true
            ]
        ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => MultiCashOutFlows::class
            ]
        );
    }
}
