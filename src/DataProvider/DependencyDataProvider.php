<?php


namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Dependency;
use Ramsey\Uuid\Uuid;

class DependencyDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface, ItemDataProviderInterface
{
    private $projectDir;

    public function __construct($projectDir)
    {
        $this->projectDir = $projectDir;
    }

    private function getDependencies(): array
    {
        $path = $this->projectDir . '/composer.json';
        $data =  json_decode(file_get_contents($path), true);
        return $data['require'];
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        // Todo: appliquer un filter personnalisé sur une entité non prise en charge par doctrine
        $items = [];
        $dependencies = $this->getDependencies();

        foreach ($dependencies as $name => $version) {
            $items[] = new Dependency(Uuid::uuid5(Uuid::NAMESPACE_URL, $name)->toString(), $name, $version);
        }

        return $items;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Dependency::class;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $dependencies = $this->getDependencies();
        foreach ($dependencies as $name => $version) {
            $uuid = Uuid::uuid5(Uuid::NAMESPACE_URL, $name)->toString();
            if ($uuid === $id) {
                return new Dependency($uuid, $name, $version);
            }
        }

        return null;
    }
}