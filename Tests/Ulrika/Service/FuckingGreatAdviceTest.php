<?php

class Tests_Ulrika_Service_FuckingGreatAdvice extends PHPUnit_Framework_TestCase
{
    /**
     * @return Ulrika_Service_FuckingGreatAdvice
     */
    protected function createTestService(Zend_Http_Client_Adapter_Test $adapter)
    {
        $service = new Ulrika_Service_FuckingGreatAdvice();
        $service->getHttpClient()->setAdapter($adapter);
        return $service;
    }

    public function testCleanTextField()
    {
        $text = 'Пиши тесты&nbsp;блять!';

        $expected = array('text' => html_entity_decode($text, ENT_COMPAT, 'UTF-8'));
        $actual = Ulrika_Service_FuckingGreatAdvice::cleanTextField(array('text' => $text));

        $this->assertEquals($expected, $actual);
    }

    public function testGetSoundUrl()
    {
        $sound = '123.MP3';

        $advice = array('sound' => $sound);
        $actual = Ulrika_Service_FuckingGreatAdvice::getSoundUrl($advice);
        $expected = 'http://fucking-great-advice.ru:80/files/sounds/' . $sound;

        $this->assertEquals($expected, $actual);

        $advice = array();
        $actual = Ulrika_Service_FuckingGreatAdvice::getSoundUrl($advice);

        $this->assertNull($actual);
    }

    public function testHasSoundAvailable()
    {
        $sound = '123.MP3';

        $advice = array('sound' => $sound);
        $actual = Ulrika_Service_FuckingGreatAdvice::hasSoundAvailable($advice);

        $this->assertTrue($actual);

        $advice = array();
        $actual = Ulrika_Service_FuckingGreatAdvice::hasSoundAvailable($advice);

        $this->assertFalse($actual);
    }

    public function testGetRandom()
    {
        $adapter = new Zend_Http_Client_Adapter_Test();
        $service = $this->createTestService($adapter);

        $id = '123';
        $text = 'Используй mock блять!';
        $advice = array(
            'id' => $id,
            'text' => $text,
        );

        $adapter->setResponse(new Zend_Http_Response(200, array(), Zend_Json::encode($advice)));

        $this->assertEquals($advice, $service->getRandom(false));
        $this->assertEquals('http://fucking-great-advice.ru:80/api/random', $service->getHttpClient()->getUri(true));

        $this->assertEquals($advice, $service->getRandom(true));
        $this->assertEquals('http://fucking-great-advice.ru:80/api/random/censored/', $service->getHttpClient()->getUri(true));
    }

    public function testGetRandomByTag()
    {
        $adapter = new Zend_Http_Client_Adapter_Test();
        $service = $this->createTestService($adapter);

        $tag = 'программисту';
        $id = '123';
        $text = 'Используй mock блять!';
        $advice = array(
            'id' => $id,
            'text' => $text,
        );

        $adapter->setResponse(new Zend_Http_Response(200, array(), Zend_Json::encode($advice)));

        $this->assertEquals($advice, $service->getRandomByTag($tag));
        $this->assertEquals('http://fucking-great-advice.ru:80/api/random_by_tag/'.urlencode($tag), $service->getHttpClient()->getUri(true));

        $this->setExpectedException('InvalidArgumentException', 'Parameter can not be empty.');
        $service->getRandomByTag('');
    }

    public function testGetLatests()
    {
        $adapter = new Zend_Http_Client_Adapter_Test();
        $service = $this->createTestService($adapter);

        $number = 10;
        $id = '123';
        $text = 'Используй mock блять!';
        $advice = array(
                    'id' => $id,
                    'text' => $text,
        );
        $advices = array_fill(0, $number, $advice);

        $adapter->setResponse(new Zend_Http_Response(200, array(), Zend_Json::encode($advices)));
        $result = $service->getLatests($number);

        $this->assertEquals($advices, $result);
        $this->assertCount($number, $result);
        $this->assertEquals('http://fucking-great-advice.ru:80/api/latest/'.$number, $service->getHttpClient()->getUri(true));
    }

    public function testServiceUnavailable()
    {
        $adapter = new Zend_Http_Client_Adapter_Test();
        $service = $this->createTestService($adapter);

        $adapter->setResponse(new Zend_Http_Response(500, array()));

        $this->setExpectedException('RuntimeException', 'Invalid API response status.');
        $service->getRandom();
    }
}
