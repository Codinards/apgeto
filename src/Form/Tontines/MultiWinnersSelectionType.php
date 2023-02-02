<?php

namespace App\Form\Tontines;

use App\Entity\Tontines\Unity;
use App\Entity\Tontines\MultiWinnersSelection;
use App\Form\AppAbstractType;
use App\Form\Extensions\EntityType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MultiWinnersSelectionType extends AppAbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("winners", ChoiceType::class, $this->optionsMerge(
            "Choisir les membres bénéficiaires",
            [
                "choices" => $options["selected_data"],
                "expanded" => true,
                "multiple" => true,
                "choice_label" => fn (Unity $value, $index, $choice) => $value->getTontineur()->getName(),

            ]
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MultiWinnersSelection::class,
            "selected_data" => []
        ]);
    }
}
