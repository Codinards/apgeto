<?php

namespace App\Form\Assistances;

use App\Entity\Assistances\AssistanceType;
use App\Form\AppAbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\ArrayToPartsTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BaseAssistanceType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, $this->optionsMerge('', [
                'label_attr' => [
                    'class' => 'text-save'
                ]
            ]))
            ->add('amount', null, $this->optionsMerge('', [
                'label_attr' => [
                    'class' => 'text-save'
                ]
            ]))
            ->add('isAmount', ChoiceType::class, $this->optionsMerge(
                'contribution.type',
                [
                    'expanded' => false,
                    'multiple' => false,
                    //'required' => false,
                    'choices' =>
                    [
                        $this->trans('is_unity_amount') => 1,
                        $this->trans('is_contribution_amount') => 0
                    ],
                    'attr' => [
                        'class' => 'row',
                        'style' => 'display:flex;align-items:center;justify-content:space-around'
                    ],
                    'label_attr' => [
                        'class' => 'text-save'
                    ],
                ]
            ));
        /*$options['attr']['novalidate'] = 'novalidate';
        $yes = $this->trans('yes');
        $no = $this->trans('no');
        $builder
            ->add('name')
            ->add('hasAmount', ChoiceType::class, $this->optionsMerge('has_amount', [
                'mapped' => false,
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    $no => 0,
                    $yes => 1

                ],
                'choice_attr' => [
                    'class' => 'text-save'
                ],
                'attr' => [
                    'class' => 'has_amount_input row',
                    'style' => 'display:flex;align-items:center;justify-content:space-around'
                ],
            ])) //->addViewTransformer(new ArrayToPartsTransformer([]))
            ->add('amount', IntegerType::class, $this->optionsMerge('amount', [
                'row_attr' => [
                    'class' => 'amount_parent_input'
                ]
            ]))
            ->add('hasContribution', ChoiceType::class, $this->optionsMerge('has_contribution', [
                'mapped' => false,
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    $no => 0,
                    $yes => 1

                ],
                'attr' => [
                    'class' => 'has_contribution_input row',
                    'style' => 'display:flex;align-items:center;justify-content:space-around'
                ],
            ]))
            ->add('contribution', IntegerType::class, $this->optionsMerge('contribution', [
                'row_attr' => [
                    'class' => 'contribution_parent_input'
                ]
            ]));*/
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AssistanceType::class,
        ]);
    }
}
