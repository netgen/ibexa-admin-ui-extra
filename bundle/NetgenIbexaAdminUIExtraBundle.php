<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaAdminUIExtraBundle;

use Netgen\Bundle\IbexaAdminUIExtraBundle\DependencyInjection\Compiler\SearchOverridePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NetgenIbexaAdminUIExtraBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new SearchOverridePass());
    }
}
