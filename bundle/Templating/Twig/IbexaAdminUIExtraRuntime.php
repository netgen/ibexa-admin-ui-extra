<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaAdminUIExtraBundle\Templating\Twig;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Twig\Extension\RuntimeExtensionInterface;

final class IbexaAdminUIExtraRuntime implements RuntimeExtensionInterface
{
    private ContentService $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    public function countContentByContentType(string $contentTypeIdentifier): int
    {
        $filter = new Filter();
        $filter->withCriterion(new Criterion\ContentTypeIdentifier($contentTypeIdentifier));
        $filter->withLimit(1);

        return $this->contentService->find($filter)->getTotalCount() ?? 0;
    }
}
