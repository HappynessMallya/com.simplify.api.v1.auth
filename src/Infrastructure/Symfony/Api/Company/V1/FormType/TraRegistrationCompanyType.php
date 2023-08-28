<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\Company\V1\FormType;

use App\Application\Company\V1\Command\CompanyTraRegistrationCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class TraRegistrationCompanyType
 * @package App\Infrastructure\Symfony\Api\Company\V1\FormType
 */
class TraRegistrationCompanyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'traRegistration',
                TextType::class,
                [
                    'constraints' => new NotBlank(),
                ]
            )
            ->add(
                'serial',
                TextType::class,
                [
                    'constraints' => new NotBlank(),
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
                'data_class' => CompanyTraRegistrationCommand::class,
                'csrf_protection' => false,
                'is_edit' => false,
                'extra_fields_message' => 'Extra fields sent: {{ extra_fields }}',
            ]
        );
    }
}
