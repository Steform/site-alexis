<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\ServiceProcessStep;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @brief Form type for ServiceProcessStep entity.
 *
 * @date 2026-03-22
 * @author Stephane H.
 */
class ServiceProcessStepType extends AbstractType
{
    /**
     * @brief Builds the ServiceProcessStep form.
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array<string, mixed> $options The form options.
     * @return void
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('labelFr', TextType::class, [
                'label' => 'back.service_process_step.label_fr',
                'required' => false,
                'attr' => ['maxlength' => 500],
            ])
            ->add('labelDe', TextType::class, [
                'label' => 'back.service_process_step.label_de',
                'required' => false,
                'attr' => ['maxlength' => 500],
            ])
            ->add('position', IntegerType::class, [
                'label' => 'back.service_process_step.position',
                'attr' => ['min' => 0],
            ])
        ;
    }

    /**
     * @brief Configures form options.
     *
     * @param OptionsResolver $resolver The options resolver.
     * @return void
     * @date 2026-03-22
     * @author Stephane H.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ServiceProcessStep::class,
        ]);
    }
}
