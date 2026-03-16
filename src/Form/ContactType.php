<?php

namespace App\Form;

use App\Dto\ContactRequest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @brief Form type for public contact form.
 *
 * @date 2026-03-16
 * @author Stephane H.
 *
 * @inputs FormBuilderInterface $builder, array<string, mixed> $options
 * @outputs ContactRequest form
 */
class ContactType extends AbstractType
{
    /**
     * @brief Builds the contact form fields and validation.
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
            ->add('name', TextType::class, [
                'label' => 'form.contact.name.label',
                'attr' => ['placeholder' => 'form.contact.name.placeholder'],
                'constraints' => [new NotBlank(message: 'validator.contact.name_required')],
            ])
            ->add('email', EmailType::class, [
                'label' => 'form.contact.email.label',
                'attr' => ['placeholder' => 'form.contact.email.placeholder'],
                'constraints' => [
                    new NotBlank(message: 'validator.contact.email_required'),
                    new Email(message: 'validator.contact.email_invalid'),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'form.contact.message.label',
                'attr' => ['rows' => 5, 'placeholder' => 'form.contact.message.placeholder'],
                'constraints' => [new NotBlank(message: 'validator.contact.message_required')],
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

    /**
     * @brief Configures contact form options.
     *
     * @param OptionsResolver $resolver The options resolver.
     * @return void
     * @date 2026-03-16
     * @author Stephane H.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactRequest::class,
        ]);
    }
}

