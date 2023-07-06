<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\V1\FormType;

use App\Application\User\Command\RegisterUserCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RegisterUserType
 * @package App\Infrastructure\Symfony\Api\User\V1\FormType
 */
class RegisterUserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'companyId',
                TextType::class,
                [
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Length(
                            [
                                'min' => 36,
                                'max' => 36,
                                'maxMessage' => 'Cannot be longer than {{ limit }} characters.',
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'firstName',
                TextType::class,
                [
                    'constraints' => new Assert\NotBlank(),
                ]
            )
            ->add(
                'lastName',
                TextType::class,
                [
                    'constraints' => new Assert\NotBlank(),
                ]
            )
            ->add(
                'username',
                TextType::class,
                [
                    'constraints' => new Assert\NotBlank(),
                ]
            )
            ->add(
                'email',
                TextType::class,
                [
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Email(),
                    ],
                ]
            )
            ->add(
                'mobileNumber',
                TextType::class,
                [
                    'constraints' => [
                        new Assert\Regex(
                            [
                                'pattern' => '/^[0-9]*$/',
                                'message' => 'This value should be numeric.',
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'password',
                TextType::class
            )
            ->add(
                'role',
                TextType::class
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
                'data_class' => RegisterUserCommand::class,
                'csrf_protection' => false,
                'is_edit' => false,
                'extra_fields_message' => 'Extra fields sent: {{ extra_fields }}',
            ]
        );
    }
}
