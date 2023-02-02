<?php

namespace App\Form\Utils;

use App\Entity\Main\Users\User;
use App\Entity\Utils\CashInFlows;
use App\Form\AppAbstractType;
use App\Form\Extensions\EntityType;
use App\Repository\Main\Users\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CashInFlowsType extends AppAbstractType
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['user_required'] === true) {
            $builder
                ->add('user', EntityType::class, $this->optionsMerge('member', [
                    'label_attr' => ['class' => 'text-save'],
                    'class' => User::class,
                    "choices" => $this->collection(
                        $this->userRepository->findAll()
                    )
                        ->sortBy(fn (User $user) => $user->getName())
                        ->toArray()
                ]));
        }
        $builder
            ->add('wording', TextType::class, $this->optionsMerge('wording', [
                'label_attr' => ['class' => 'text-save']
            ]))
            ->add('cashInFlows', IntegerType::class, $this->optionsMerge('cash_in_flow.amount', [
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
            'data_class' => CashInFlows::class,
            "user_required" => false
        ]);
    }
}
