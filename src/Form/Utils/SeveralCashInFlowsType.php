<?php

namespace App\Form\Utils;

use App\Entity\Main\Users\User;
use App\Entity\Utils\SeveralCashInFlows;
use App\Form\AppAbstractType;
use App\Form\Extensions\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeveralCashInFlowsType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
            ]))
            ->add('targets', EntityType::class, $this->optionsMerge('Membres concernés', [
                "class" => User::class,
                "expanded" => true,
                "multiple" => true,
                "attr" => [
                    'class' => "bg-purple"
                ],
                'label_attr' => ['class' => 'text-save'],
                'choice_label' => fn (User $user) => $user->getName() . " ----- Solde Fond: " . number_format($user->getAccount()->getCashBalances(), 0, ",", " ") . "FCFA"
            ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SeveralCashInFlows::class,
        ]);
    }
}
