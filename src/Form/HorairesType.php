<?php

namespace App\Form;

use App\Entity\Horaires;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type for Horaires entity.
 *
 * @author Stephane H.
 * @created 2026-03-11
 *
 * @inputs  FormBuilderInterface, OptionsResolver
 * @outputs Horaires form
 */
class HorairesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('jour', ChoiceType::class, [
                'label' => 'Jour',
                'choices' => [
                    'Lundi' => 'lundi',
                    'Mardi' => 'mardi',
                    'Mercredi' => 'mercredi',
                    'Jeudi' => 'jeudi',
                    'Vendredi' => 'vendredi',
                    'Samedi' => 'samedi',
                    'Dimanche' => 'dimanche',
                ],
                'disabled' => $options['edit_mode'] ?? false,
            ])
            ->add('heureDebutMatin', TimeType::class, [
                'label' => 'Heure début matin',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('heureFinMatin', TimeType::class, [
                'label' => 'Heure fin matin',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('heureDebutApresMidi', TimeType::class, [
                'label' => 'Heure début après-midi',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('heureFinApresMidi', TimeType::class, [
                'label' => 'Heure fin après-midi',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Commentaire FR (ex. fermé)',
                'required' => false,
            ])
            ->add('commentaireDe', TextareaType::class, [
                'label' => 'Commentaire DE',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Horaires::class,
            'edit_mode' => false,
        ]);
        $resolver->setAllowedTypes('edit_mode', 'bool');
    }
}
