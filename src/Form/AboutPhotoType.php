<?php

namespace App\Form;

use App\Entity\AboutPhoto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

/**
 * @brief Form type for AboutPhoto entity (slider image).
 *
 * @date 2026-03-18
 * @author Stephane H.
 */
class AboutPhotoType extends AbstractType
{
    /**
     * @brief Builds the AboutPhoto form.
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array<string, mixed> $options The form options.
     * @return void
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('altFr', TextType::class, [
                'label' => 'back.about.photo.alt_fr',
                'required' => false,
            ])
            ->add('altDe', TextType::class, [
                'label' => 'back.about.photo.alt_de',
                'required' => false,
            ])
            ->add('position', IntegerType::class, [
                'label' => 'back.about.photo.position',
                'required' => false,
                'constraints' => [
                    new PositiveOrZero(),
                ],
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'back.about.photo.is_active',
                'required' => false,
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'back.about.photo.image_file',
                'mapped' => false,
                'required' => !$options['is_edit'],
                'help' => 'back.about.photo.image_help',
                'constraints' => array_filter([
                    !$options['is_edit'] ? new NotBlank(message: 'Image requise.') : null,
                    new File(
                        maxSize: '5M',
                        mimeTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'],
                        mimeTypesMessage: 'Format accepté : JPEG, PNG, WebP ou GIF.'
                    ),
                ]),
            ])
        ;
    }

    /**
     * @brief Configures form options.
     *
     * @param OptionsResolver $resolver The options resolver.
     * @return void
     * @date 2026-03-18
     * @author Stephane H.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AboutPhoto::class,
            'is_edit' => false,
        ]);
        $resolver->setAllowedTypes('is_edit', 'bool');
    }
}

