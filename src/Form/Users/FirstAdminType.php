<?php

namespace App\Form\Users;

use App\Entity\Main\Users\User;
use App\Entity\Utils\FirstConfigEntity;
use App\Form\AppAbstractType;
use App\Repository\Main\Users\RoleRepository;
use App\Tools\AppConstants;
use App\Tools\Languages\LangResolver;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class FirstAdminType extends AppAbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', TextType::class, $this->optionsMerge('pseudo', [
                'label_attr' => [
                    'class' => 'text-edit'
                ],
            ]))
            ->add('password', PasswordType::class, $this->optionsMerge('password', [
                'label_attr' => [
                    'class' => 'text-edit'
                ],
            ]))
            ->add('username', TextType::class, $this->optionsMerge('username', [
                'label_attr' => [
                    'class' => 'text-edit'
                ],
            ]))
            ->add('address', TextType::class, $this->optionsMerge('address', [
                'label_attr' => [
                    'class' => 'text-edit'
                ],
            ]))
            ->add('telephone', TextType::class, $this->optionsMerge(
                'telephone',
                [
                    'label_attr' => [
                        'class' => 'text-edit'
                    ],
                ]
            ))
            ->add('appName', TextType::class, $this->optionsMerge(
                'app_name',
                [
                    'label_attr' => [
                        'class' => 'text-edit'
                    ],
                ]
            ))
            ->add('logo', FileType::class, $this->optionsMerge('logo', [
                'required' => false,
                'label_attr' => [
                    'class' => 'text-edit'
                ],
            ]))
            ->add('defaultLanguage', ChoiceType::class, $this->optionsMerge('default_language', [
                'choices' => array_combine(
                    array_values(LangResolver::$languages_name),
                    array_keys(LangResolver::$languages_name)
                ),
                'multiple' => false,
                'expanded' => true,
                'attr' => [
                    'class' => 'row',
                    'style' => 'display:flex;align-items:center;justify-content:space-around'
                ],
                'label_attr' => [
                    'class' => 'text-edit'
                ],
            ]))
            ->add('moneyDevise', ChoiceType::class, $this->optionsMerge('money_devise', [
                'choices' => [
                    'franc CFA' => 'XAF',
                    'naira N' => 'NGN',
                    'euro' => '€',
                    'dollar' => '$'
                ],
                'label_attr' => [
                    'class' => 'text-edit'
                ],
            ]))
            ->add('fundCanBeNegative', ChoiceType::class, $this->optionsMerge('fund_can_be_negative', [
                'choices' => [
                    $this->trans('yes') => 'yes',
                    $this->trans('no') => 'no'
                ],
                'multiple' => false,
                'expanded' => true,
                'attr' => [
                    'class' => 'row',
                    'style' => 'display:flex;align-items:center;justify-content:space-around'
                ],
                'label_attr' => [
                    'class' => 'text-edit'
                ],
            ]))
            ->add('baseFundAmountToLoan', IntegerType::class, $this->optionsMerge(
                'base_amount_to_loan',
                [
                    'label_attr' => [
                        'class' => 'text-edit'
                    ],
                ]
            ))
            ->add('accountBaseAmount', IntegerType::class, $this->optionsMerge('account_base_amount', [
                'label_attr' => [
                    'class' => 'text-edit'
                ],
            ]))
            ->add('userCanGetMultipleLoan', ChoiceType::class, $this->optionsMerge(
                'user_can_get_multiple_loan',
                [
                    'choices' => [
                        $this->trans('yes') => 'yes',
                        $this->trans('no') => 'no'
                    ],
                    'multiple' => false,
                    'expanded' => true,
                    'attr' => [
                        'class' => 'row',
                        'style' => 'display:flex;align-items:center;justify-content:space-around'
                    ],
                    'label_attr' => [
                        'class' => 'text-edit'
                    ],
                ]
            ))
            ->add('interestRateToLoan', IntegerType::class, $this->optionsMerge('interest_rate_to_loan', [
                'label_attr' => [
                    'class' => 'text-edit'
                ],
                'attr' => [
                    'value' => AppConstants::$INTEREST_RATE * 100
                ],
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 100
                    ])
                ]
            ]))
            /*->add('insurranceAmount', IntegerType::class, $this->optionsMerge(
                'insurrance_amount',
                [
                    'label_attr' => [
                        'class' => 'text-edit'
                    ],
                ]
            ))*/
            ->add('unityMaxCount', IntegerType::class, $this->optionsMerge('unity_max_count', [
                'label_attr' => [
                    'class' => 'text-edit'
                ],
            ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FirstConfigEntity::class,
        ]);
    }
}
