<?php

namespace Keboola\StagingProvider\Tests;

use Keboola\OutputMapping\Exception\InvalidOutputException;
use Keboola\OutputMapping\Staging\StrategyFactory as OutputStrategyFactory;
use Keboola\OutputMapping\Writer\File\Strategy\ABSWorkspace;
use Keboola\OutputMapping\Writer\File\Strategy\Local as OutputFileLocal;
use Keboola\OutputMapping\Writer\Table\Strategy\AllEncompassingTableStrategy;
use Keboola\StorageApi\Client;
use Keboola\StorageApi\ClientException;
use Keboola\StorageApi\Components;
use Keboola\StorageApi\Options\Components\Configuration;
use Keboola\StorageApi\Workspaces;
use Keboola\StorageApiBranch\ClientWrapper;
use Keboola\StagingProvider\OutputProviderInitializer;
use Keboola\StagingProvider\WorkspaceProviderFactory\ComponentWorkspaceProviderFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class OutputProviderInitializerTest extends TestCase
{
    public function testInitializeOutputLocal()
    {
        $storageApiClient = new Client(['token' => 'foo', 'url' => 'bar']);
        $stagingFactory = new OutputStrategyFactory(
            new ClientWrapper(
                $storageApiClient,
                null,
                new NullLogger(),
                ''
            ),
            new NullLogger(),
            'json'
        );

        $providerFactory = new ComponentWorkspaceProviderFactory(
            new Components($storageApiClient),
            new Workspaces($storageApiClient),
            'my-test-component',
            'my-test-config'
        );
        $init = new OutputProviderInitializer($stagingFactory, $providerFactory);

        $init->initializeProviders(
            OutputStrategyFactory::LOCAL,
            [],
            '/tmp/random/data'
        );
        self::assertInstanceOf(OutputFileLocal::class, $stagingFactory->getFileOutputStrategy(OutputStrategyFactory::LOCAL));
        self::assertInstanceOf(AllEncompassingTableStrategy::class, $stagingFactory->getTableOutputStrategy(OutputStrategyFactory::LOCAL));

        $this->expectExceptionMessage('The project does not support "workspace-redshift" table output backend.');
        $this->expectException(InvalidOutputException::class);
        $stagingFactory->getTableOutputStrategy(OutputStrategyFactory::WORKSPACE_REDSHIFT);
    }

    public function testInitializeOutputRedshift()
    {
        $storageApiClient = new Client(['token' => 'foo', 'url' => 'bar']);
        $stagingFactory = new OutputStrategyFactory(
            new ClientWrapper(
                $storageApiClient,
                null,
                new NullLogger(),
                ''
            ),
            new NullLogger(),
            'json'
        );

        $providerFactory = new ComponentWorkspaceProviderFactory(
            new Components($storageApiClient),
            new Workspaces($storageApiClient),
            'my-test-component',
            'my-test-config'
        );
        $init = new OutputProviderInitializer($stagingFactory, $providerFactory);

        $init->initializeProviders(
            OutputStrategyFactory::WORKSPACE_REDSHIFT,
            [
                'owner' => [
                    'hasSynapse' => true,
                    'hasRedshift' => true,
                    'hasSnowflake' => true,
                    'fileStorageProvider' => 'azure',
                ],
            ],
            '/tmp/random/data'
        );
        self::assertInstanceOf(OutputFileLocal::class, $stagingFactory->getFileOutputStrategy(OutputStrategyFactory::LOCAL));
        self::assertInstanceOf(AllEncompassingTableStrategy::class, $stagingFactory->getTableOutputStrategy(OutputStrategyFactory::LOCAL));
        self::assertInstanceOf(OutputFileLocal::class, $stagingFactory->getFileOutputStrategy(OutputStrategyFactory::WORKSPACE_REDSHIFT));
        self::assertInstanceOf(AllEncompassingTableStrategy::class, $stagingFactory->getTableOutputStrategy(OutputStrategyFactory::WORKSPACE_REDSHIFT));

        $this->expectExceptionMessage('The project does not support "workspace-snowflake" table output backend.');
        $this->expectException(InvalidOutputException::class);
        $stagingFactory->getTableOutputStrategy(OutputStrategyFactory::WORKSPACE_SNOWFLAKE);
    }

    public function testInitializeOutputSnowflake()
    {
        $storageApiClient = new Client(['token' => 'foo', 'url' => 'bar']);
        $stagingFactory = new OutputStrategyFactory(
            new ClientWrapper(
                $storageApiClient,
                null,
                new NullLogger(),
                ''
            ),
            new NullLogger(),
            'json'
        );

        $providerFactory = new ComponentWorkspaceProviderFactory(
            new Components($storageApiClient),
            new Workspaces($storageApiClient),
            'my-test-component',
            'my-test-config'
        );
        $init = new OutputProviderInitializer($stagingFactory, $providerFactory);

        $init->initializeProviders(
            OutputStrategyFactory::WORKSPACE_SNOWFLAKE,
            [
                'owner' => [
                    'hasSynapse' => true,
                    'hasRedshift' => true,
                    'hasSnowflake' => true,
                    'fileStorageProvider' => 'azure',
                ],
            ],
            '/tmp/random/data'
        );
        self::assertInstanceOf(OutputFileLocal::class, $stagingFactory->getFileOutputStrategy(OutputStrategyFactory::LOCAL));
        self::assertInstanceOf(AllEncompassingTableStrategy::class, $stagingFactory->getTableOutputStrategy(OutputStrategyFactory::LOCAL));
        self::assertInstanceOf(OutputFileLocal::class, $stagingFactory->getFileOutputStrategy(OutputStrategyFactory::WORKSPACE_SNOWFLAKE));
        self::assertInstanceOf(AllEncompassingTableStrategy::class, $stagingFactory->getTableOutputStrategy(OutputStrategyFactory::WORKSPACE_SNOWFLAKE));

        $this->expectExceptionMessage('The project does not support "workspace-redshift" table output backend.');
        $this->expectException(InvalidOutputException::class);
        $stagingFactory->getTableOutputStrategy(OutputStrategyFactory::WORKSPACE_REDSHIFT);
    }

    public function testInitializeOutputSynapse()
    {
        $storageApiClient = new Client(['token' => 'foo', 'url' => 'bar']);
        $stagingFactory = new OutputStrategyFactory(
            new ClientWrapper(
                $storageApiClient,
                null,
                new NullLogger(),
                ''
            ),
            new NullLogger(),
            'json'
        );

        $providerFactory = new ComponentWorkspaceProviderFactory(
            new Components($storageApiClient),
            new Workspaces($storageApiClient),
            'my-test-component',
            'my-test-config'
        );
        $init = new OutputProviderInitializer($stagingFactory, $providerFactory);

        $init->initializeProviders(
            OutputStrategyFactory::WORKSPACE_SYNAPSE,
            [
                'owner' => [
                    'hasSynapse' => true,
                    'hasRedshift' => true,
                    'hasSnowflake' => true,
                    'fileStorageProvider' => 'azure',
                ],
            ],
            '/tmp/random/data'
        );

        self::assertInstanceOf(OutputFileLocal::class, $stagingFactory->getFileOutputStrategy(OutputStrategyFactory::LOCAL));
        self::assertInstanceOf(AllEncompassingTableStrategy::class, $stagingFactory->getTableOutputStrategy(OutputStrategyFactory::LOCAL));
        self::assertInstanceOf(OutputFileLocal::class, $stagingFactory->getFileOutputStrategy(OutputStrategyFactory::WORKSPACE_SYNAPSE));
        self::assertInstanceOf(AllEncompassingTableStrategy::class, $stagingFactory->getTableOutputStrategy(OutputStrategyFactory::WORKSPACE_SYNAPSE));

        $this->expectExceptionMessage('The project does not support "workspace-snowflake" table output backend.');
        $this->expectException(InvalidOutputException::class);
        $stagingFactory->getTableOutputStrategy(OutputStrategyFactory::WORKSPACE_SNOWFLAKE);
    }

    public function testInitializeOutputAbs()
    {
        if (!getenv('RUN_SYNAPSE_TESTS')) {
            self::markTestSkipped('Synapse test is disabled.');
        }

        $stagingFactory = new OutputStrategyFactory(
            new ClientWrapper(
                new Client([
                    'url' => getenv('STORAGE_API_URL_SYNAPSE'),
                    'token' => getenv('STORAGE_API_TOKEN_SYNAPSE'),
                ]),
                null,
                new NullLogger(),
                ''
            ),
            new NullLogger(),
            'json'
        );

        $components = new Components($stagingFactory->getClientWrapper()->getBasicClient());
        try {
            $components->deleteConfiguration('keboola.runner-workspace-abs-test', 'my-test-config');
        } catch (ClientException $e) {
            if ($e->getCode() !== 404) {
                throw $e;
            }
        }

        $configuration = new Configuration();
        $configuration->setConfigurationId('my-test-config');
        $configuration->setName($configuration->getConfigurationId());
        $configuration->setComponentId('keboola.runner-workspace-abs-test');
        $components->addConfiguration($configuration);

        $providerFactory = new ComponentWorkspaceProviderFactory(
            $components,
            new Workspaces($stagingFactory->getClientWrapper()->getBasicClient()),
            'keboola.runner-workspace-abs-test',
            'my-test-config'
        );
        $init = new OutputProviderInitializer($stagingFactory, $providerFactory);

        $init->initializeProviders(
            OutputStrategyFactory::WORKSPACE_ABS,
            [
                'owner' => [
                    'hasSynapse' => true,
                    'hasRedshift' => true,
                    'hasSnowflake' => true,
                    'fileStorageProvider' => 'azure',
                ],
            ],
            '/tmp/random/data'
        );

        self::assertInstanceOf(OutputFileLocal::class, $stagingFactory->getFileOutputStrategy(OutputStrategyFactory::LOCAL));
        self::assertInstanceOf(AllEncompassingTableStrategy::class, $stagingFactory->getTableOutputStrategy(OutputStrategyFactory::LOCAL));
        self::assertInstanceOf(ABSWorkspace::class, $stagingFactory->getFileOutputStrategy(OutputStrategyFactory::WORKSPACE_ABS));
        self::assertInstanceOf(AllEncompassingTableStrategy::class, $stagingFactory->getTableOutputStrategy(OutputStrategyFactory::WORKSPACE_ABS));

        $this->expectExceptionMessage('The project does not support "workspace-snowflake" table output backend.');
        $this->expectException(InvalidOutputException::class);
        $stagingFactory->getTableOutputStrategy(OutputStrategyFactory::WORKSPACE_SNOWFLAKE);
    }
}