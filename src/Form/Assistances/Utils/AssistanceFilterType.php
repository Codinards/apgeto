<?php

namespace App\Form\Assistances\Utils;

use App\Entity\Assistances\Assistance;
use App\Entity\Assistances\AssistanceType;
use App\Entity\Assistances\Contributor;
use App\Entity\Utils\AssistanceFilter;
use App\Form\AppAbstractType;
use App\Repository\Assistances\AssistanceRepository;
use App\Repository\Assistances\ContributorRepository;
use App\Repository\Main\Users\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssistanceFilterType extends AppAbstractType
{
    protected $assistanceRepository;

    protected $UserRepository;

    protected $contributorRepository;

    public function __construct(AssistanceRepository $assistanceRepository, UserRepository $UserRepository, ContributorRepository $contributorRepository)
    {
        $this->assistanceRepository = $assistanceRepository;
        $this->UserRepository = $UserRepository;
        $this->contributorRepository = $contributorRepository;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = [];
        /** @var Assistance[] $assistances */
        $assistances = $this->assistanceRepository->findByUserDistinct();
        if (!empty($assistances)) {
            foreach ($assistances as $assistance) {
                $choices[$assistance->getUser()->getUsername()] = $assistance->getUser()->getId();
            }
        }

        $years = array_map(fn ($record) => $record['year'], $this->assistanceRepository->findYears());

        $years = array_combine($years, $years);

        $none = $this->trans('none');
        $by = $this->trans('by');
        $builder
            ->add('type', EntityType::class, $this->optionsMerge('type', [
                'label' => $by . ' ' . $this->trans('type'),
                'required' => false,
                'class' => AssistanceType::class,
                'label_attr' => ['class' => 'text-dark']
            ]))
            ->add('member', ChoiceType::class, $this->optionsMerge('member', [
                'label' => $by . ' ' . $this->trans('member'),
                'required' => false,
                'choices' => $choices,
                'label_attr' => ['class' => 'text-dark']
            ]))
            ->add('period', ChoiceType::class, $this->optionsMerge('year', [
                'label' => $by . ' ' . $this->trans('year'),
                'required' => false,
                'label_attr' => ['class' => 'text-dark'],
                'choices' => $years
            ]))
            ->add('contributor', EntityType::class, $this->optionsMerge('contributor', [
                'label' => $by . ' ' . $this->trans('contributor'),
                'required' => false,
                'class' => Contributor::class,
                'label_attr' => ['class' => 'text-dark'],
            ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AssistanceFilter::class,
        ]);
    }
}
