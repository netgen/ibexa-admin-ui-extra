<?php

declare(strict_types=1);

namespace Netgen\Bundle\BetterIbexaAdminUIBundle\Templating\Twig;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Twig\Extension\RuntimeExtensionInterface;

final class BetterIbexaAdminUIRuntime implements RuntimeExtensionInterface
{
    private ContentService $filterService;

    public function __construct(ContentService $filterService)
    {
        $this->filterService = $filterService;
    }

    public function countContentByContentType(string $contentTypeIdentifier): int
    {
        $query = new Filter();
        $query->withCriterion(new Criterion\ContentTypeIdentifier($contentTypeIdentifier));
        $query->withLimit(0);

        return $this->filterService->find($query)->getTotalCount() ?? 0;
    }
}
