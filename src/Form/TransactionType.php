<?php

namespace App\Form;

use App\Entity\Transaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ClientEmetteur')
            ->add('TelephoneEmetteur')
            ->add('NciEmetteur')
            ->add('Code')
            ->add('Montant')
            ->add('Frais')
            ->add('ClientRecepteur')
            ->add('TelephoneRecepteur')
            ->add('NciRecepteur')
            ->add('DateReception')
            ->add('CommissionEmetteur')
            ->add('CommissionRecepteur')
            ->add('CommissionWari')
            ->add('TaxesEtat')
            ->add('UserEmetteur')
            ->add('UserRecepteur')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
