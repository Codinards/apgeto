<?php

namespace App\Form\Operations;

use App\Entity\Main\Operations\Operation;
use App\Entity\Main\Users\User;
use App\Form\AppAbstractType;
use App\Form\Extensions\EntityType;
use App\Entity\Utils\OperationFromAccount;
use App\Repository\Main\Users\UserRepository;
use App\Tools\AppConstants;
use App\Twig\NumberTwigExtension;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OperationFromAccountType extends AppAbstractType
{
    protected $numberFormatter;

    protected $userRepository;

    public function __construct(NumberTwigExtension $numberFormatter, UserRepository $userRepository)
    {
        $this->numberFormatter = $numberFormatter;
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('wording', TextType::class, $this->optionsMerge('wording'));
        if ($options['action_type'] == 'in') {
            $builder->add('inflows', IntegerType::class, $this->optionsMerge('inflow'));
        } else {
            $builder->add('outflows', IntegerType::class, $this->optionsMerge('outflow'));
        }
        $builder
            ->add('observation', null, $this->optionsMerge('observation'))
            ->add('createdAt', DateTimeType::class, $this->optionsMerge('created_at'))
            ->add('users', EntityType::class, $this->optionsMerge('members', [
                'class' => User::class,
                'expanded' => true,
                'multiple' => true,
                "choices" => $this->collection(
                    $this->userRepository->findBy(['locked' => 0])
                )->sortBy(fn (User $user) => $user->getName())->toArray(),
                // "choice_filter" => fn (User $user) => $user->getAccount()->getCashBalances() >= AppConstants::$BASE_ASSURANCE_AMOUNT,
                'choice_label' => fn (User $item) => $item->getName()
                    . " --- " . ucfirst($this->trans('cash_balance')) . ": "
                    . $this->numberFormatter->moneyFormat($item->getAccount()->getCashBalances()),
                'choice_attr' => [
                    'class' => 'font-size-5'
                ],
                'attr' => [
                    'class' => 'font-size-8'
                ]
            ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OperationFromAccount::class,
            'action_type' => null
        ]);
        $resolver->setRequired('action_type');
    }
}
