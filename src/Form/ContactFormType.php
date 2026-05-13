<?php

namespace App\Form;

use App\DTO\ContactFormDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse email',
                'empty_data' => '',
            ])
            ->add('name', TextType::class, [
                'label' => 'Votre nom',
                'empty_data' => '',
            ])
            ->add('service', ChoiceType::class, [
                'label' => 'Choisissez le service concerné',
                'choices'  => [
                    'Service clients'      => 'clients@recette.com',
                    'Service commercial'   => 'commerce@recette.com',
                    'Service technique'    => 'technique@recette.com',
                    'Service comptabilité' => 'comptabilite@recette.com',
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
                'empty_data' => '',
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Envoyer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactFormDTO::class,
        ]); 
    }
}