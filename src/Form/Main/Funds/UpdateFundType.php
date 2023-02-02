<?php

namespace App\Form\Main\Funds;

use App\Form\AppAbstractType;
use App\Entity\Main\Funds\Fund;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateFundType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Fund $data */
        $data = $builder->getData();
        $builder
            ->add('wording', null, $this->optionsMerge("wording"));
        if (((int)$data->getCashInFlows()) > 0) {
            $builder->add("cashInFlows", IntegerType::class, $this->optionsMerge("Montant Entrée de fond"));
        } else {
            $builder->add("cashOutFlows", IntegerType::class, $this->optionsMerge("Montant Sortie de fond"));
        }


        $builder->add('createdAt', null, $this->optionsMerge("createdAt"))
            ->add('observations', null, $this->optionsMerge("observations"));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Fund::class,
        ]);
    }
}
