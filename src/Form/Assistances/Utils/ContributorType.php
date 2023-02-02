<?php

namespace App\Form\Assistances\Utils;

use App\Entity\Utils\AssistanceContributor;
use App\Form\AppAbstractType;
use App\Tools\Entity\ContributorData;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class ContributorType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var AssistanceContributor $contributor */
        $index = ContributorData::$index;
        $contributor = ContributorData::getInstance()->getContributor();
        $yes = $this->trans('yes');
        $no = $this->trans('no');
        $options['attr']['data-balance'] = $contributor->getAccount()->getCashBalances();
        $options['attr']['class'] = 'contributor_form_' . $contributor->getId();
        $builder
            ->add('member', CheckboxType::class, $this->optionsMerge('member', [
                'label' => $contributor->getName(),
                'required' => false,
                'attr' => [
                    //'disabled' => true,
                    'checked' => true,
                    'data-index' => $index,
                ]
            ])) //->addModelTransformer(new ContributorToCheckBoxTransformer)
            ->add('amount', IntegerType::class, $this->optionsMerge('amount', [
                'attr' => [
                    'class' => 'contributor_amount_input input_' . $contributor->getId(),
                    'value' => $contributor->getAmount() ? $contributor->getAmount() : 0,

                ],
                'constraints' => [
                    new Range([
                        'min' => $contributor->getType()->getAmount() ?? 0,
                    ])
                ]
            ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AssistanceContributor::class,
        ]);
    }
}
