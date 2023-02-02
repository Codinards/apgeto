<?php

namespace App\Form\Tontines;

use App\Entity\Tontines\Tontinetype;
use App\Form\AppAbstractType;
use App\Tools\Twig\LocalLanguages;
use App\Twig\TranslatorTwigExtension;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TontinetypeType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Tontinetype $type */
        $type = $builder->getData();
        $yes = $this->trans('yes');
        $no = $this->trans('no');
        $builder
            ->add('name', null, $this->optionsMerge('name'))
            ->add('cotisation', null, $this->optionsMerge('cotisation'))
            ->add('hasAchat', null, $this->optionsMerge('has.achat', [
                'row_attr' => [
                    'class' => 'tontine_has_achat'
                ],
                'label_attr' => [
                    'class' => 'text-edit'
                ]
            ]))
            ->add('minAchat', null, $this->optionsMerge(
                'min.achat',
                [
                    'row_attr' => [
                        'class' => 'tontine_min_achat'
                    ]
                ]
            ))
            //->add('isCurrent', null, $this->optionsMerge('is.current'))
            ->add('hasAvaliste', ChoiceType::class, $this->optionsMerge('has.avaliste', [
                'expanded' => false,
                'choices' => [
                    $yes => 1,
                    $no => 0
                ]
            ]))
            ->add('hasMultipleTontine', ChoiceType::class, $this->optionsMerge(
                'has.multiple.tontine',
                [
                    'expanded' => false,
                    'choices' => [
                        $yes => 1,
                        $no => 0
                    ]
                ]
            ))
            ->add('hasAmend', CheckboxType::class, $this->optionsMerge('has.amend', [
                'data' => ($type->getMinAmend() || $type->getAmend()),
                'mapped' => false,
                'required' => false,
                'row_attr' => [
                    'class' => 'tontine_has_amend'
                ],
                'label_attr' => [
                    'class' => 'text-edit'
                ]
            ]))
            ->add('amend', null, $this->optionsMerge('amend', [
                'row_attr' => [
                    'class' => 'tontine_amend'
                ]
            ]))
            ->add('minAmend', null, $this->optionsMerge('min.amend', [
                'row_attr' => [
                    'class' => 'tontine_min_amend'
                ]
            ]))

            //->add('closePeriod')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tontinetype::class,
        ]);
    }
}
