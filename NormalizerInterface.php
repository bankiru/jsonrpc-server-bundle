<?php

namespace Bankiru\Api\JsonRpc;

interface NormalizerInterface
{
    /**
     * Normalizes entity representation
     *
     * @param object|\stdClass|array|null $entity  original entity
     * @param array                       $context request context for normalization
     *
     * @return array|null Normalized entity representation
     */
    public function normalize($entity, array $context = []);
}
