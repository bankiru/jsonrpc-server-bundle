<?php

namespace Bankiru\Api\JsonRpc\Adapters\Symfony;

use Bankiru\Api\JsonRpc\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface as SymfonyNormalizerInterface;

final class SymfonyNormalizerAdapter implements NormalizerInterface
{
    /** @var SymfonyNormalizerInterface */
    private $normalizer;

    /**
     * SymfonyNormalizerAdapter constructor.
     *
     * @param SymfonyNormalizerInterface $normalizer
     */
    public function __construct(SymfonyNormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /** {@inheritdoc} */
    public function normalize($entity, array $context = [])
    {
        if (null === $entity) {
            return null;
        }

        return $this->normalizer->normalize($entity, ['groups' => $context]);
    }
}
