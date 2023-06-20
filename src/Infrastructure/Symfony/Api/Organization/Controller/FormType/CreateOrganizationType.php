<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Organization\Controller\FormType;

use App\Application\Organization\CommandHandler\CreateOrganizationCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CreateOrganizationType
 * @package App\Infrastructure\Symfony\Api\Organization\Controller\FormType
 */
class CreateOrganizationType extends AbstractType
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
                'ownerName',
                TextType::class,
                [
                    'constraints' => new Assert\NotBlank(),
                ]
            )->add(
                'ownerEmail',
                TextType::class,
                [
                    'constraints' => new Assert\Email(),
                ]
            )->add(
                'ownerPhoneNumber',
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
                'data_class' => CreateOrganizationCommand::class,
                'csrf_protection' => false,
                'is_edit' => false,
                'extra_fields_message' => 'Extra fields sent: {{ extra_fields }}',
            ]
        );
    }
}
