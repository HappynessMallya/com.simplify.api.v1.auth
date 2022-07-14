<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Api\User\Form;

use App\Application\User\Command\VerifyUserExistsCommand;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class VerifyUserExistsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                EmailType::class,
                [
                    'constraints' => new NotBlank()
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => VerifyUserExistsCommand::class,
            'csrf_protection' => false,
            'is_edit' => false,
            'extra_fields_message' => 'Extra fields sent: {{ extra_fields }}'
        ]);
    }
}
