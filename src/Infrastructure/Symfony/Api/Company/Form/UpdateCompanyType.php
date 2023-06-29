<?php

namespace App\Infrastructure\Symfony\Api\Company\Form;

use App\Application\Company\Command\UpdateCompanyCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
<<<<<<< HEAD
use Symfony\Component\Validator\Constraints as Assert;
=======
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
>>>>>>> a0ad8974b085c54779022f85960193829fe75996

/**
 * Class UpdateCompanyType
 * @package App\Infrastructure\Symfony\Api\Company\Form
 */
final class UpdateCompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'organizationId',
                TextType::class,
                [
                    'constraints' => [
<<<<<<< HEAD
                        new Assert\Length(
                            [
                                'min' => 36,
                                'max' => 36,
                                'maxMessage' => 'Cannot be longer than {{ limit }} characters.',
                            ]
                        ),
                    ],
=======
                        new NotBlank(),
                    ]
>>>>>>> a0ad8974b085c54779022f85960193829fe75996
                ]
            )
            ->add(
                'name',
                TextType::class,
            )
<<<<<<< HEAD
            ->add('email', EmailType::class, ['constraints' => new Assert\Email()])
=======
             ->add('email', EmailType::class, ['constraints' => new Email()])
>>>>>>> a0ad8974b085c54779022f85960193829fe75996
            ->add(
                'phone',
                TextType::class
            )
            ->add(
                'address',
                TextType::class
<<<<<<< HEAD
            )
        ;
=======
            );
>>>>>>> a0ad8974b085c54779022f85960193829fe75996
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
