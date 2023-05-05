<?php

namespace SL\WebsiteBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;

class MoneyTransform implements DataTransformerInterface
{
    public function transform($value)
    {
        // Remove o símbolo "R$" e transforma a string em um número de ponto flutuante
        if (strpos($value, 'R$') !== false) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', substr($value, 3));
            return floatval($value);
        }
        return $value;
    }

    public function reverseTransform($value)
    {
        // Remove o símbolo "R$" e transforma a string em um número de ponto flutuante
        if (strpos($value, 'R$') !== false) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', substr($value, 3));
            return floatval($value);
        }
        return $value;
    }
}