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
                'label' => 'form.devis.name.label',
                'attr' => ['placeholder' => 'form.devis.name.placeholder'],
                'constraints' => [new NotBlank(['message' => 'validator.devis.name_required'])],
            ])
            ->add('email', EmailType::class, [
                'label' => 'form.devis.email.label',
                'attr' => ['placeholder' => 'form.devis.email.placeholder'],
                'constraints' => [
                    new NotBlank(['message' => 'validator.devis.email_required']),
                    new Email(['message' => 'validator.devis.email_invalid']),
                ],
            ])
            ->add('telephone', TelType::class, [
                'label' => 'form.devis.phone.label',
                'attr' => ['placeholder' => 'form.devis.phone.placeholder'],
                'required' => false,
            ])
            ->add('vehicule', TextType::class, [
                'label' => 'form.devis.vehicle.label',
                'attr' => ['placeholder' => 'form.devis.vehicle.placeholder'],
                'constraints' => [new NotBlank(['message' => 'validator.devis.vehicle_required'])],
            ])
            ->add('typePrestation', ChoiceType::class, [
                'label' => 'form.devis.type.label',
                'choices' => [
                    'form.devis.type.reparation' => 'reparation_carrosserie',
                    'form.devis.type.peinture' => 'peinture',
                    'form.devis.type.debosselage' => 'debosselage',
                    'form.devis.type.pare_brise' => 'pare_brise',
                    'form.devis.type.entretien' => 'entretien',
                    'form.devis.type.autre' => 'autre',
                ],
                'choice_translation_domain' => 'messages',
                'placeholder' => 'form.devis.type.placeholder',
                'constraints' => [new NotBlank(['message' => 'validator.devis.type_required'])],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'form.devis.message.label',
                'attr' => ['rows' => 4, 'placeholder' => 'form.devis.message.placeholder'],
                'constraints' => [new NotBlank(['message' => 'validator.devis.message_required'])],
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
