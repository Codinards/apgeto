<?php

namespace App\Form\Utils;

use App\Entity\Main\Funds\Account;
use App\Entity\Utils\LoanDataUpdate;
use App\Entity\Utils\LoanInFlows;
use App\Form\AppAbstractType;
use App\Form\Extensions\CollectionType;
use App\Form\Extensions\EntityType;
use App\Form\Main\Funds\DebtAvalistType;
use App\Form\Validators\AfterClosedDebt;
use App\Tools\AppConstants;
use DateInterval;
use DateTime;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoanDataUpdateType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var LoanDataUpdate $loanUpdated */
        $loanUpdated = $builder->getData();
        if ($loanUpdated->getIsInflow()) {
            $builder
                ->add('loanInFlows', IntegerType::class, $this->optionsMerge('loan_in_flow.amount', [
                    'label_attr' => ['class' => 'text-save debt_loan_in_flows'],
                    'attr' => [
                        'class' => 'debt_loan_in_flows',
                    ]
                ]))
                ->add('interests', IntegerType::class, $this->optionsMerge('debt.interests', [
                    'label_attr' => ['class' => 'text-save'],
                    'required' => false,
                    'attr' => [
                        'class' => 'debt_interests',
                        'data-rate' => AppConstants::$INTEREST_RATE,
                        'data-devise' => AppConstants::$MONEY_DEVISE
                    ]
                ]))
                ->add('avalistes', CollectionType::class, $this->optionsMerge('avalistes', [
                    'entry_type' => DebtAvalistType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => false
                ]))
                ->add('renewalPeriod', DateIntervalType::class, $this->optionsMerge('renewalPeriod', [
                    'label_attr' => ['class' => 'text-save']
                ]));
        } else {
            $builder->add('loanOutFlows', IntegerType::class, $this->optionsMerge('loan_out_flow.amount', [
                'label_attr' => ['class' => 'text-save debt_loan_out_flows'],
                'attr' => [
                    'class' => 'debt_loan_in_flows',
                ]
            ]));
        }
        $builder
            ->add('wording', TextType::class, $this->optionsMerge('wording', [
                'label_attr' => ['class' => 'text-save']
            ]))
            ->add('createdAt', DateTimeType::class, $this->optionsMerge('created_at', [
                'label_attr' => ['class' => 'text-save'],
                'constraints' => [
                    new AfterClosedDebt()
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
            'data_class' => LoanDataUpdate::class,
        ]);
    }
}
