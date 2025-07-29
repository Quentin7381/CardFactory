<?php

namespace App\Form;

use App\Entity\Card;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Template;

class CardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $fields = ['name', 'card_title', 'card_subtitle', 'card_image'];

        foreach ($fields as $field) {
            $builder->add($field, null, [
                'attr' => [
                    'class' => 'form-control ' . $field,
                ],
                'required' => $field == 'name' ? true : false,
            ]);
        }

        // Make the card_body field a textarea
        $builder->add('card_body', TextareaType::class, [
            'required' => false,
            'attr' => [
                'class' => 'form-control card_body',
                'placeholder' => 'Enter the card description here...',
            ],
        ]);

        // Make the card_image field a file upload
        $builder->add('card_image', FileType::class, [
            'label' => 'Upload Image',
            'mapped' => false,
            'required' => false,
            'attr' => [
                'class' => 'form-control card_image',
                'data-image' => $options['image_url'] ?? '',
            ],
            'label_attr' => [
                'class' => 'card_image',
            ],
        ]);

        // Add a select field for Template entity
        $builder->add('template', EntityType::class, [
            'class' => Template::class,
            'choice_label' => 'name',
            'placeholder' => 'Select a Template',
            'required' => true,
            'attr' => [
                'class' => 'form-control template-select',
            ],
            'label' => false,
            'choice_attr' => function (Template $template) {
                return ['data-css-class' => $template->getCssClass()];
            },
        ]);

        // Store old template for data change check
        $card = $builder->getData();
        $template = $card ? $card->getTemplate() : null;

        if ($template) {
            $builder->add('old_template', null, [
                'data' => $template->getId(),
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control old-template',
                    'style' => 'display:none;',
                ],
                'label' => false,
                'disabled' => true,
                'row_attr' => [
                    'style' => 'display:none;', // Hide the entire container
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Card::class,
            'image_url' => null,

        ]);
    }
}
