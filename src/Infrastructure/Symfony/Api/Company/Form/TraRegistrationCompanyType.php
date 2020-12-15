<?php

namespace App\Infrastructure\Symfony\Api\Company\Form;

use App\Application\Company\Command\CompanyTraRegistrationCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class TraRegistrationCompanyType
 * @package App\Infrastructure\Symfony\Api\Company\Form
 */
final class TraRegistrationCompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('traRegistration', TextType::class, ['constraints' => new NotBlank()]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CompanyTraRegistrationCommand::class,
            'csrf_protection' => false,
            'is_edit' => false,
            'extra_fields_message' => 'Extra fields sent: {{ extra_fields }}'
        ]);
    }
}
