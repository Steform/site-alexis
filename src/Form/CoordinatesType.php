<?php

namespace App\Form;

use App\Entity\Coordinates;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @brief Form type for Coordinates entity.
 *
 * @date 2026-03-16
 * @author Stephane H.
 *
 * @inputs FormBuilderInterface $builder, OptionsResolver $resolver
 * @outputs Coordinates form
 */
class CoordinatesType extends AbstractType
{
    /**
     * @brief Builds the coordinates form fields.
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array<string, mixed> $options The form options.
     * @return void
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('companyName', TextType::class, [
                'label' => 'back.coordinates.company_name',
            ])
            ->add('street', TextType::class, [
                'label' => 'back.coordinates.street',
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'back.coordinates.postal_code',
            ])
            ->add('city', TextType::class, [
                'label' => 'back.coordinates.city',
            ])
            ->add('phone', TelType::class, [
                'label' => 'back.coordinates.phone',
            ])
            ->add('email', EmailType::class, [
                'label' => 'back.coordinates.email',
            ])
            ->add('googleMapsEmbedUrl', TextType::class, [
                'label' => 'back.coordinates.google_maps_url',
                'required' => false,
            ])
            ->add('facebookUrl', TextType::class, [
                'label' => 'back.coordinates.facebook_url',
                'required' => false,
            ])
            ->add('instagramUrl', TextType::class, [
                'label' => 'back.coordinates.instagram_url',
                'required' => false,
            ])
        ;
    }

    /**
     * @brief Configures coordinates form options.
     *
     * @param OptionsResolver $resolver The options resolver.
     * @return void
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Coordinates::class,
        ]);
    }
}

