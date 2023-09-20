<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\V2\FormType;

use App\Application\User\V2\Command\RegisterUserCommand;
use App\Domain\Model\User\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RegisterUserType
 * @package App\Infrastructure\Symfony\Api\User\V2\FormType
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
                EmailType::class,
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
                'companies',
                CollectionType::class,
                [
                    'entry_type' => TextType::class,
                    'constraints' => [
                        new Assert\Count(
                            [
                                'min' => 1,
                                'max' => 50,
                            ]
                        ),
                    ],
                    'entry_options' => [
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
                    ],
                    'allow_add' => true,
                    'delete_empty' => true,
                ]
            )
            ->add(
                'role',
                TextType::class
            )
            ->add(
                'userType',
                TextType::class,
                [
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Choice(UserType::getValues()),
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
                'data_class' => RegisterUserCommand::class,
                'csrf_protection' => false,
                'is_edit' => false,
                'extra_fields_message' => 'Extra fields sent: {{ extra_fields }}',
            ]
        );
    }
}
