<?php

namespace App\Form\Tontines;

use App\Entity\Tontines\Tontine;
use App\Entity\Tontines\Tontinetype as TontinesTontinetype;
use App\Form\AppAbstractType;
use App\Form\Extensions\EntityType;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TontineUpdateType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $languages = $this->languageProvider->getLocaleKey();
        $yes = $this->trans('yes');
        $no = $this->trans('no');

        $defaultName = $builder->getData()->getType() . ' No: ' . (new DateTime())->format($languages != 'en' ? 'd/m/Y' : 'm/d/Y');
        $options['attr']['class'] = "row";
        $builder
            ->add('name', null, $this->optionsMerge('tontine.name', [
                'attr' => [
                    'style' => 'font-weight: bold',
                    'placeholder' => $defaultName
                ],
                'label_attr' => [
                    'style' => 'font-size:1.1rem;',
                    'class' => 'text-save'
                ],
                'required' => false
            ]))
            ->add('amount', IntegerType::class, $this->optionsMerge('amount.to.benefit', [
                'required' => true,
                'attr' => [
                    'style' => 'font-weight: bold'
                ],
                'label_attr' => [
                    'style' => 'font-size:1.1rem;',
                    'class' => 'text-save'
                ],
            ]))
            ->add('createdAt', null, $this->optionsMerge(
                'created_at',
                [
                    'attr' => [
                        'style' => 'font-weight: bold'
                    ],
                    'label_attr' => [
                        'style' => 'font-size:1.1rem;',
                        'class' => 'text-save'
                    ],
                ]
            ))
            ->add('addMember', ChoiceType::class, $this->optionsMerge('tontine.add.member', [
                'expanded' => false,
                'choices' => [
                    $yes => 1,
                    $no => 0
                ],
                'attr' => [
                    'style' => 'font-weight: bold'
                ],
                'label_attr' => [
                    'style' => 'font-size:1.1rem;',
                    'class' => 'text-save'
                ],
            ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tontine::class,
        ]);
    }
}
