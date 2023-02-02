<?php

namespace App\Form\Utils\SeveralFundOperations;

use App\Entity\Utils\SeveralFundsOperations\MultiCashInFlows;
use App\Form\AppAbstractType;
use App\Form\Extensions\CollectionType;
use App\Form\Utils\CashInFlowsType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MultiCashInFlowsType extends AppAbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('targets', CollectionType::class, $this->optionsMerge("members", [
            "entry_type" => CashInFlowsType::class,
            "entry_options" => [
                "user_required" => true
            ]
        ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => MultiCashInFlows::class
            ]
        );
    }
}
