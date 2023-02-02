<?php

namespace App\Form\Utils;

use App\Entity\Utils\ContributorFilter;
use App\Form\AppAbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContributorFilterType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('minCashBalance', IntegerType::class, $this->optionsMerge('minCashBalance', [
                'label' => $this->trans('min.cash_balance'),
                'required' => false
            ]))
            ->add('maxCashBalance', IntegerType::class, $this->optionsMerge('maxCashBalance', [
                'label' => $this->trans('max.cash_balance'),
                'required' => false
            ]))
            /*->add('minCashBetween', IntegerType::class, $this->optionsMerge('minCashBetween', [
                'required' => false
            ]))
            ->add('maxCashBetween', IntegerType::class, $this->optionsMerge('maxCashBetween', [
                'required' => false
            ]))*/;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContributorFilter::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
