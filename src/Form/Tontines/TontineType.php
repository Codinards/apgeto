<?php

namespace App\Form\Tontines;

use App\Entity\Tontines\Tontine;
use App\Entity\Tontines\Tontinetype as TontinesTontinetype;
use App\Entity\Tontines\TontineurData;
use App\Form\AppAbstractType;
use App\Form\Extensions\EntityType;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TontineType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $options['attr']['class'] = "row";

        if (!$builder->getData()->getType()) {
            $builder
                ->add('type', EntityType::class, $this->optionsMerge('tontinetype', [
                    'class' => TontinesTontinetype::class
                ]));
        }
        if (!isset($options['attr']['add'])) {
            $builder
                ->add('amount', IntegerType::class, $this->optionsMerge('amount.to.benefit', [
                    'required' => true,
                ]))
                ->add('createdAt', null, $this->optionsMerge('created_at'));
        } else {
            unset($options['attr']['add']);
        }


        $builder
            ->add('tontineurData', CollectionType::class, $this->optionsMerge(false, [
                'entry_type' => TontineurType::class,
                'label' => false,
                'entry_options' => [
                    'label' => false,
                    'attr' => [
                        'class' => 'row form_tontineur',
                    ]
                ]
            ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tontine::class,
        ]);
    }
}
