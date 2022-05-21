<?php

namespace SL\WebsiteBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SLWebsiteBundle extends Bundle
{

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
