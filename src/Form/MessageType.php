<?php

namespace App\Form;

// use App\Entity\MessageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
// use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', NumberType::class, [
                'label' => 'ID',

            ])
            ->add('numberOrder', TextareaType::class, [
                'label' => 'Номер заказа',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                ],
            ])

            ->add('address', TextType::class, [
                'label' => 'Адрес',
                'attr' => ['class' => 'save'],
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MessageType::class,
            'csrf_token_id' => 'trip_order_create',
        ]);
    }
}
