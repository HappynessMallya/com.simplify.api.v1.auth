<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\V1\FormType;

use App\Application\Company\V1\Command\CreateCompanyCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CreateCompanyType
 * @package App\Infrastructure\Symfony\Api\Company\Form
 */
class CreateCompanyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'constraints' => new Assert\NotBlank(),
                ]
            )->add(
                'tin',
                TextType::class,
                [
                    'constraints' => new Assert\NotBlank(),
                ]
            )->add(
                'email',
                TextType::class,
                [
                    'constraints' => new Assert\Email(),
                ]
            )->add(
                'phone',
                TextType::class
            )->add(
                'address',
                TextType::class
            )->add(
                'serial',
                TextType::class,
                [
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Length(
                            [
                                'min' => 10,
                                'max' => 10,
                                'minMessage' => 'Must be at least {{ limit }} characters long',
                                'maxMessage' => 'Cannot be longer than {{ limit }} characters',
                            ]
                        ),
                    ],
                ]
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => CreateCompanyCommand::class,
                'csrf_protection' => false,
                'is_edit' => false,
                'extra_fields_message' => 'Extra fields sent: {{ extra_fields }}',
            ]
        );
    }
}
