<?php

namespace App\Form;

use App\Entity\Transaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RetraitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           /*  ->add('ClientEmetteur')
            ->add('TelephoneEmetteur')
            ->add('NciEmetteur')
            ->add('DateEnvoi') */
            ->add('Code')
            ->add('NciRecepteur')
            /* ->add('Montant')
            ->add('Frais')
            ->add('ClientRecepteur')
            ->add('TelephoneRecepteur')            
            ->add('DateReception')
            ->add('CommissionEmetteur')
            ->add('CommissionRecepteur')
            ->add('CommissionWari')
            ->add('TaxesEtat')
            ->add('etat')
            ->add('UserEmetteur')
            ->add('UserRecepteur') */
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
