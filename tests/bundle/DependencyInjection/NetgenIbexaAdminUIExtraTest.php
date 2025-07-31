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
                    'show_siteaccess_urls_outside_configured_content_tree_root' => false,
                    'queues' => [
                        'disabled' => false,
                        'transports' => [],
                    ],
                ],
            ],
        ];
    }

    public static function provideShowExternalSiteaccessUrlsConfigurationCases(): iterable
    {
        return [
            [
                [
                    'show_siteaccess_urls_outside_configured_content_tree_root' => false,
                ],
                false,
            ],
            [
                [
                    'show_siteaccess_urls_outside_configured_content_tree_root' => true,
                ],
                true,
            ],
        ];
    }

    public static function provideQueuesConfigurationCases(): iterable
    {
        return [
            [
                [
                    'queues' => [],
                ],
                [
                    'disabled' => false,
                    'transports' => [],
                ],
            ],
            [
                [
                    'queues' => [
                        'disabled' => false,
                        'transports' => [],
                    ],
                ],
                [
                    'disabled' => false,
                    'transports' => [],
                ],
            ],
            [
                [
                    'queues' => [
                        'disabled' => true,
                        'transports' => [
                            'transport1',
                            'transport2',
                        ],
                    ],
                ],
                [
                    'disabled' => true,
                    'transports' => [
                        'transport1',
                        'transport2',
                    ],
                ],
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
            'netgen_ibexa_admin_ui_extra.show_siteaccess_urls_outside_configured_content_tree_root',
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
            'netgen_ibexa_admin_ui_extra.show_siteaccess_urls_outside_configured_content_tree_root',
            $expectedParameterValue,
        );
    }

    /**
     * @dataProvider provideQueuesConfigurationCases
     */
    public function testQueuesConfiguration(array $configuration, array $expectedParameterValues): void
    {
        $this->load($configuration);

        $this->assertContainerBuilderHasParameter(
            'netgen_ibexa_admin_ui_extra.queues.disabled',
            $expectedParameterValues['disabled'],
        );
        $this->assertContainerBuilderHasParameter(
            'netgen_ibexa_admin_ui_extra.queues.transports',
            $expectedParameterValues['transports'],
        );
    }

    protected function getContainerExtensions(): array
    {
        return [
            new NetgenIbexaAdminUIExtraExtension(),
        ];
    }
}
