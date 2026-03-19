<?php

namespace App\Form;

use App\Entity\HomeHeroPhoto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @brief Form type for HomeHeroPhoto entity.
 *
 * @date 2026-03-19
 * @author Stephane H.
 */
class HomeHeroPhotoType extends AbstractType
{
    /**
     * @brief Builds the HomeHeroPhoto form.
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array<string, mixed> $options The form options.
     * @return void
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('altFr', TextType::class, [
                'label' => 'back.home_hero.photo.alt_fr',
                'required' => false,
            ])
            ->add('altDe', TextType::class, [
                'label' => 'back.home_hero.photo.alt_de',
                'required' => false,
            ])
            ->add('position', IntegerType::class, [
                'label' => 'back.home_hero.photo.position',
                'attr' => ['min' => 0],
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'back.home_hero.photo.is_active',
                'required' => false,
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'back.home_hero.photo.image_file',
                'mapped' => false,
                'required' => !$options['is_edit'],
                'help' => 'back.home_hero.photo.image_help',
                'constraints' => array_filter([
                    !$options['is_edit'] ? new NotBlank(message: 'Image requise.') : null,
                    new File(
                        maxSize: '6M',
                        mimeTypes: ['image/jpeg', 'image/jpg', 'image/png'],
                        mimeTypesMessage: 'Format accepté : JPEG ou PNG.'
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
     * @date 2026-03-19
     * @author Stephane H.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => HomeHeroPhoto::class,
            'is_edit' => false,
        ]);
        $resolver->setAllowedTypes('is_edit', 'bool');
    }
}

