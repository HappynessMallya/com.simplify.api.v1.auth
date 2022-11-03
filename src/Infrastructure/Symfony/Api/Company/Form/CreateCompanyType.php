<?php

namespace App\Infrastructure\Symfony\Api\Company\Form;

use App\Application\Company\Command\CreateCompanyCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class CreateCompanyType
 * @package App\Infrastructure\Symfony\Api\Company\Form
 */
final class CreateCompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['constraints' => new NotBlank()])
            ->add('tin', TextType::class, ['constraints' => new NotBlank()])
            ->add('email', TextType::class, ['constraints' => new Email()])
            ->add('phone', TextType::class)
            ->add('address', TextType::class)
            ->add(
                'serial',
                TextType::class,
                [
                    'constraints' => new NotBlank()
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CreateCompanyCommand::class,
            'csrf_protection' => false,
            'is_edit' => false,
            'extra_fields_message' => 'Extra fields sent: {{ extra_fields }}'
        ]);
    }
}
