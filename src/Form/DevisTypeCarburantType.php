<?php

namespace App\Form;

use App\Entity\DevisTypeCarburant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for DevisTypeCarburant entity.
 *
 * @author Stephane H.
 * @date 2026-03-19
 */
class DevisTypeCarburantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'label' => 'back.devis_type_carburant.code',
                'attr' => ['placeholder' => 'ex: diesel'],
                'constraints' => [new NotBlank()],
            ])
            ->add('label', TextType::class, [
                'label' => 'back.devis_type_carburant.label_fr',
                'constraints' => [new NotBlank()],
            ])
            ->add('labelDe', TextType::class, [
                'label' => 'back.devis_type_carburant.label_de',
                'required' => false,
            ])
            ->add('ordre', IntegerType::class, [
                'label' => 'back.devis_type_carburant.ordre',
                'data' => 0,
            ])
            ->add('actif', CheckboxType::class, [
                'label' => 'back.devis_type_carburant.actif',
                'required' => false,
                'data' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DevisTypeCarburant::class,
            'translation_domain' => 'back',
        ]);
    }
}
