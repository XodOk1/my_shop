<?php
// src/Form/MyFormType.php
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