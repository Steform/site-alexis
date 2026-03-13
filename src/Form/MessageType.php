<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type for Message entity (bounded-date messages).
 *
 * @author Stephane H.
 * @created 2026-03-11
 *
 * @inputs  FormBuilderInterface, OptionsResolver
 * @outputs Message form
 */
class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenu', TextareaType::class, [
                'label' => 'Contenu FR (ex. Garage fermé du 12 au 22 février)',
                'attr' => ['rows' => 3],
            ])
            ->add('contenuDe', TextareaType::class, [
                'label' => 'Contenu DE (z.B. Garage vom 12. bis 22. Februar geschlossen)',
                'attr' => ['rows' => 3],
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Date et heure de début d\'affichage',
                'widget' => 'single_text',
            ])
            ->add('dateFin', DateTimeType::class, [
                'label' => 'Date et heure de fin d\'affichage',
                'widget' => 'single_text',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
