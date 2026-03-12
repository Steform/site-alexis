<?php

namespace App\Form;

use App\Dto\DevisRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for quote request (devis).
 *
 * @author Stephane H.
 * @created 2026-03-11
 *
 * @inputs  FormBuilderInterface, OptionsResolver
 * @outputs DevisRequest form
 */
class DevisType extends AbstractType
{
    /**
     * Builds the devis form with validation.
     *
     * @param FormBuilderInterface $builder
     * @param array<string, mixed> $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Votre nom'],
                'constraints' => [new NotBlank(['message' => 'Le nom est requis.'])],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'votre@email.fr'],
                'constraints' => [
                    new NotBlank(['message' => 'L\'email est requis.']),
                    new Email(['message' => 'Email invalide.']),
                ],
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'attr' => ['placeholder' => '06 12 34 56 78'],
                'required' => false,
            ])
            ->add('vehicule', TextType::class, [
                'label' => 'Véhicule',
                'attr' => ['placeholder' => 'Ex. : Renault Clio 2020'],
                'constraints' => [new NotBlank(['message' => 'Le véhicule est requis.'])],
            ])
            ->add('typePrestation', ChoiceType::class, [
                'label' => 'Type de prestation',
                'choices' => [
                    'Réparation carrosserie' => 'reparation_carrosserie',
                    'Peinture' => 'peinture',
                    'Débosselage' => 'debosselage',
                    'Pare-brise / Vitrages' => 'pare_brise',
                    'Entretien / Mécanique' => 'entretien',
                    'Autre' => 'autre',
                ],
                'placeholder' => 'Sélectionnez...',
                'constraints' => [new NotBlank(['message' => 'Le type de prestation est requis.'])],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'attr' => ['rows' => 4, 'placeholder' => 'Décrivez votre demande...'],
                'constraints' => [new NotBlank(['message' => 'Le message est requis.'])],
            ])
            ->add('website', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'd-none',
                    'tabindex' => '-1',
                    'autocomplete' => 'off',
                ],
            ])
        ;
    }

    /**
     * Configures form options.
     *
     * @param OptionsResolver $resolver
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DevisRequest::class,
        ]);
    }
}
