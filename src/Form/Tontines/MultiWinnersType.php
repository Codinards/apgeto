<?php

namespace App\Form\Tontines;

use App\Entity\Tontines\MultiWinners;
use App\Entity\Tontines\Unity;
use App\Entity\Tontines\MultiWinnersSelection;
use App\Form\AppAbstractType;
use App\Tools\Entity\DataProvider;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MultiWinnersType extends AppAbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var MultiWinners $data  */
        $data = $builder->getData();
        DataProvider::getInstance()->setData($data->getWinners());
        $builder->add("winners", CollectionType::class, $this->optionsMerge(
            "Choisir les membres bénéficiaires",
            [
                "label" => null,
                "entry_type" => UnityWonType::class
            ]
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MultiWinners::class
        ]);
    }
}
