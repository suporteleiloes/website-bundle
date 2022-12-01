<?php


namespace SL\WebsiteBundle\Doctrine;


use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use SL\WebsiteBundle\Entity\ApiSync;

class DeletedFilter extends SQLFilter
{

    public static bool $disableDeletedFilter = false;

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {

        if (self::$disableDeletedFilter) {
            return '';
        }

        if (!$targetEntity->getReflectionClass()->getParentClass()) {
            return '';
        }

        if ($targetEntity->getReflectionClass()->getParentClass()->name !== ApiSync::class && (!$targetEntity->getReflectionClass()->getParentClass()->getParentClass() || $targetEntity->getReflectionClass()->getParentClass()->getParentClass()->name !== FormEntity::class)) {
            return '';
        }

        // @TODO: PARA TESTE, Permitir que carregue registros deletados dentro de JOINS. Ver resultado.
        /*$t = preg_replace('/\D/', '', $targetTableAlias);
        if (!empty($p) && $t != '0') {
            return '';
        }*/

        return sprintf('%s.deleted = %s', $targetTableAlias, $this->getParameter('deleted'));
    }

}