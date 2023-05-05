<?php

namespace SL\WebsiteBundle\Form;

use SL\WebsiteBundle\Entity\Proposta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
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
                'invalid_message' => 'Valor inválido. Formato: 0.00',
                'currency' => ''
            ])
            ->add('parcelado', ChoiceType::class, [
                'choices'  => [
                    'À vista' => false,
                    'Parcelado' => true,
                ],
            ])
            ->add('valorEntrada', MoneyType::class, [
                'invalid_message' => 'Valor de entrada inválido. Formato: 0.00',
                'currency' => ''
            ])
            ->add('quantidadeParcelas')
            ->add('indiceCorrecao', ChoiceType::class, [
                'choices'  => [
                    'Qualquer' => null,
                    'IPCA' => 'IPCA',
                    'IGP-M' => 'IGP-M',
                    'IPA' => 'IPA',
                    'IPC' => 'IPC',
                    'INCC' => 'INCC',
                    'Taxa Selic' => 'Taxa Selic',
                ],
            ])
            ->add('mensagem')
            ->add('bemId')
            ->add('loteId')
        ;

        $builder->get('valor')->addModelTransformer(new MoneyTransform());
        $builder->get('valorEntrada')->addModelTransformer(new MoneyTransform());

        /*$builder->get('valor')
            ->addModelTransformer(new CallbackTransformer(
                function ($money) {
                    return $money;
                },
                function ($string) {
                    if (strpos($string, 'R$') !== false) {
                        $string = str_replace('.', '', $string); // remove o ponto
                        $string = str_replace(',', '.', $string); // substitui a vírgula por ponto
                        return floatval($string);
                    }

                    return $string;
                }
            ))
        ;
        $builder->get('valorEntrada')
            ->addModelTransformer(new CallbackTransformer(
                function ($money) {
                    return $money;
                },
                function ($string) {
                    if (strpos($string, 'R$') !== false) {
                        $string = str_replace('.', '', $string); // remove o ponto
                        $string = str_replace(',', '.', $string); // substitui a vírgula por ponto
                        return floatval($string);
                    }

                    return $string;
                }
            ))
        ;*/
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Proposta::class,
            'allow_extra_fields' => true
        ]);
    }
}
