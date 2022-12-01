<?php

namespace SL\WebsiteBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class BeforeRequestListener
{

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $filter = $this->em
            ->getFilters()
            ->enable('deleted_filter');
        $filter->setParameter('deleted', false);
    }

}