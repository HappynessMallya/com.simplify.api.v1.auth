<?php

namespace App\Infrastructure\Symfony\Api\Company\Controller\V2\FormType;

use App\Application\Company\V2\CommandHandler\UpdateCompanyCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class UpdateCompanyType
 * @package App\Infrastructure\Symfony\Api\Company\Form
 */
class UpdateCompanyType extends AbstractType
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
                    'constraints' => [
                        new NotBlank(),
                        new Length(
                            [
                                'min' => 1,
                                'max' => 200,
                                'minMessage' => 'Must be at least {{ limit }} characters long.',
                                'maxMessage' => 'Cannot be longer than {{ limit }} characters.',
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Email(),
                    ],
                ]
            )
            ->add(
                'phone',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Regex(
                            [
                                'pattern' => '/^[0-9]*$/',
                                'message' => 'This value should be numeric.',
                            ]
                        ),
                        new Length(
                            [
                                'min' => 9,
                                'max' => 15,
                                'minMessage' => 'Must be at least {{ limit }} characters long.',
                                'maxMessage' => 'Cannot be longer than {{ limit }} characters.',
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'address',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Length(
                            [
                                'min' => 5,
                                'max' => 100,
                                'minMessage' => 'Must be at least {{ limit }} characters long',
                                'maxMessage' => 'Cannot be longer than {{ limit }} characters',
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'organizationId',
                TextType::class,
                [
                    'constraints' => [
                        new Length(
                            [
                                'min' => 36,
                                'max' => 36,
                                'minMessage' => 'Must be at least {{ limit }} characters long',
                                'maxMessage' => 'Cannot be longer than {{ limit }} characters',
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'companyId',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Length(
                            [
                                'min' => 36,
                                'max' => 36,
                                'minMessage' => 'Must be at least {{ limit }} characters long',
                                'maxMessage' => 'Cannot be longer than {{ limit }} characters',
                            ]
                        ),
                    ],
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => UpdateCompanyCommand::class,
                'csrf_protection' => false,
                'is_edit' => false,
                'extra_fields_message' => 'Extra fields sent: {{ extra_fields }}',
            ]
        );
    }
}
