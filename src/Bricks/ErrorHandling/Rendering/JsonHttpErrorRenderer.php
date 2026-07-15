<?php declare(strict_types=1);

namespace Concept\Stack\Bricks\ErrorHandling\Rendering;

use Concept\Extensions\ErrorHandlerWhoops\Contracts\HttpErrorRendererInterface;
use Concept\Extensions\Http\Contracts\ResponseFactoryInterface;
use Concept\Extensions\Http\Protocol\HttpStatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Stack recipe: JSON-only HTTP errors (API without view). Requires the http capability.
 */
final class JsonHttpErrorRenderer implements HttpErrorRendererInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
    ) {}

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $errors
     */
    public function render(
        ServerRequestInterface $request,
        int $code,
        ?string $message = null,
        array $data = [],
        array $errors = [],
    ): ResponseInterface {
        return $this->responseFactory->jsonError(
            $message ?? HttpStatusCode::getReasonPhrase($code),
            $code,
            $errors,
        );
    }
}
