<?php

namespace App\Form\Utils;

use App\Entity\Main\Funds\Account;
use App\Entity\Utils\LoanInFlows;
use App\Form\AppAbstractType;
use App\Form\Extensions\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoanInFlowsUpdateType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('wording', TextType::class, $this->optionsMerge('wording', [
                'label_attr' => ['class' => 'text-save']
            ]))
            ->add('createdAt', DateTimeType::class, $this->optionsMerge('created_at', [
                'label_attr' => ['class' => 'text-save']
            ]))
            ->add('renewalPeriod', DateIntervalType::class, $this->optionsMerge('Durée de remboursement du prêt', [
                'label_attr' => ['class' => 'text-save']
            ]))
            ->add('observations', TextType::class, $this->optionsMerge('general.observations', [
                'required' => false,
                'label_attr' => ['class' => 'text-save']
            ]))
            ->add('firstAvaliste', EntityType::class, $this->optionsMerge('firstAvaliste', [
                'class' => Account::class,
                'label' => "Avaliste",
                'required' => true,
                'row_attr' => [
                    'class' => 'input_avaliste input_avaliste_1'
                ],
                'label_attr' => ['class' => 'text-save']
            ]))
            ->add('secondAvaliste', EntityType::class, $this->optionsMerge('secondAvaliste', [
                'class' => Account::class,
                'label' => "Second avaliste",
                'required' => false,
                'row_attr' => [
                    'class' => 'input_avaliste input_avaliste_2 loan_in_flow_group_2'
                ],
                'label_attr' => ['class' => 'text-save']
            ]))
            ->add('secondObservation', TextType::class, $this->optionsMerge('secondObservation', [
                'required' => false,
                'label' => "Observation",
                'row_attr' => [
                    'class' => 'input_observation input_observation_2 loan_in_flow_group_2'
                ],
                'label_attr' => ['class' => 'text-save']
            ]))
            ->add('thirdAvaliste', EntityType::class, $this->optionsMerge('thirdAvaliste', [
                'class' => Account::class,
                'label' => "Troisième avaliste",
                'required' => false,
                'row_attr' => [
                    'class' => 'input_avaliste input_avaliste_3 loan_in_flow_group_3'
                ],
                'label_attr' => ['class' => 'text-save']
            ]))
            ->add('thirdObservation', TextType::class, $this->optionsMerge('thirdObservation', [
                'required' => false,
                'label' => "Observation",
                'row_attr' => [
                    'class' => 'input_observation input_observation_3 loan_in_flow_group_3'
                ],
                'label_attr' => ['class' => 'text-save']
            ]))
            ->add('fourthAvaliste', EntityType::class, $this->optionsMerge('fourthAvaliste', [
                'class' => Account::class,
                'label' => "Quatrième avaliste",
                'required' => false,
                'row_attr' => [
                    'class' => 'input_avaliste input_avaliste_4 loan_in_flow_group_4'
                ],
                'label_attr' => ['class' => 'text-save']
            ]))
            ->add('fourthObservation', TextType::class, $this->optionsMerge('fourthObservation', [
                'required' => false,
                'label' => "Observation",
                'row_attr' => [
                    'class' => 'input_observation input_observation_4 loan_in_flow_group_4'
                ],
                'label_attr' => ['class' => 'text-save']
            ]))
            ->add('fifthAvaliste', EntityType::class, $this->optionsMerge('fifthAvaliste', [
                'class' => Account::class,
                'label' => "Cinquième avaliste",
                'required' => false,
                'row_attr' => [
                    'class' => 'input_avaliste input_avaliste_5 loan_in_flow_group_5'
                ],
                'label_attr' => ['class' => 'text-save']
            ]))
            ->add('fifthObservation', TextType::class, $this->optionsMerge('fifthObservation', [
                'required' => false,
                'label' => "Observation",
                'row_attr' => [
                    'class' => 'input_observation input_observation_5 loan_in_flow_group_5'
                ],
                'label_attr' => ['class' => 'text-save']
            ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LoanInFlows::class,
        ]);
    }
}
