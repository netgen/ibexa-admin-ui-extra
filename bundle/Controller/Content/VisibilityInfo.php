<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaAdminUIExtraBundle\Controller\Content;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\BadStateException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class VisibilityInfo extends Controller
{
    public function __construct(
        private readonly LocationService $locationService,
        private readonly TranslatorInterface $translator,
        private readonly ContentService $contentService,
    ) {}

    public function __invoke(int $contentId, Request $request): Response
    {
        $iconPath = $request->attributes->get('iconPath') ?? $request->query->get('iconPath');

        $content = $this->contentService->loadContent($contentId);

        if ($content->getContentInfo()->isHidden() === true) {
            $title = $this->translator->trans('content.visibility.content_hidden', [], 'locationview');
        }

        try {
            $locations = $this->locationService->loadLocations($content->getContentInfo());

            $hiddenLocations = 0;
            $hiddenByAncestor = false;
            foreach ($locations as $location) {
                if ($location->explicitlyHidden) {
                    $hiddenLocations++;
                    continue;
                }

                if ($location->isInvisible() && $location->getParentLocation()?->isInvisible()) {
                    $hiddenLocations++;
                    $hiddenByAncestor = true;
                }
            }

            if ($hiddenLocations !== 0) {
                if (count($locations) === 1) {
                    $extraContent = $this->translator->trans('content.visibility.location_hidden', [], 'locationview');
                } else {
                    $extraContent = $hiddenByAncestor
                        ? $this->translator->trans(
                            'content.visibility.locations_hidden_by_ancestor',
                            [
                                '%hidden%' => $hiddenLocations,
                                '%total%' => count($locations),
                            ],
                            'locationview',
                        )
                        : $this->translator->trans(
                            'content.visibility.locations_hidden',
                            [
                                '%hidden%' => $hiddenLocations,
                                '%total%' => count($locations),
                            ],
                            'locationview',
                        );
                }
            }
        } catch (BadStateException $e) {
            $extraContent = $this->translator->trans('content.visibility.cannot_fetch_locations', [], 'locationview');
        }

        if (!isset($title) && !isset($extraContent)) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return $this->render('@IbexaAdminUi/themes/admin/ui/component/alert/alert.html.twig',
            [
                'type' => 'info',
                'title' => $title ?? '',
                'extra_content' => $extraContent ?? '',
                'icon_path' => $iconPath,
                'class' => 'mt-4',
            ],
        );
    }
}
