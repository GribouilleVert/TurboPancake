<?php
namespace TurboPancake\Middlewares\Internals;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use TurboPancake\Exceptions\SystemException;

class RequestParametersCustomsMiddleware implements MiddlewareInterface {

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        $allowedGetArray = $this->getGetAllowedArrayKey();
        $newQueryParams = [];
        foreach ($request->getQueryParams() as $key => $value) {
            if (is_array($value) AND !in_array($key, $allowedGetArray)) {
                continue;
            }

            try {
                $value = strval($value);
            } catch (Throwable $t) {
                continue;
            }

            $newQueryParams[$key] = $value;
        }


        $allowedPostArray = $this->getPostAllowedArrayKey();
        $newBodyParams = [];
        foreach ($request->getParsedBody() as $key => $value) {
            if (is_array($value) AND !in_array($key, $allowedPostArray)) {
                continue;
            }

            try {
                $value = strval($value);
            } catch (Throwable $t) {
                continue;
            }

            $newBodyParams[$key] = $value;
        }

        return $handler->handle($request
            ->withQueryParams($newQueryParams)
            ->withParsedBody($newBodyParams));
    }

    private function getGetAllowedArrayKey(): array
    {
        if ($this->container->has('turbopancake.getCustoms.allowedArrayKeys')) {
            $_ = $this->container->get('turbopancake.getCustoms.allowedArrayKeys');
            if (!is_array($_)) {
                throw new SystemException('Get customs config is invalid: "turbopancake.getCustoms.allowedArrayKeys" is not an array.', SystemException::SEVERITY_LOW);
            }

            foreach ($_ as $allowedGetKey) {
                if (!is_string($allowedGetKey)) {
                    throw new SystemException('Get customs config is invalid: "turbopancake.getCustoms.allowedArrayKeys" contains non string values.', SystemException::SEVERITY_LOW);
                }
            }

            $allowedGetArray = $_;
        } else {
            $allowedGetArray = [];
        }
    }

    private function getPostAllowedArrayKey(): array
    {
        if ($this->container->has('turbopancake.postCustoms.allowedArrayKeys')) {
            $_ = $this->container->get('turbopancake.postCustoms.allowedArrayKeys');
            if (!is_array($_)) {
                throw new SystemException('Post customs config is invalid: "turbopancake.postCustoms.allowedArrayKeys" is not an array.', SystemException::SEVERITY_LOW);
            }

            foreach ($_ as $allowedGetKey) {
                if (!is_string($allowedGetKey)) {
                    throw new SystemException('Post customs config is invalid: "turbopancake.postCustoms.allowedArrayKeys" contains non string values.', SystemException::SEVERITY_LOW);
                }
            }

            return $_;
        }
        return [];
    }
}
