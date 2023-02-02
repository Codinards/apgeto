<?php

namespace App\Form\Utils\SeveralFundOperations;

use App\Entity\Utils\CashOutFlows;
use App\Form\AppAbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class CashOutFlowsType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var CashOutFlows $data */
        $data = $builder->getData();
        $builder
            ->add('wording', TextType::class, $this->optionsMerge('wording', [
                'label_attr' => ['class' => 'text-save']
            ]))
            ->add('cashOutFlows', IntegerType::class, $this->optionsMerge('cash_out_flow.amount', [
                'label_attr' => ['class' => 'text-save']
            ]))
            ->add('createdAt', DateTimeType::class, $this->optionsMerge('created_at', [
                'label_attr' => ['class' => 'text-save']
            ]))
            ->add('observations', TextType::class, $this->optionsMerge('general.observations', [
                'required' => false,
                'label_attr' => ['class' => 'text-save']
            ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CashOutFlows::class,
        ]);
    }
}
