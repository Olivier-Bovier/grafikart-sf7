<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Recipe;
use App\Form\FormListenerFactory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function __construct(private FormListenerFactory $listenerFactory) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la catégorie',
                'empty_data' => '',
            ])
            ->add('slug', TextType::class, [
                'empty_data' => '',
                'required' => false,
            ])
/*             ->add('recipes', EntityType::class, [
                'class' => Recipe::class,
                'choice_label' => 'title',
                'expanded' => true,
                'multiple' => true,
                'by_reference' => false
            ]) */
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary mt-3']
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->listenerFactory->autoSlug('name'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->listenerFactory->timestamps())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
