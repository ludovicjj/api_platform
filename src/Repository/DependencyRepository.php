<?php


namespace App\Repository;

use App\Entity\Dependency;

class DependencyRepository
{
    private $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    private function getDependencies(): array
    {
        $path = $this->projectDir . '/dependency.json';
        $data =  json_decode(file_get_contents($path), true);
        return $data['require'];
    }

    /**
     * @return Dependency[]
     */
    public function findAll(): array
    {
        $items = [];
        $dependencies = $this->getDependencies();
        foreach ($dependencies as $name => $version) {
            $items[] = new Dependency($name, $version);
        }

        return $items;
    }

    public function find(string $uuid): ?Dependency
    {
        $dependencies = $this->findAll();
        foreach ($dependencies as $dependency) {
            if ($dependency->getUuid() === $uuid) {
                return $dependency;
            }
        }

        return null;
    }

    public function persist(Dependency $dependency)
    {
        $path = $this->projectDir . '/dependency.json';
        $data =  json_decode(file_get_contents($path), true);
        $data['require'][$dependency->getName()] = $dependency->getVersion();

        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    public function remove(Dependency $dependency)
    {
        $path = $this->projectDir . '/dependency.json';
        $data =  json_decode(file_get_contents($path), true);
        unset($data['require'][$dependency->getName()]);

        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}