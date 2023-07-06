<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\V1\FormType;

use App\Application\Company\Command\ChangeEnableCompanyCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Class ChangeEnableCompanyType
 * @package App\Infrastructure\Symfony\Api\Company\V1\FormType
 */
class ChangeEnableCompanyType extends AbstractType
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
                        new NotBlank(),
                        new Length(
                            [
                                'min' => 36,
                                'max' => 36,
                                'minMessage' => 'Must be at least {{ limit }} characters long',
                                'maxMessage' => 'Cannot be longer than {{ limit }} characters',
                            ]
                        )
                    ]
                ]
            )
            ->add(
                'enable',
                CheckboxType::class,
                [
                    'constraints' => [
                        new NotNull(),
                    ]
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
                'data_class' => ChangeEnableCompanyCommand::class,
                'csrf_protection' => false,
                'is_edit' => false,
                'extra_fields_message' => 'Extra fields sent: {{ extra_fields }}',
            ]
        );
    }
}
