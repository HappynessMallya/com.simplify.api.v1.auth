<?php

namespace App\Infrastructure\Symfony\Api\Company\V1\FormType;

use App\Application\Company\Command\UpdateCompanyCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UpdateCompanyType
 * @package App\Infrastructure\Symfony\Api\Company\V1\FormType
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
                'organizationId',
                TextType::class,
                [
                    'constraints' => [
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
                'name',
                TextType::class,
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'constraints' => new Assert\Email(),
                ]
            )
            ->add(
                'phone',
                TextType::class
            )
            ->add(
                'address',
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
                'data_class' => UpdateCompanyCommand::class,
                'csrf_protection' => false,
                'is_edit' => false,
                'extra_fields_message' => 'Extra fields sent: {{ extra_fields }}',
            ]
        );
    }
}
