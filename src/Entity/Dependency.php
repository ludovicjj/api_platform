<?php


namespace App\Entity;


use ApiPlatform\Core\Annotation\ApiProperty;

class Dependency
{
    /**
     * @ApiProperty(identifier=true)
     */
    private $uuid;

    /**
     * @ApiProperty(
     *     description="Nom de la description"
     * )
     */
    private $name;

    /**
     * @ApiProperty(
     *     description="Version de la description",
     *     openapiContext={"example" = "5.2.*"}
     * )
     */
    private $version;

    public function __construct(
        string $uuid,
        string $name,
        string $version
    ) {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}