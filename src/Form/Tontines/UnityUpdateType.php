<?php

namespace App\Form\Tontines;

use App\Entity\Tontines\Unity;
use App\Entity\Tontines\Tontine;
use App\Entity\Tontines\Tontineur;
use App\Entity\Tontines\TontineurData;
use App\Form\AppAbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class UnityUpdateType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Unity $unity */
        $unity = $builder->getData();
        $tontine = $unity->getTontine();
        if ($tontine->getType()->getHasAchat()) {
            $builder
                ->add('achat', IntegerType::class, $this->optionsMerge('unity.price', [
                    'constraints' => [
                        new Range([
                            'min' => $tontine->getType()->getMinAchat(),
                        ])
                    ],
                    'row_attr' => ['class' => 'unity_update_input']
                ]));
        }
        $builder->add('benefitAt', null, $this->optionsMerge('date', [
            'row_attr' => ['class' => 'unity_update_input']
        ]));
        if ($tontine->getType()->getHasAvaliste()) {
            $builder
                ->add('avaliste', EntityType::class, $this->optionsMerge('avaliste', [
                    'class' => TontineurData::class,
                    'choices' => $tontine->getTontineurData(),
                    'required' => false,
                    'row_attr' => ['class' => 'unity_update_input']
                ]));
        }
        $builder
            ->add('observation', null, $this->optionsMerge('observation', [
                'row_attr' => ['class' => 'unity_update_input']
            ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Unity::class,
        ]);
    }
}
