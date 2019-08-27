<?php

namespace Seungmun\LaravelYandexCheckout\Http\Controllers;

use Exception;
use Throwable;
use Illuminate\Http\Response;
use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Symfony\Component\Debug\Exception\FatalThrowableError;

trait HandlesCheckoutErrors
{
    /**
     * Perform the given callback with exception handling.
     *
     * @param  \Closure  $callback
     * @return \Illuminate\Http\Response
     */
    protected function withErrorHandling($callback)
    {
        $debug = $this->configuration()->get('app.debug');

        try {
            return $callback();
        } catch (Exception $e) {
            $this->exceptionHandler()->report($e);

            return new Response($debug ? $e->getMessage() : 'Error occurred.', 500);
        } catch (Throwable $e) {
            $this->exceptionHandler()->report(new FatalThrowableError($e));

            return new Response($debug ? $e->getMessage() : 'Error occurred.', 500);
        }
    }

    /**
     * Get the configuration repository instance.
     *
     * @return \Illuminate\Contracts\Config\Repository
     */
    protected function configuration()
    {
        return Container::getInstance()->make(Repository::class);
    }

    /**
     * Get the exception handler instance.
     *
     * @return \Illuminate\Contracts\Debug\ExceptionHandler
     */
    protected function exceptionHandler()
    {
        return Container::getInstance()->make(ExceptionHandler::class);
    }
}