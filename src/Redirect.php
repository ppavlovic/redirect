<?php

namespace G4\Redirect;

use G4\Constants\Http;
use G4\ValueObject\Location;
use G4\ValueObject\Url;

class Redirect
{
    /**
     * @var Location
     */
    private $location;

    public function __construct(Location $location = null)
    {
        $this->location = $location;
    }

    public function location(Url $url)
    {
        $this->sendHeader('Location: ' . (string) $url, Http::CODE_301);
        $this->terminate();
    }

    public function redirectPermanently()
    {
        $this->doRedirect(Http::CODE_301);
    }

    public function redirectFound()
    {
        $this->doRedirect(Http::CODE_302);
    }

    public function redirectSeeOther()
    {
        $this->doRedirect(Http::CODE_303);
    }

    public function redirectNotModified()
    {
        $this->doRedirect(Http::CODE_304);
    }

    public function redirectUseProxy()
    {
        $this->doRedirect(Http::CODE_305);
    }

    public function redirectSwitchProxy()
    {
        $this->doRedirect(Http::CODE_306);
    }

    public function redirectTemporary()
    {
        $this->doRedirect(Http::CODE_307);
    }

    public function redirectPermanent()
    {
        $this->doRedirect(Http::CODE_308);
    }

    private function doRedirect($code)
    {
        $this->sendHeaderWithCode('Location: ' . (string) $this->location, false, $code);
        $this->terminate();
    }

    /**
     * @codeCoverageIgnore
     */
    protected function sendHeader($header, $code)
    {
        header($header, $code);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function sendHeaderWithCode($header, $replace, $code)
    {
        header($header, $replace, $code);
    }

    /**
     * @codeCoverageIgnore
     */
    protected function terminate()
    {
        exit();
    }
}
