<?php
// src/Form/MyFormType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class MyForm
{
public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        ->add('username', TextType::class, [
            'attr' => ['class' => 'form-control'],
            'label_attr' => ['class' => 'form-label']
        ])
        ->add('email', EmailType::class, [
            'attr' => ['class' => 'form-control'],
            'label_attr' => ['class' => 'form-label']
        ])
        ->add('agreeTerms', CheckboxType::class, [
            'attr' => ['class' => 'form-check-input'],
            'label_attr' => ['class' => 'form-check-label']
        ]);
}
}
