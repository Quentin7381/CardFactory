<?php

namespace App\Form;

use App\Entity\Card;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $fields = ['name', 'card_title', 'card_subtitle', 'card_image'];

        foreach ($fields as $field) {
            $builder->add($field, null, [
                'attr' => [
                    'class' => 'form-control ' . $field, // Add a class with the field name
                ],
            ]);
        }

        // Make the card_body field a textarea
        $builder->add('card_body', TextareaType::class, [
            'attr' => [
                'class' => 'form-control card_body', // Add a class for styling
                'placeholder' => 'Enter the card body here...', // Optional: Add a placeholder
            ],
        ]);

        // Make the card_image field a file upload
        $builder->add('card_image', FileType::class, [
            'label' => 'Upload Image',
            'mapped' => false, // This tells Symfony not to map this field directly to the entity
            'required' => false,
            'attr' => [
                'class' => 'form-control card_image', // Add a class for styling
            ],
            'label_attr' => [
                'class' => 'card_image', // Add a class for the label
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Card::class,
        ]);
    }
}
