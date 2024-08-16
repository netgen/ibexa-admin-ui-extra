<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaAdminUIExtraBundle\Tab\LocationView;

use Ibexa\AdminUi\Tab\LocationView\UrlsTab as IbexaUrlsTab;
use Ibexa\Contracts\AdminUi\Tab\AbstractEventDispatchingTab;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

use function array_search;
use function preg_match;

final class UrlsTab extends AbstractEventDispatchingTab implements OrderedTabInterface
{
    private const SYSTEM_URL_REGEX_PATTERN = '#/view/content/\d+/full/\d+/\d+$#';

    public function __construct(
        private readonly IbexaUrlsTab $inner,
        private readonly RouterInterface $router,
        private readonly ConfigResolverInterface $configResolver,
        private readonly array $siteaccessList,
        Environment $twig,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);
    }

    public function getIdentifier(): string
    {
        return $this->inner->getIdentifier();
    }

    public function getName(): string
    {
        return $this->inner->getName();
    }

    public function getOrder(): int
    {
        return $this->inner->getOrder();
    }

    public function getTemplate(): string
    {
        return $this->inner->getTemplate();
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        $contentTreeUrls = [];
        $externalUrls = [];

        /** @var Location $location */
        $location = $contextParameters['location'];

        $locationPath = $location->path;
        foreach ($this->siteaccessList as $siteaccess) {
            $rootLocationId = $this->configResolver->getParameter(
                'content.tree_root.location_id',
                null,
                $siteaccess,
            );

            $url = $this->router->generate(
                'ibexa.url.alias',
                [
                    'locationId' => $location->id,
                    'siteaccess' => $siteaccess,
                ],
                UrlGeneratorInterface::ABSOLUTE_URL,
            );

            // checks if the url matches invalid system url format
            if (preg_match(self::SYSTEM_URL_REGEX_PATTERN, $url)) {
                continue;
            }

            $locationIdIndex = array_search((string) $location->id, $locationPath, true);
            $rootLocationIdIndex = array_search((string) $rootLocationId, $locationPath, true);

            // checks if the url is inside configured siteaccess content tree
            if ($locationIdIndex !== false && $rootLocationIdIndex !== false && $rootLocationIdIndex <= $locationIdIndex) {
                $contentTreeUrls[$siteaccess] = $url;
            } else {
                $externalUrls[$siteaccess] = $url;
            }
        }

        $parameters = [
            'content_tree_urls' => $contentTreeUrls,
            'external_urls' => $externalUrls,
        ];

        $parentParameters = $this->inner->getTemplateParameters($contextParameters);

        return $parentParameters + $parameters;
    }
}
