<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Address;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'First Name',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last Name',
            ])
            ->add('country', TextType::class, [
                'label' => 'Country',
            ])
            ->add('state', TextType::class, [
                'label' => 'State',
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'label' => 'City',
            ])
            ->add('postal', TextType::class, [
                'label' => 'Postal Code',
            ])
            ->add('addressLine1', TextType::class, [
                'label' => 'Address Line 1',
            ])
            ->add('addressLine2', TextType::class, [
                'label' => 'Address Line 2',
                'required' => false,
            ])
            ->add('addressLine3', TextType::class, [
                'label' => 'Address Line 3',
                'required' => false,
            ])
            ->add('additionalInformation', TextareaType::class, [
                'label' => 'Additional Information',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
