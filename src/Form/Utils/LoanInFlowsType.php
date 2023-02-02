<?php

namespace App\Form\Utils;

use App\Entity\Main\Funds\Account;
use App\Entity\Main\Funds\DebtAvalist;
use App\Entity\Utils\LoanInFlows;
use App\Form\AppAbstractType;
use App\Form\Extensions\CollectionType;
use App\Form\Extensions\EntityType;
use App\Form\Main\Funds\DebtAvalistType;
use App\Form\Validators\NotBeforeDate;
use App\Repository\Main\Funds\AccountRepository;
use App\Repository\Main\Funds\DebtRepository;
use App\Tools\AppConstants;
use App\Tools\Twig\LocalLanguages;
use App\Twig\TranslatorTwigExtension;
use DateInterval;
use DateTime;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoanInFlowsType extends AppAbstractType
{
    public function __construct(
        private AccountRepository $accountRepository,
        private DebtRepository $debtRepository,
        TranslatorTwigExtension $appTranslator,
        LocalLanguages $languageProvider
    ) {
        parent::__construct($appTranslator, $languageProvider);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->getData()->setRenewalPeriod(new DateInterval(AppConstants::$LOANPERIOD));
        if ($builder->getData()->getAvalistes()->isEmpty()) {
            $builder->getData()->addAvaliste(new DebtAvalist());
        }
        $choices = $this->collection($this->accountRepository->findAll())
            ->sortBy(fn (Account $account) => $account->getUser()->getName());

        $builder
            ->add('wording', TextType::class, $this->optionsMerge('wording', [
                'label_attr' => ['class' => 'text-save']
            ]))
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
            ->add('createdAt', DateTimeType::class, $this->optionsMerge('created_at', [
                'label_attr' => ['class' => 'text-save'],
                'constraints' => [
                    new NotBeforeDate(['value' =>  $this->debtRepository->findLast(['user' => $builder->getData()->getUser()])?->getCreatedAt()])
                ]
            ]))
            ->add('renewalPeriod', DateIntervalType::class, $this->optionsMerge('Durée de remboursement du prêt', [
                'label_attr' => ['class' => 'text-save'], "labels" => ["years" => "Nombre d'Années", "months" => "Nombre de Mois", "days" => "Nombre de Jours"],
            ]))
            ->add('observations', TextType::class, $this->optionsMerge('general.observations', [
                'required' => false,
                'label_attr' => ['class' => 'text-save']
            ]))
            ->add('avalistes', CollectionType::class, $this->optionsMerge('avalistes', [
                'entry_type' => DebtAvalistType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false
            ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LoanInFlows::class,
        ]);
    }
}
