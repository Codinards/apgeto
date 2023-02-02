<?php

namespace App\Form\Tontines;

use App\Entity\Tontines\Tontineur;
use App\Entity\Tontines\TontineurData;
use App\Form\AppAbstractType;
use App\Tools\AppConstants;
use App\Tools\Counter;
use App\Tools\Entity\DataProvider;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TontineurType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var TontineurData $data */
        $data = DataProvider::getInstance()->getData();
        if ($data) {


            $name = $this->trans('unity');
            $names = $this->trans('unities');
            $none = $this->trans('nona');

            $array = array_merge([], [
                'label' => $data->getTontineur()->getName(),
                'mapped' => true,
                'required' => false,
                'label_attr' => [
                    'style' => 'display:inline-block;font-size:1.2em;',
                    'class' => 'text-edit'
                ]
            ]);

            $choices = [];
            for ($i = 1; $i <= AppConstants::$TONTINEUR_MAX_COUNT; $i++) {
                $choices[$i . ' ' . ($i == 1 ? $name : $names)] = $i;
            }
            $choices[$none] = 0.5;

            $builder
                ->add('count', ChoiceType::class, $this->optionsMerge('part.numbers', [
                    'choices' => $choices,
                    'attr' => [
                        'class' => 'field-to-select',
                        'style' => 'color: #b88517;'
                    ],
                    'label_attr' => ['class' => 'text-show'],

                ]))
                ->add('demiNom', CheckboxType::class, $this->optionsMerge('half.part', [
                    'required' => false,
                    'attr' => [
                        'class' => 'field-to-select'
                    ],
                    'label_attr' => ['class' => 'text-edit']
                ]))
                ->add('isSelected', CheckboxType::class, $this->optionsMerge('choice', $array))
                ->add('name', HiddenType::class, $this->optionsMerge('name', [
                    'mapped' => false,
                    'attr' => [
                        'value' => $data->getTontineur()->getName()
                    ]
                ]));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TontineurData::class,
            "allow_extra_fields" => true
        ]);
    }
}
