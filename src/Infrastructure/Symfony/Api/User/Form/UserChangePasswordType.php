<?php

namespace App\Infrastructure\Symfony\Api\User\Form;

use App\Application\User\Command\UserChangePasswordCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserChangePasswordType
 * @package App\Infrastructure\Symfony\Api\User\Form
 */
class UserChangePasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                TextType::class,
                [
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Email(),
                    ],
                ]
            )
            ->add(
                'password',
                TextType::class,
                [
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Regex([
                            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])([A-Za-z\d$@$!%*?&]|[^ ])$/',
                            'message' => 'This value must comply with the terms.',
                        ]),
                        new Assert\Length(
                            [
                                'min' => 6,
                                'max' => 20,
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
                'data_class' => UserChangePasswordCommand::class,
                'csrf_protection' => false,
                'is_edit' => false,
                'extra_fields_message' => 'Extra fields sent: {{ extra_fields }}',
            ]
        );
    }
}
