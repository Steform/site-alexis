<?php

namespace App\Form;

use App\Entity\DevisTypePrestation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for DevisTypePrestation entity.
 *
 * @author Stephane H.
 * @date 2026-03-19
 */
class DevisTypePrestationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'back.devis_type.code',
                'attr' => ['placeholder' => 'ex: reparation_carrosserie'],
                'constraints' => [new NotBlank()],
            ])
            ->add('label', TextType::class, [
                'label' => 'back.devis_type.label_fr',
                'constraints' => [new NotBlank()],
            ])
            ->add('labelDe', TextType::class, [
                'label' => 'back.devis_type.label_de',
                'required' => false,
            ])
            ->add('ordre', IntegerType::class, [
                'label' => 'back.devis_type.ordre',
                'data' => 0,
            ])
            ->add('actif', CheckboxType::class, [
                'label' => 'back.devis_type.actif',
                'required' => false,
                'data' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DevisTypePrestation::class,
            'translation_domain' => 'back',
        ]);
    }
}
