<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaAdminUIExtraBundle\Controller\Content;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\Exceptions\BadStateException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class VisibilityInfo extends Controller
{
    public function __construct(
        private readonly LocationService $locationService,
        private readonly TranslatorInterface $translator,
    ) {}

    public function __invoke(Content $content, String $iconPath): Response
    {
        if ($content->getContentInfo()->isHidden() === true) {
            $title = $this->translator->trans('content.visibility.content_hidden', [], 'locationview');
        } else {
            try {
                $locations = $this->locationService->loadLocations($content->getContentInfo());

                $hiddenLocations = 0;
                foreach ($locations as $location) {
                    if ($location->explicitlyHidden || $location->invisible) {
                        $hiddenLocations++;
                    }
                }

                if ($hiddenLocations !== 0) {
                    if (count($locations) === 1) {
                        $title = $this->translator->trans('content.visibility.location_hidden', [], 'locationview');
                    } else {
                        $title = $this->translator->trans(
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
                $title = $this->translator->trans('content.visibility.cannot_fetch_locations', [], 'locationview');
            }
        }

        if (!isset($title)) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return $this->render('@IbexaAdminUi/themes/admin/ui/component/alert/alert.html.twig',
            [
                'type' => 'info',
                'title' => $title,
                'icon_path' => $iconPath,
                'class' => 'mt-4',
            ],
        );
    }
}
