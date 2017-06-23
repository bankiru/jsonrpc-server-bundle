<?php

namespace Bankiru\Api\JsonRpc\Adapters\JMS;

use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\Metadata\PropertyMetadata;
use Metadata\Driver\DriverInterface;

class HandledTypeDriver implements DriverInterface
{
    /** @var  DriverInterface */
    private $driver;
    /** @var  Reader */
    private $reader;

    /**
     * HandledTypeDriver constructor.
     *
     * @param DriverInterface $driver
     * @param Reader          $reader
     */
    public function __construct(DriverInterface $driver, Reader $reader)
    {
        $this->driver = $driver;
        $this->reader = $reader;
    }

    /**
     * @param \ReflectionClass $class
     *
     * @return \Metadata\ClassMetadata
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        $metadata = $this->driver->loadMetadataForClass($class);
        foreach ($metadata->propertyMetadata as $key => $propertyMetadata) {
            $type = $propertyMetadata->type['name'];

            if (!$propertyMetadata->reflection) {
                continue;
            }

            /** @var PropertyMetadata $propertyMetadata */
            /** @var HandledType $annot */
            $annot = $this->reader->getPropertyAnnotation($propertyMetadata->reflection, HandledType::class);
            if (!$annot) {
                continue;
            }

            $isCollection   = false;
            $collectionType = null;

            if (in_array($type, ['array', 'ArrayCollection'], true)) {
                $isCollection   = true;
                $collectionType = $type;
                $type           = $propertyMetadata->type['params'][0]['name'];
            }

            $handler = $annot->handler ?: 'Relation';

            $newType = sprintf('%s<%s>', $handler, $type);
            if ($isCollection) {
                $newType = sprintf('%s<%s<%s>>', $collectionType, $handler, $type);
            }

            $propertyMetadata->setType($newType);
        }

        return $metadata;
    }
}
