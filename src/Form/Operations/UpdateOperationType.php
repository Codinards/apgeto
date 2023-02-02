<?php

namespace App\Form\Operations;

use App\Entity\Main\Operations\Operation;
use App\Form\AppAbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateOperationType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Operation */
        $operation = $builder->getData();
        $builder
            ->add('wording', TextType::class, $this->optionsMerge('wording'));

        if ((int)$operation->getInflows() > 0) {
            $builder->add('inflows', IntegerType::class, $this->optionsMerge('inflow'));
        } else {
            $builder->add('outflows', IntegerType::class, $this->optionsMerge('outflow'));
        }
        $builder
            ->add('observation', null, $this->optionsMerge('observation'))
            ->add('createdAt', null, $this->optionsMerge('created_at'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Operation::class,
            // 'action_type' => null
        ]);
        // $resolver->setRequired('action_type');
    }
}
