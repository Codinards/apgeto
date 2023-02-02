<?php

namespace App\Form\Utils;

use App\Entity\Utils\LoanOutFlows;
use App\Form\AppAbstractType;
use App\Form\Validators\NotBeforeDate;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class LoanOutFlowsType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var LoanOutFlows $data */
        $data = $builder->getData();
        $builder
            ->add('wording', TextType::class, $this->optionsMerge('wording', [
                'label_attr' => ['class' => 'text-save']
            ]))
            ->add('loanOutFlows', IntegerType::class, $this->optionsMerge('loan_out_flow.amount', [
                'label_attr' => ['class' => 'text-save'],
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'max' => $data->getAccount()->getLoanBalances()
                    ])
                ]
            ]))
            ->add('createdAt', DateTimeType::class, $this->optionsMerge('created_at', [
                'label_attr' => ['class' => 'text-save'],
                'constraints' => [
                    new NotBeforeDate(['value' => $builder->getData()->getParent()->getCreatedAt()])
                ]
            ]))
            ->add('observations', TextType::class, $this->optionsMerge('general.observations', [
                'required' => false,
                'label_attr' => ['class' => 'text-save']
            ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LoanOutFlows::class,
        ]);
    }
}
