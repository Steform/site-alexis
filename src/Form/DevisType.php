<?php

namespace App\Form;

use App\Dto\DevisRequest;
use App\Entity\DevisTypeCarburant;
use App\Entity\DevisTypePrestation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for quote request (devis).
 *
 * @author Stephane H.
 * @created 2026-03-11
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
        /** @var DevisTypePrestation[] $typeChoices */
        $typeChoices = $options['type_prestation_choices'] ?? [];
        $useDe = ($options['locale'] ?? 'fr') === 'de';
        $choices = [];
        foreach ($typeChoices as $t) {
            $label = $useDe && $t->getLabelDe() ? $t->getLabelDe() : $t->getLabel();
            $choices[$label] = $t->getCode();
        }

        /** @var DevisTypeCarburant[] $carburantChoices */
        $carburantChoices = $options['type_carburant_choices'] ?? [];
        $carburantChoicesForm = [];
        foreach ($carburantChoices as $c) {
            $label = $useDe && $c->getLabelDe() ? $c->getLabelDe() : $c->getLabel();
            $carburantChoicesForm[$label] = $c->getCode();
        }

        $currentYear = (int) date('Y');
        $yearChoices = [];
        for ($y = $currentYear; $y >= $currentYear - 100; --$y) {
            $yearChoices[(string) $y] = $y;
        }

        $builder
            ->add('nom', TextType::class, [
                'label' => 'form.devis.name.label',
                'attr' => ['placeholder' => 'form.devis.name.placeholder'],
                'constraints' => [new NotBlank(message: 'validator.devis.name_required')],
            ])
            ->add('email', EmailType::class, [
                'label' => 'form.devis.email.label',
                'attr' => ['placeholder' => 'form.devis.email.placeholder'],
                'constraints' => [
                    new NotBlank(message: 'validator.devis.email_required'),
                    new Email(message: 'validator.devis.email_invalid'),
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
                'constraints' => [new NotBlank(message: 'validator.devis.vehicle_required')],
            ])
            ->add('anneeVehicule', ChoiceType::class, [
                'label' => 'form.devis.year.label',
                'choices' => $yearChoices,
                'placeholder' => 'form.devis.year.placeholder',
                'required' => false,
            ])
            ->add('typeCarburant', ChoiceType::class, [
                'label' => 'form.devis.fuel.label',
                'choices' => $carburantChoicesForm,
                'placeholder' => 'form.devis.fuel.placeholder',
                'required' => false,
            ])
            ->add('typePrestation', ChoiceType::class, [
                'label' => 'form.devis.type.label',
                'choices' => $choices,
                'placeholder' => 'form.devis.type.placeholder',
                'constraints' => [new NotBlank(message: 'validator.devis.type_required')],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'form.devis.message.label',
                'attr' => ['rows' => 4, 'placeholder' => 'form.devis.message.placeholder'],
                'constraints' => [new NotBlank(message: 'validator.devis.message_required')],
            ])
            ->add('photos', FileType::class, [
                'label' => 'form.devis.photos.label',
                'help' => 'form.devis.photos.help',
                'multiple' => true,
                'required' => false,
                'mapped' => true,
                'constraints' => [
                    new All([
                        new File(
                            maxSize: '5M',
                            mimeTypes: ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
                            mimeTypesMessage: 'validator.devis.photos_invalid',
                        ),
                    ]),
                ],
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DevisRequest::class,
            'type_prestation_choices' => [],
            'type_carburant_choices' => [],
            'locale' => 'fr',
        ]);
        $resolver->setAllowedTypes('type_prestation_choices', 'array');
        $resolver->setAllowedTypes('type_carburant_choices', 'array');
    }
}
