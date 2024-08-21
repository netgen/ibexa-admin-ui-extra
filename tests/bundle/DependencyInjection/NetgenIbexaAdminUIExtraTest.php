<?php

declare(strict_types=1);

namespace Netgen\IbexaAdminUIExtra\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Netgen\Bundle\IbexaAdminUIExtraBundle\DependencyInjection\NetgenIbexaAdminUIExtraExtension;

final class NetgenIbexaAdminUIExtraTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setParameter('kernel.bundles', []);
    }

    public static function provideDefaultConfigurationCases(): iterable
    {
        return [
            [
                [],
            ],
            [
                [
                    'show_external_siteaccess_urls' => false,
                ],
            ],
        ];
    }

    public static function provideShowExternalSiteaccessUrlsConfigurationCases(): iterable
    {
        return [
            [
                [
                    'show_external_siteaccess_urls' => false,
                ],
                false,
            ],
            [
                [
                    'show_external_siteaccess_urls' => true,
                ],
                true,
            ],
        ];
    }

    /**
     * @dataProvider provideDefaultConfigurationCases
     */
    public function testDefaultConfiguration(array $configuration): void
    {
        $this->load($configuration);

        $this->assertContainerBuilderHasParameter(
            'netgen_ibexa_admin_ui_extra.show_external_siteaccess_urls',
            false,
        );
    }

    /**
     * @dataProvider provideShowExternalSiteaccessUrlsConfigurationCases
     */
    public function testShowExternalSiteaccessUrlsConfiguration(array $configuration, bool $expectedParameterValue): void
    {
        $this->load($configuration);

        $this->assertContainerBuilderHasParameter(
            'netgen_ibexa_admin_ui_extra.show_external_siteaccess_urls',
            $expectedParameterValue,
        );
    }

    protected function getContainerExtensions(): array
    {
        return [
            new NetgenIbexaAdminUIExtraExtension(),
        ];
    }
}
