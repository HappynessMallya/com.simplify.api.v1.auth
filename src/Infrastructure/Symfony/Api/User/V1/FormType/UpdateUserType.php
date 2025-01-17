<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\V1\FormType;

use App\Application\User\V1\Command\UpdateUserCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UpdateUserType
 * @package App\Infrastructure\Symfony\Api\User\V1\FormType
 */
class UpdateUserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'companies',
                CollectionType::class,
                [
                    'entry_type' => TextType::class,
                    'constraints' => [
                        new Assert\Count(
                            [
                                'min' => 0,
                                'max' => 50,
                            ]
                        ),
                    ],
                    'entry_options' => [
                        'constraints' => [
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
                'firstName',
                TextType::class,
                [
                    'constraints' => [
                        new Assert\Length(
                            [
                                'min' => 2,
                                'max' => 100,
                                'minMessage' => 'Must be at least {{ limit }} characters long',
                                'maxMessage' => 'Cannot be longer than {{ limit }} characters',
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'lastName',
                TextType::class,
                [
                    'constraints' => [
                        new Assert\Length(
                            [
                                'min' => 2,
                                'max' => 100,
                                'minMessage' => 'Must be at least {{ limit }} characters long',
                                'maxMessage' => 'Cannot be longer than {{ limit }} characters',
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'email',
                TextType::class,
                [
                    'constraints' => new Assert\Email(),
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
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => UpdateUserCommand::class,
                'csrf_protection' => false,
                'is_edit' => false,
                'extra_fields_message' => 'Extra fields sent: {{ extra_fields }}',
            ]
        );
    }
}
