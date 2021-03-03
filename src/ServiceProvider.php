<?php

namespace Luke\Yonbip;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['yon'] = function ($pimple) {
            $yon = new Yon($pimple);
            return $yon;
        };

        $pimple['yon.accessToken'] = function ($pimple) {
            $config = $pimple->getConfig();
            $accessToken = new YonAccessToken(
                $pimple,
                $config['yon']['appKey'],
                $config['yon']['appSecret'],
                $config['yon']['token_api']
            );

            return $accessToken;
        };

    }
}