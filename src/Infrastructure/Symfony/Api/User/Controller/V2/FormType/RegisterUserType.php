<?php

namespace App\Infrastructure\Symfony\Api\User\Controller\V2\FormType;

use App\Application\User\V2\CommandHandler\RegisterUserCommand;
use App\Domain\Model\User\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Email()
                    ]
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
                        new Assert\Count([
                            'min' => 1,
                            'max' => 5,
                        ]),
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
                'username',
                TextType::class,
                [
                    'constraints' => new NotBlank()
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
                        new Assert\Choice(UserType::getValues())
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RegisterUserCommand::class,
            'csrf_protection' => false,
            'is_edit' => false,
            'extra_fields_message' => 'Extra fields sent: {{ extra_fields }}'
        ]);
    }
}
