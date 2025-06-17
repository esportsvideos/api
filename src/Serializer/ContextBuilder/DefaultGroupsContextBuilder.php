<?php

namespace App\Serializer\ContextBuilder;

use ApiPlatform\State\SerializerContextBuilderInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\HttpFoundation\Request;

#[AsDecorator(decorates: 'api_platform.serializer.context_builder', priority: 1)]
final readonly class DefaultGroupsContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct(
        private SerializerContextBuilderInterface $decorated,
    ) {
    }

    /**
     * @param array<mixed, mixed>|null $extractedAttributes
     */
    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);

        if (isset($context['groups']) && is_array($context['groups']) && true === $normalization) {
            $context['groups'][] = 'entity_id:read';
            $context['groups'][] = 'timestampable:read';
        }

        return $context;
    }
}
