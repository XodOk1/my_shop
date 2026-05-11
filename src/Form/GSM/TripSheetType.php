<?php

namespace App\Form\GSM;

use App\Entity\GSM\TripSheet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Button;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
// use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripSheetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Название путевого листа',
                'attr' => [
                    'placeholder' => 'Например: Иванов Иван / 12.03.2025',
                ],
            ])
            ->add('route', TextareaType::class, [
                'label' => 'Маршрут',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'Например: Склад → ТЦ Вега → Офис',
                ],
            ])
            ->add('addRoute', ButtonType::class, [
                'label' => 'Добавить маршрут',
                'attr' => ['class' => 'addRoute'],
            ])

            ->add('save', SubmitType::class, [
                'label' => 'Сохранить',
                'attr' => ['class' => 'save'],
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TripSheet::class,
            'csrf_token_id' => 'trip_sheet_create',
        ]);
    }
}
