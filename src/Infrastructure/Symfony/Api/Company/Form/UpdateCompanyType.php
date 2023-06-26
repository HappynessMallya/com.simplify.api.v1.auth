<?php

namespace App\Infrastructure\Symfony\Api\Company\Form;

use App\Application\Company\Command\UpdateCompanyCommand;
use App\Entity\Enums\CompanyStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                        new NotBlank(),
                    ]
                ]
            )
            ->add(
                'name',
                TextType::class,
            )
             ->add('email', EmailType::class, ['constraints' => new Email()])
            ->add(
                'phone',
                TextType::class
            )
            ->add(
                'address',
                TextType::class
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UpdateCompanyCommand::class,
            'csrf_protection' => false,
            'is_edit' => false,
            'extra_fields_message' => 'Extra fields sent: {{ extra_fields }}'
        ]);
    }
}
