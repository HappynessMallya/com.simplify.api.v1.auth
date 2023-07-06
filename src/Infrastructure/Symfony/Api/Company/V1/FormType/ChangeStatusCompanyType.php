<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\V1\FormType;

use App\Application\Company\Command\ChangeStatusCompanyCommand;
use App\Domain\Model\Company\CompanyStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ChangeStatusCompanyType
 * @package App\Infrastructure\Symfony\Api\Company\V1\FormType
 */
class ChangeStatusCompanyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'status',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Choice(CompanyStatus::getValues()),
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
                'data_class' => ChangeStatusCompanyCommand::class,
                'csrf_protection' => false,
                'is_edit' => false,
                'extra_fields_message' => 'Extra fields sent: {{ extra_fields }}',
            ]
        );
    }
}
