<?php

namespace Bankiru\Api\JsonRpc\Adapters\Builtin;

use Bankiru\Api\JsonRpc\NormalizerInterface;

final class BuiltinNormalizerAdapter implements NormalizerInterface
{
    /** {@inheritdoc} */
    public function normalize($entity, array $context = [])
    {
        if (null === $entity) {
            return null;
        }

        return json_decode(json_encode($entity), true);
    }
}
