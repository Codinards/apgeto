<?php

namespace App\Form\Tontines;

use App\Entity\Tontines\Unity;
use App\Entity\Tontines\Tontine;
use App\Entity\Tontines\Tontineur;
use App\Entity\Tontines\TontineurData;
use App\Form\AppAbstractType;
use App\Tools\Entity\DataProvider;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;

class UnityWonType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        /** @var Unity $unity */
        $unity = $builder->getData();
        if ($unity === null) {
            $unity = DataProvider::getInstance()->getData();
            $options["data"] = $unity;
        }
        $tontine = $unity->getTontine();
        if ($tontine->getType()->getHasAchat()) {
            $builder
                ->add('achat', IntegerType::class, $this->optionsMerge('unity.price', [
                    'attr' => [
                        'value' => $unity->getIsDemiNom() ? (int) ($tontine->getType()->getMinAchat()) : $tontine->getType()->getMinAchat()
                    ],
                    'constraints' => [
                        new Range([
                            'min' => $unity->getIsDemiNom() ? (int) ($tontine->getType()->getMinAchat()) : $tontine->getType()->getMinAchat()
                        ])
                    ]
                ]));
        }
        $builder->add('benefitAt', null, $this->optionsMerge('date'), [
            'required' => true,
            'constraints' => [
                new NotBlank(),
                new DateTime()
            ]
        ]);
        if ($tontine->getType()->getHasAvaliste()) {
            $builder
                ->add('avaliste', EntityType::class, $this->optionsMerge('avaliste', [
                    'class' => TontineurData::class,
                    'choices' => $this->collection($tontine->getTontineurData()->toArray())->sortBy(fn ($item) => $item->getTontineur()->getName()),
                    'required' => true
                ]));
        }
        $builder
            ->add('observation', null, $this->optionsMerge('observation', []));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Unity::class,
        ]);
    }
}
