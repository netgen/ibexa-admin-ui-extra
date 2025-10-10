<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaAdminUIExtraBundle\Controller\Content;

use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\Exceptions\BadStateException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Symfony\Component\HttpFoundation\Response;

class VisibilityInfo extends Controller
{
    public function __construct(
        private readonly LocationService $locationService,
    ) {}

    public function __invoke(Content $content): Response
    {
        $info = 'Content is HIDDEN';
        $messageType = 'error';
        if ($content->getContentInfo()->isHidden() === false) {
            try {
                $locations = $this->locationService->loadLocations($content->getContentInfo());

                $hiddenLocations = 0;
                foreach ($locations as $location) {
                    if ($location->explicitlyHidden || $location->invisible) {
                        $hiddenLocations++;
                    }
                }

                if (count($locations) === 1) {
                    if ($hiddenLocations === 0) {
                        $info = 'Location: VISIBLE';
                        $messageType = 'success';
                    } else {
                        $info = 'Location: HIDDEN';
                    }
                } else if ($hiddenLocations === 0) {
                    $info = 'Locations: ALL VISIBLE';
                    $messageType = 'success';
                } else {
                    // some or all locations are hidden
                    $info = sprintf(
                        'Locations: %s out of %s HIDDEN',
                        $hiddenLocations,
                        count($locations),
                    );
                    if (count($locations) !== $hiddenLocations) {
                        $messageType = 'warning';
                    }
                }
            } catch (BadStateException $e) {
                $info = "Can't fetch locations for this content!";
            }
        }

        return $this->render(
            '@NetgenIbexaAdminUIExtra/themes/ngadmin/ui/visibility_info.html.twig',
            [
                'info' => $info,
                'type' => $messageType,
            ],
        );
    }
}
