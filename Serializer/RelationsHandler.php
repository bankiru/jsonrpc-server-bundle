<?php

namespace Bankiru\Api\JsonRpc\Serializer;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use JMS\Serializer\Context;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

final class RelationsHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * RelationsHandler constructor.
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager) { $this->manager = $manager; }


    public function serializeRelation(JsonSerializationVisitor $visitor, $relation, array $type, Context $context)
    {
        if ($relation instanceof \Traversable) {
            $relation = iterator_to_array($relation);
        }

        if (is_array($relation)) {
            return array_map([$this, 'getSingleEntityRelation'], $relation);
        }

        return $this->getSingleEntityRelation($relation);
    }

    /**
     * @param $relation
     *
     * @return array|mixed
     */
    protected function getSingleEntityRelation($relation)
    {
        $metadata = $this->manager->getClassMetadata(get_class($relation));

        $ids = $metadata->getIdentifierValues($relation);
        if (!$metadata->isIdentifierComposite) {
            $ids = array_shift($ids);
        }

        return $ids;
    }

    public function deserializeRelation(JsonDeserializationVisitor $visitor, $relation, array $type, Context $context)
    {
        $className = isset($type['params'][0]['name']) ? $type['params'][0]['name'] : null;

        if (!class_exists($className)) {
            throw new \InvalidArgumentException('Class name should be explicitly set for deserialization');
        }

        /** @var ClassMetadata $metadata */
        $metadata = $this->manager->getClassMetadata($className);

        if (!is_array($relation)) {
            return $this->manager->getReference($className, $relation);
        }

        $single = false;
        if ($metadata->isIdentifierComposite) {
            $single = true;
            foreach ($metadata->getIdentifierFieldNames() as $idName) {
                $single = $single && array_key_exists($idName, $relation);
            }
        }

        if ($single) {
            return $this->manager->getReference($className, $relation);
        }

        $objects = [];
        foreach ($relation as $idSet) {
            $objects[] = $this->manager->getReference($className, $idSet);
        }

        return $objects;
    }
}
