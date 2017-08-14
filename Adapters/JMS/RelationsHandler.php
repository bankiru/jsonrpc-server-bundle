<?php

namespace Bankiru\Api\JsonRpc\Adapters\JMS;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

final class RelationsHandler
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * RelationsHandler constructor.
     *
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function serializeRelation(JsonSerializationVisitor $visitor, $relation)
    {
        if ($relation instanceof \Traversable) {
            $relation = iterator_to_array($relation);
        }

        if (is_array($relation)) {
            return array_map([$this, 'getSingleEntityRelation'], $relation);
        }

        return $this->getSingleEntityRelation($relation);
    }

    public function deserializeRelation(JsonDeserializationVisitor $visitor, $relation, array $type)
    {
        $className = isset($type['params'][0]['name']) ? $type['params'][0]['name'] : null;

        if (!class_exists($className)) {
            throw new \InvalidArgumentException('Class name should be explicitly set for deserialization');
        }

        /** @var ClassMetadata $metadata */
        $metadata = $this->manager->getClassMetadata($className);

        if (!is_array($relation)) {
            return $this->deserializeIdentifier($className, $relation);
        }

        $single = false;
        if ($metadata->isIdentifierComposite) {
            $single = true;
            foreach ($metadata->getIdentifierFieldNames() as $idName) {
                $single = $single && array_key_exists($idName, $relation);
            }
        }

        if ($single) {
            return $this->deserializeIdentifier($className, $relation);
        }

        $objects = [];
        foreach ($relation as $idSet) {
            $objects[] = $this->deserializeIdentifier($className, $idSet);
        }

        return $objects;
    }

    /**
     * @param $relation
     *
     * @return array|mixed
     */
    private function getSingleEntityRelation($relation)
    {
        $metadata = $this->manager->getClassMetadata(get_class($relation));

        $ids = $metadata->getIdentifierValues($relation);
        if (1 === count($metadata->getIdentifierFieldNames())) {
            $ids = array_shift($ids);
        }

        return $ids;
    }

    /**
     * @param string $className
     * @param mixed  $identifier
     *
     * @return object
     */
    private function deserializeIdentifier($className, $identifier)
    {
        if (method_exists($this->manager, 'getReference')) {
            return $this->manager->getReference($className, $identifier);
        }

        return $this->manager->find($className, $identifier);
    }
}
