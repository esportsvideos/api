<?php

namespace App\Serializer\ContextBuilder;

use ApiPlatform\State\SerializerContextBuilderInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[AsDecorator(decorates: 'api_platform.serializer.context_builder', priority: 2)]
final readonly class AdminContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct(private SerializerContextBuilderInterface $decorated, private AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    /**
     * @param array<mixed, mixed>|null $extractedAttributes
     */
    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);

        if (isset($context['groups']) && is_array($context['groups']) && true === $normalization && $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $adminGroups = array_map(static fn ($group) => 'admin:'.$group, $context['groups']);
            $context['groups'] = array_merge(
                $context['groups'],
                $adminGroups,
                [
                    'admin:blameable:read',
                    'admin:timestampable:read',
                ]
            );
        }

        return $context;
    }
}
