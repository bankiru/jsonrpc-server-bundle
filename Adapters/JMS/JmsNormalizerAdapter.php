<?php

namespace Bankiru\Api\JsonRpc\Adapters\JMS;

use Bankiru\Api\JsonRpc\NormalizerInterface;
use JMS\Serializer\ArrayTransformerInterface;
use JMS\Serializer\ContextFactory\SerializationContextFactoryInterface;

final class JmsNormalizerAdapter implements NormalizerInterface
{
    /** @var ArrayTransformerInterface */
    private $normalizer;
    /** @var SerializationContextFactoryInterface */
    private $contextFactory;

    /**
     * JmsNormalizerAdapter constructor.
     *
     * @param ArrayTransformerInterface            $normalizer
     * @param SerializationContextFactoryInterface $contextFactory
     */
    public function __construct(
        ArrayTransformerInterface $normalizer,
        SerializationContextFactoryInterface $contextFactory
    ) {
        $this->normalizer     = $normalizer;
        $this->contextFactory = $contextFactory;
    }

    /** {@inheritdoc} */
    public function normalize($entity, array $context = [])
    {
        if (null === $entity) {
            return null;
        }

        $jmsContext = $this->contextFactory->createSerializationContext();
        $jmsContext->setGroups($context);

        return $this->normalizer->toArray($entity, $jmsContext);
    }
}
