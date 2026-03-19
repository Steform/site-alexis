<?php

namespace App\Form;

use App\Entity\AboutSection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @brief Form type for AboutSection entity.
 *
 * @date 2026-03-18
 * @author Stephane H.
 */
class AboutSectionType extends AbstractType
{
    /**
     * @brief Builds the AboutSection form.
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
            ->add('leadFr', TextType::class, [
                'label' => 'back.about.section.lead_fr',
                'required' => true,
            ])
            ->add('leadDe', TextType::class, [
                'label' => 'back.about.section.lead_de',
                'required' => true,
            ])
            ->add('contentFr', TextareaType::class, [
                'label' => 'back.about.section.content_fr',
                'required' => true,
                'attr' => ['rows' => 8],
            ])
            ->add('contentDe', TextareaType::class, [
                'label' => 'back.about.section.content_de',
                'required' => true,
                'attr' => ['rows' => 8],
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
            'data_class' => AboutSection::class,
        ]);
    }
}

