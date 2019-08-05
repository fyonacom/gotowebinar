<?php

namespace Slakbal\Gotowebinar\Traits\Client;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

trait AccessProvider
{
    protected $cache_tags = ['GOTO', 'GOTO-AUTH'];


    private function getAuthenticationHeader()
    {
        return ['Authorization' => 'Basic ' . base64_encode($this->getClientId() . ':' . $this->getClientSecret())];
    }


    private function getClientId()
    {
        return config('goto.client_id'); //Consumer Key = Client Id
    }


    private function getClientSecret()
    {
        return config('goto.client_secret');
    }


    private function getBearerHeader()
    {
        return ['Authorization' => 'Bearer ' . $this->getAccessToken()];
    }


    private function getAccessToken()
    {
        return Cache::tags($this->cache_tags)->get('access-token');
    }


    private function setAccessInformation($responseObject)
    {
        dump($responseObject);

        $this->setAccessToken($responseObject->access_token, $responseObject->expires_in)
             ->setRefreshToken($responseObject->refresh_token)
             ->setOrganizerKey($responseObject->organizer_key)
             ->setAccountKey($responseObject->account_key);

        return $this;
    }


    private function setAccountKey($accountKey)
    {
        Cache::tags($this->cache_tags)->forever('account-key', $accountKey);

        return $this;
    }


    private function setOrganizerKey($organizerKey)
    {
        Cache::tags($this->cache_tags)->forever('organizer-key', $organizerKey);

        return $this;
    }


    private function setRefreshToken($refreshToken, $ttlSeconds = null)
    {
        Cache::tags($this->cache_tags)->put('refresh-token', $refreshToken, $ttlSeconds ?? Carbon::now()->addDays(30));

        return $this;
    }


    private function setAccessToken($accessToken, $ttlSeconds = null)
    {
        Cache::tags($this->cache_tags)->put('access-token', $accessToken, $ttlSeconds ?? Carbon::now()->addHour());

        return $this;
    }


    private function hasAccessToken()
    {
        return Cache::tags($this->cache_tags)->has('access-token');
    }


    private function getOrganizerKey()
    {
        return Cache::tags($this->cache_tags)->get('organizer-key');
    }


    private function getAccountKey()
    {
        return Cache::tags($this->cache_tags)->get('account-key');
    }


    private function getTokenType()
    {
        return Cache::tags($this->cache_tags)->get('token-type');
    }


    private function setTokenType($tokenType)
    {
        Cache::tags($this->cache_tags)->forever('token-type', $tokenType);

        return $this;
    }


    private function getRefreshToken()
    {
        return Cache::tags($this->cache_tags)->get('refresh-token');
    }


    private function clearAuthCache()
    {
        Cache::tags('GOTO-AUTH')->flush();

        return $this;
    }

}