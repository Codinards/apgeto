<?php

namespace App\Form\Main\Users;

use App\Entity\Main\Users\Role;
use App\Entity\Main\Users\UserAction;
use App\Form\AppAbstractType;
use App\Form\Extensions\EntityType;
use App\Repository\Main\Users\UserActionRepository;
use App\Tools\Twig\LocalLanguages;
use App\Twig\TranslatorTwigExtension;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleType extends AppAbstractType
{
    protected $useractionRepo;

    public function __construct(TranslatorTwigExtension $appTranslator, LocalLanguages $languageProvider, UserActionRepository $useractionRepo)
    {
        parent::__construct($appTranslator, $languageProvider);
        $this->useractionRepo = $useractionRepo;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$locale = $this->languageProvider->getLocaleKey();
        $builder
            ->add('name', null, $this->optionsMerge('name'))
            ->add('title', null, $this->optionsMerge('title'))
            ->add('isDeletable', null, $this->optionsMerge('is.deletable'))
            ->add('userActions', EntityType::class, $this->optionsMerge('user.actions', [
                //'label' => $this->appTranslator->__u('user.actions', [], 'forms', $locale),
                'class' => UserAction::class,
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->useractionRepo->findIsUpdatable(),
                //'choice_filter' => fn (UserAction $action) => $action->getIsUpdatable() === true,
                'label_attr' => [
                    'class' => 'mb-1',
                ],
                'attr' => [
                    'style' => 'border: 2px solid grey; border-radius: 5px',
                    'class' => 'p-3 bg-show'
                ],
            ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Role::class,
        ]);
    }
}
