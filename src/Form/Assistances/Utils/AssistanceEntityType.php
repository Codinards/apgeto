<?php

namespace App\Form\Assistances\Utils;

use App\Entity\Utils\AssistanceContributor;
use App\Entity\Utils\AssistanceEntity;
use App\Form\AppAbstractType;
use App\Form\Extensions\CollectionType;
use App\Tools\Entity\ContributorData;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssistanceEntityType extends AppAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var AssistanceEntity $contributor */
        $assistance = $builder->getData();
        $yes = $this->trans('yes');
        $no = $this->trans('no');
        $options['attr']['data-total_count'] = $assistance->getContributors()->count();
        $options['attr']['class'] = 'assistance_form';
        ContributorData::getInstance()->setContributors($assistance->getContributors());
        $builder->add('contributors', CollectionType::class, $this->optionsMerge('contributors', [
            'entry_type' => ContributorType::class,
        ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AssistanceEntity::class,
        ]);
    }
}
