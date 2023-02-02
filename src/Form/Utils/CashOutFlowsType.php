<?php

namespace App\Form\Utils;

use App\Entity\Main\Users\User;
use App\Entity\Utils\CashOutFlows;
use App\Form\AppAbstractType;
use App\Form\Extensions\EntityType;
use App\Repository\Main\Users\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class CashOutFlowsType extends AppAbstractType
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
            "user_required" => false
        ]);
    }
}
