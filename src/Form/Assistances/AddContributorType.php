<?php

namespace App\Form\Assistances;

use App\Entity\Assistances\Contributor;
use App\Entity\Main\Users\User;
use App\Form\AppAbstractType;
use App\Tools\Entity\ContributorData;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddContributorType extends AppAbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $index = ContributorData::$index;
        /** @var User $contributor */
        $contributor = ContributorData::getInstance()->getContributor();
        $options['attr']['class'] = 'contributor_form_' . $contributor->getId();

        $builder
            ->add('select', CheckboxType::class, $this->optionsMerge('member', [
                'label' => $contributor->getName(),
                'required' => false,
                'attr' => [
                    //'disabled' => true,
                    'checked' => true,
                    'data-index' => $index,
                ]
            ]))
            ->add('amount', IntegerType::class, $this->optionsMerge('amount', [
                'attr' => [
                    'class' => 'contributor_amount_input input_' . $contributor->getId(),
                    'value' => /*$contributor->getAmount() ? $contributor->getAmount() :*/ 0,

                ]

            ]))
            ->add('index', HiddenType::class, $this->optionsMerge('index', [
                'attr' => [
                    'value' => $contributor->getId()
                ]
            ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contributor::class,
        ]);
    }
}
