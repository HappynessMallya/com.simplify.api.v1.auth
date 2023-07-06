<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Organization\V1\FormType;

use App\Application\Organization\CommandHandler\ChangeOrganizationStatusCommand;
use App\Domain\Model\Organization\OrganizationStatus;
use App\Domain\Model\User\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ChangeOrganizationStatusType
 * @package App\Infrastructure\Symfony\Api\Organization\Controller\FormType
 */
class ChangeOrganizationStatusType extends AbstractType
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
                'status',
                TextType::class,
                [
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Choice(OrganizationStatus::getValues()),
                    ],
                ]
            )
            ->add(
                'userType',
                TextType::class,
                [
                    'constraints' => [
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
                'data_class' => ChangeOrganizationStatusCommand::class,
                'csrf_protection' => false,
                'is_edit' => false,
                'extra_fields_message' => 'Extra fields sent: {{ extra_fields }}',
            ]
        );
    }
}
