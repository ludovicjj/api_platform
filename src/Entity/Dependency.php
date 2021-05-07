<?php


namespace App\Entity;


use ApiPlatform\Core\Annotation\ApiProperty;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\Length(
     *     min = 2
     * )
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ApiProperty(
     *     description="Version de la description",
     *     openapiContext={"example" = "5.2.*"}
     * )
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 1
     * )
     * @Groups({"write:dependency"})
     */
    private $version;

    public function __construct(
        string $name,
        string $version
    ) {
        $this->uuid = Uuid::uuid5(Uuid::NAMESPACE_URL, $name)->toString();
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

    /**
     * @param string $version
     * @return $this
     */
    public function setVersion(string $version): self
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}