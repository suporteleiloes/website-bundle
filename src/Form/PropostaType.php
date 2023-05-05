<?php

namespace SL\WebsiteBundle\Form;

use SL\WebsiteBundle\Entity\Proposta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
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
            ->add('valor', MoneyType::class, [
                'invalid_message' => 'Valor dos débitos inválido. Formato: 0.00',
                'currency' => ''
            ])
            ->add('parcelado', ChoiceType::class, [
                'choices'  => [
                    'À vista' => false,
                    'Parcelado' => true,
                ],
            ])
            ->add('valorEntrada', MoneyType::class, [
                'invalid_message' => 'Valor dos débitos inválido. Formato: 0.00',
                'currency' => ''
            ])
            ->add('quantidadeParcelas')
            ->add('indiceCorrecao')
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
