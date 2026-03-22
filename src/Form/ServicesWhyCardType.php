<?php

namespace App\Form;

use App\Entity\ServicesWhyCard;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @brief Form type for ServicesWhyCard entity.
 *
 * @date 2026-03-21
 * @author Stephane H.
 */
class ServicesWhyCardType extends AbstractType
{
    /**
     * @brief Builds the ServicesWhyCard form.
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array<string, mixed> $options The form options.
     * @return void
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titleFr', TextType::class, [
                'label' => 'back.services_why_card.title_fr',
                'required' => true,
            ])
            ->add('titleDe', TextType::class, [
                'label' => 'back.services_why_card.title_de',
                'required' => true,
            ])
            ->add('textFr', TextareaType::class, [
                'label' => 'back.services_why_card.text_fr',
                'required' => true,
                'attr' => ['rows' => 4],
            ])
            ->add('textDe', TextareaType::class, [
                'label' => 'back.services_why_card.text_de',
                'required' => true,
                'attr' => ['rows' => 4],
            ])
            ->add('position', IntegerType::class, [
                'label' => 'back.services_why_card.position',
                'attr' => ['min' => 0],
            ])
        ;
    }

    /**
     * @brief Configures form options.
     *
     * @param OptionsResolver $resolver The options resolver.
     * @return void
     * @date 2026-03-21
     * @author Stephane H.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ServicesWhyCard::class,
        ]);
    }
}
