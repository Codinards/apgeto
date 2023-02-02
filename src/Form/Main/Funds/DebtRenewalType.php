<?php

namespace App\Form\Main\Funds;

use App\Entity\Main\Funds\DebtRenewal;
use App\Form\AppAbstractType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function foo\func;

class DebtRenewalType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var DebtRenewal $data */
        $data = $builder->getData();
        $data->renewalOutflow = $data->getAccount()->getCashBalances() > $data->getAmount();
        $builder
            ->add('wording')
            ->add('amount')
            ->add("observation")
            ->add('createdAt', null, $this->optionsMerge(
                "createdAt",
                ['data' => $data->getDebt()->getRenewalDate()]
            ))
            ->add(
                'renewalOutflow',
                ChoiceType::class,
                $this->optionsMerge(
                    "Retirer automatiquement le montant de reconduction sur le fond du membre",
                    [
                        // "label_attr" => ["class" => "text-edit"],
                        "choices" => [
                            "Retirer Sur le fond" => DebtRenewal::FUND_SUBSTRACT,
                            "Ajouter au prêt" => DebtRenewal::DEBT_ADD,
                            "Ne pas retirer" => DebtRenewal::ANY_ACTION
                        ],
                        "expanded" => true,
                        "attr" => [
                            'style' => 'display:flex;align-items:center;justify-content:space-around'
                        ],
                        "choice_attr" => function ($value, $option, $index) {
                            if ($value === true) return ["class" => "text-purple"];
                            return ["class" => "text-chocolate"];
                        },
                    ]
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DebtRenewal::class,
        ]);
    }
}
