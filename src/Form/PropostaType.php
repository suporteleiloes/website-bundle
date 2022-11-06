<?php

namespace SL\WebsiteBundle\Form;

use SL\WebsiteBundle\Entity\Proposta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropostaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nome')
            ->add('email')
            ->add('telefone')
            ->add('assunto')
            ->add('mensagem')
            ->add('bemId')
            ->add('loteId')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Proposta::class,
        ]);
    }
}
