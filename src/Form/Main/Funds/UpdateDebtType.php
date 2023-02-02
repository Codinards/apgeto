<?php

namespace App\Form\Main\Funds;

use App\Form\AppAbstractType;
use App\Entity\Main\Funds\Debt;
use App\Form\Extensions\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateDebtType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Debt $data */
        $data = $builder->getData();
        $avalistesIds = $this->collection($data->getAvalistes()->toArray())
            ->map(fn ($item) => $item->getId())->toArray();
        $observations = $data->resolveObservations();

        $builder
            ->add('wording')
            ->add('observations', null, $this->optionsMerge("observations", [
                "data" => empty($observations) ? null : $observations["observations"]
            ]))
            ->add('createdAt')
            // ->add('avalistes', null, $this->optionsMerge("avalistes", [
            //     "multiple" => true,
            //     "expanded" => true,
            //     "choice_attr" => function ($choice, $index, $value) use ($data, $avalistesIds) {
            //         return in_array($choice->getId(), $avalistesIds) ? ["checked" => "checked", "class" => "bg-save"] : [];
            //     }
            // ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Debt::class,
        ]);
    }
}
