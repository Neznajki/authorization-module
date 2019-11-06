<?php declare(strict_types=1);


namespace App\Service;


use JsonRpcServerBundle\Exception\RpcMessageException;

class ServerHelperService
{
    /**
     * @param array $serverParams
     * @return string
     * @throws RpcMessageException
     */
    public function getIpAddress(array $serverParams): string
    {
        if (! empty($serverParams['HTTP_CLIENT_IP'])) {
            $ip = $serverParams['HTTP_CLIENT_IP'];
        } elseif (! empty($serverParams['HTTP_X_FORWARDED_FOR'])) {
            $ip = $serverParams['HTTP_X_FORWARDED_FOR'];
        } elseif (! empty($serverParams['REMOTE_ADDR'])) {
            $ip = $serverParams['REMOTE_ADDR'];
        } else {
            throw new RpcMessageException('client IP address could not be detected');
        }

        return $ip;
    }

    /**
     * @param array $serverParams
     * @return string
     * @throws RpcMessageException
     */
    public function getUserAgent(array $serverParams): string
    {
        if (empty($serverParams['HTTP_USER_AGENT'])) {
            throw new RpcMessageException('user agent could not be detected');
        }

        return $serverParams['HTTP_USER_AGENT'];
    }
}
