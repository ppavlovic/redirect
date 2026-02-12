<?php

use G4\Redirect\Redirect;
use G4\ValueObject\Location;
use G4\ValueObject\Url;
use PHPUnit\Framework\TestCase;

class TestableRedirect extends Redirect
{
    public $headersSent = [];
    public $terminated = false;

    protected function sendHeader($header, $code)
    {
        $this->headersSent[] = ['header' => $header, 'code' => $code, 'replace' => null];
    }

    protected function sendHeaderWithCode($header, $replace, $code)
    {
        $this->headersSent[] = ['header' => $header, 'replace' => $replace, 'code' => $code];
    }

    protected function terminate()
    {
        $this->terminated = true;
    }
}

class RedirectTest extends TestCase
{
    public function testRedirectToLocation()
    {
        $redirect = new Redirect();

        $this->assertInstanceOf(Redirect::class, $redirect);
    }

    public function testConstructorWithoutLocation()
    {
        $redirect = new Redirect();

        $this->assertInstanceOf(Redirect::class, $redirect);
    }

    public function testConstructorWithLocation()
    {
        $location = new Location('https://example.com');
        $redirect = new Redirect($location);

        $this->assertInstanceOf(Redirect::class, $redirect);
    }

    public function testLocationMethodAcceptsUrlParameter()
    {
        $redirect = new Redirect();
        $url = new Url('https://example.com/test');

        $reflection = new \ReflectionMethod($redirect, 'location');
        $this->assertTrue($reflection->isPublic());
        $this->assertEquals(1, $reflection->getNumberOfParameters());
    }

    public function testDoRedirectMethodExists()
    {
        $location = new Location('https://example.com');
        $redirect = new Redirect($location);

        $reflection = new \ReflectionClass($redirect);
        $this->assertTrue($reflection->hasMethod('doRedirect'));
    }

    public function testDoRedirectMethodIsPrivate()
    {
        $location = new Location('https://example.com');
        $redirect = new Redirect($location);

        $reflection = new \ReflectionMethod($redirect, 'doRedirect');
        $this->assertTrue($reflection->isPrivate());
    }

    public function testDoRedirectMethodAcceptsCodeParameter()
    {
        $location = new Location('https://example.com');
        $redirect = new Redirect($location);

        $reflection = new \ReflectionMethod($redirect, 'doRedirect');
        $this->assertEquals(1, $reflection->getNumberOfParameters());
    }

    public function testRedirectPermanentlyMethodExists()
    {
        $location = new Location('https://example.com');
        $redirect = new Redirect($location);

        $this->assertTrue(method_exists($redirect, 'redirectPermanently'));
    }

    public function testRedirectFoundMethodExists()
    {
        $location = new Location('https://example.com');
        $redirect = new Redirect($location);

        $this->assertTrue(method_exists($redirect, 'redirectFound'));
    }

    public function testRedirectSeeOtherMethodExists()
    {
        $location = new Location('https://example.com');
        $redirect = new Redirect($location);

        $this->assertTrue(method_exists($redirect, 'redirectSeeOther'));
    }

    public function testRedirectNotModifiedMethodExists()
    {
        $location = new Location('https://example.com');
        $redirect = new Redirect($location);

        $this->assertTrue(method_exists($redirect, 'redirectNotModified'));
    }

    public function testRedirectUseProxyMethodExists()
    {
        $location = new Location('https://example.com');
        $redirect = new Redirect($location);

        $this->assertTrue(method_exists($redirect, 'redirectUseProxy'));
    }

    public function testRedirectSwitchProxyMethodExists()
    {
        $location = new Location('https://example.com');
        $redirect = new Redirect($location);

        $this->assertTrue(method_exists($redirect, 'redirectSwitchProxy'));
    }

    public function testRedirectTemporaryMethodExists()
    {
        $location = new Location('https://example.com');
        $redirect = new Redirect($location);

        $this->assertTrue(method_exists($redirect, 'redirectTemporary'));
    }

    public function testRedirectPermanentMethodExists()
    {
        $location = new Location('https://example.com');
        $redirect = new Redirect($location);

        $this->assertTrue(method_exists($redirect, 'redirectPermanent'));
    }

    public function testAllRedirectMethodsAreCallable()
    {
        $location = new Location('https://example.com');
        $redirect = new Redirect($location);

        $methods = [
            'redirectPermanently',
            'redirectFound',
            'redirectSeeOther',
            'redirectNotModified',
            'redirectUseProxy',
            'redirectSwitchProxy',
            'redirectTemporary',
            'redirectPermanent',
        ];

        foreach ($methods as $method) {
            $this->assertTrue(
                is_callable([$redirect, $method]),
                "Method {$method} should be callable"
            );
        }
    }

    public function testAllPublicMethodsArePublic()
    {
        $location = new Location('https://example.com');
        $redirect = new Redirect($location);

        $methods = [
            'location',
            'redirectPermanently',
            'redirectFound',
            'redirectSeeOther',
            'redirectNotModified',
            'redirectUseProxy',
            'redirectSwitchProxy',
            'redirectTemporary',
            'redirectPermanent',
        ];

        foreach ($methods as $method) {
            $reflection = new \ReflectionMethod($redirect, $method);
            $this->assertTrue(
                $reflection->isPublic(),
                "Method {$method} should be public"
            );
        }
    }

    public function testLocationPropertyIsPrivate()
    {
        $location = new Location('https://example.com');
        $redirect = new Redirect($location);

        $reflection = new \ReflectionClass($redirect);
        $property = $reflection->getProperty('location');
        
        $this->assertTrue($property->isPrivate());
    }

    public function testLocationPropertyTypeIsLocation()
    {
        $location = new Location('https://example.com');
        $redirect = new Redirect($location);

        $reflection = new \ReflectionClass($redirect);
        $property = $reflection->getProperty('location');
        $property->setAccessible(true);
        
        $value = $property->getValue($redirect);
        $this->assertInstanceOf(Location::class, $value);
    }

    public function testConstructorSetsLocationProperty()
    {
        $location = new Location('https://example.com/path');
        $redirect = new Redirect($location);

        $reflection = new \ReflectionClass($redirect);
        $property = $reflection->getProperty('location');
        $property->setAccessible(true);
        
        $value = $property->getValue($redirect);
        $this->assertSame($location, $value);
    }

    public function testLocationMethodSendsHeaderAndTerminates()
    {
        $redirect = new TestableRedirect();
        $url = new Url('https://example.com/test');

        $redirect->location($url);

        $this->assertCount(1, $redirect->headersSent);
        $this->assertEquals('Location: https://example.com/test', $redirect->headersSent[0]['header']);
        $this->assertEquals(301, $redirect->headersSent[0]['code']);
        $this->assertTrue($redirect->terminated);
    }

    public function testRedirectPermanentlySendsCode301()
    {
        $location = new Location('https://example.com');
        $redirect = new TestableRedirect($location);

        $redirect->redirectPermanently();

        $this->assertCount(1, $redirect->headersSent);
        $this->assertEquals('Location: https://example.com', $redirect->headersSent[0]['header']);
        $this->assertEquals(301, $redirect->headersSent[0]['code']);
        $this->assertFalse($redirect->headersSent[0]['replace']);
        $this->assertTrue($redirect->terminated);
    }

    public function testRedirectFoundSendsCode302()
    {
        $location = new Location('https://example.com');
        $redirect = new TestableRedirect($location);

        $redirect->redirectFound();

        $this->assertCount(1, $redirect->headersSent);
        $this->assertEquals('Location: https://example.com', $redirect->headersSent[0]['header']);
        $this->assertEquals(302, $redirect->headersSent[0]['code']);
        $this->assertFalse($redirect->headersSent[0]['replace']);
        $this->assertTrue($redirect->terminated);
    }

    public function testRedirectSeeOtherSendsCode303()
    {
        $location = new Location('https://example.com');
        $redirect = new TestableRedirect($location);

        $redirect->redirectSeeOther();

        $this->assertCount(1, $redirect->headersSent);
        $this->assertEquals('Location: https://example.com', $redirect->headersSent[0]['header']);
        $this->assertEquals(303, $redirect->headersSent[0]['code']);
        $this->assertFalse($redirect->headersSent[0]['replace']);
        $this->assertTrue($redirect->terminated);
    }

    public function testRedirectNotModifiedSendsCode304()
    {
        $location = new Location('https://example.com');
        $redirect = new TestableRedirect($location);

        $redirect->redirectNotModified();

        $this->assertCount(1, $redirect->headersSent);
        $this->assertEquals('Location: https://example.com', $redirect->headersSent[0]['header']);
        $this->assertEquals(304, $redirect->headersSent[0]['code']);
        $this->assertFalse($redirect->headersSent[0]['replace']);
        $this->assertTrue($redirect->terminated);
    }

    public function testRedirectUseProxySendsCode305()
    {
        $location = new Location('https://example.com');
        $redirect = new TestableRedirect($location);

        $redirect->redirectUseProxy();

        $this->assertCount(1, $redirect->headersSent);
        $this->assertEquals('Location: https://example.com', $redirect->headersSent[0]['header']);
        $this->assertEquals(305, $redirect->headersSent[0]['code']);
        $this->assertFalse($redirect->headersSent[0]['replace']);
        $this->assertTrue($redirect->terminated);
    }

    public function testRedirectSwitchProxySendsCode306()
    {
        $location = new Location('https://example.com');
        $redirect = new TestableRedirect($location);

        $redirect->redirectSwitchProxy();

        $this->assertCount(1, $redirect->headersSent);
        $this->assertEquals('Location: https://example.com', $redirect->headersSent[0]['header']);
        $this->assertEquals(306, $redirect->headersSent[0]['code']);
        $this->assertFalse($redirect->headersSent[0]['replace']);
        $this->assertTrue($redirect->terminated);
    }

    public function testRedirectTemporarySendsCode307()
    {
        $location = new Location('https://example.com');
        $redirect = new TestableRedirect($location);

        $redirect->redirectTemporary();

        $this->assertCount(1, $redirect->headersSent);
        $this->assertEquals('Location: https://example.com', $redirect->headersSent[0]['header']);
        $this->assertEquals(307, $redirect->headersSent[0]['code']);
        $this->assertFalse($redirect->headersSent[0]['replace']);
        $this->assertTrue($redirect->terminated);
    }

    public function testRedirectPermanentSendsCode308()
    {
        $location = new Location('https://example.com');
        $redirect = new TestableRedirect($location);

        $redirect->redirectPermanent();

        $this->assertCount(1, $redirect->headersSent);
        $this->assertEquals('Location: https://example.com', $redirect->headersSent[0]['header']);
        $this->assertEquals(308, $redirect->headersSent[0]['code']);
        $this->assertFalse($redirect->headersSent[0]['replace']);
        $this->assertTrue($redirect->terminated);
    }

    public function testSendHeaderMethodIsProtected()
    {
        $redirect = new Redirect();
        $reflection = new \ReflectionMethod($redirect, 'sendHeader');
        
        $this->assertTrue($reflection->isProtected());
    }

    public function testSendHeaderWithCodeMethodIsProtected()
    {
        $redirect = new Redirect();
        $reflection = new \ReflectionMethod($redirect, 'sendHeaderWithCode');
        
        $this->assertTrue($reflection->isProtected());
    }

    public function testTerminateMethodIsProtected()
    {
        $redirect = new Redirect();
        $reflection = new \ReflectionMethod($redirect, 'terminate');
        
        $this->assertTrue($reflection->isProtected());
    }

    public function testAllRedirectMethodsCallDoRedirect()
    {
        $location = new Location('https://example.com/path');
        
        $methods = [
            'redirectPermanently' => 301,
            'redirectFound' => 302,
            'redirectSeeOther' => 303,
            'redirectNotModified' => 304,
            'redirectUseProxy' => 305,
            'redirectSwitchProxy' => 306,
            'redirectTemporary' => 307,
            'redirectPermanent' => 308,
        ];

        foreach ($methods as $method => $expectedCode) {
            $redirect = new TestableRedirect($location);
            $redirect->$method();
            
            $this->assertEquals($expectedCode, $redirect->headersSent[0]['code'], 
                "Method {$method} should send HTTP code {$expectedCode}");
        }
    }

    public function testLocationWithDifferentUrls()
    {
        $urls = [
            'https://example.com',
            'https://example.com/path',
            'https://example.com/path?query=value',
            'https://example.com:8080/path',
        ];

        foreach ($urls as $urlString) {
            $redirect = new TestableRedirect();
            $url = new Url($urlString);
            
            $redirect->location($url);
            
            $this->assertEquals('Location: ' . $urlString, $redirect->headersSent[0]['header']);
            $this->assertEquals(301, $redirect->headersSent[0]['code']);
        }
    }

    public function testRedirectWithDifferentLocations()
    {
        $locations = [
            'https://example.com',
            'https://example.com/path',
            'https://example.com/path?query=value',
            'https://example.com:8080/path',
        ];

        foreach ($locations as $locationString) {
            $location = new Location($locationString);
            $redirect = new TestableRedirect($location);
            
            $redirect->redirectPermanently();
            
            $this->assertEquals('Location: ' . $locationString, $redirect->headersSent[0]['header']);
            $this->assertEquals(301, $redirect->headersSent[0]['code']);
        }
    }

    public function testSendHeaderMethodCanBeCalled()
    {
        $redirect = new Redirect();

        $reflection = new \ReflectionMethod($redirect, 'sendHeader');
        $reflection->setAccessible(true);

        // Verify the method exists and is callable
        $this->assertTrue($reflection->isProtected());
        $this->assertEquals(2, $reflection->getNumberOfParameters());
    }

    public function testSendHeaderWithCodeMethodCanBeCalled()
    {
        $redirect = new Redirect();

        $reflection = new \ReflectionMethod($redirect, 'sendHeaderWithCode');
        $reflection->setAccessible(true);

        // Verify the method exists and is callable
        $this->assertTrue($reflection->isProtected());
        $this->assertEquals(3, $reflection->getNumberOfParameters());
    }

    public function testTerminateMethodCanBeCalled()
    {
        $redirect = new Redirect();
        
        $reflection = new \ReflectionMethod($redirect, 'terminate');
        $reflection->setAccessible(true);

        // Verify the method exists and is callable
        $this->assertTrue($reflection->isProtected());
        $this->assertEquals(0, $reflection->getNumberOfParameters());
    }

    public function testDoRedirectMethodUsesCorrectParameters()
    {
        $location = new Location('https://example.com');
        $redirect = new TestableRedirect($location);

        // Test that doRedirect is called with correct code
        $reflection = new \ReflectionMethod($redirect, 'doRedirect');
        $reflection->setAccessible(true);
        $reflection->invoke($redirect, 301);

        $this->assertCount(1, $redirect->headersSent);
        $this->assertEquals(301, $redirect->headersSent[0]['code']);
    }

    public function testMultipleRedirectCalls()
    {
        $location = new Location('https://example.com');
        
        $redirect1 = new TestableRedirect($location);
        $redirect1->redirectPermanently();
        
        $redirect2 = new TestableRedirect($location);
        $redirect2->redirectFound();
        
        $redirect3 = new TestableRedirect($location);
        $redirect3->redirectTemporary();

        $this->assertEquals(301, $redirect1->headersSent[0]['code']);
        $this->assertEquals(302, $redirect2->headersSent[0]['code']);
        $this->assertEquals(307, $redirect3->headersSent[0]['code']);
    }
}