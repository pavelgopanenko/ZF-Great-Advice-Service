<?php

/**
 * Client service for fucking-great-advice.ru API.
 *
 * Service gives fucking advice to those in need.
 *
 * @category   Ulrika
 * @package    Ulrika_Service
 * @subpackage Ulrika_Service_FuckingGreatAdvice
 *
 * @author     Pavel Gopanenko <pavelgopanenko@gmail.com>
 * @license    MIT License
 * @version    Release: @package_version@
 * @link http://fucking-great-advice.ru/api/
 */
class Ulrika_Service_FuckingGreatAdvice extends Zend_Service_Abstract
{
    const API_URL = 'http://fucking-great-advice.ru:80/api';

    const SOUND_URL = 'http://fucking-great-advice.ru:80/files/sounds';

    /**
     * Clean 'text' field in advice.
     *
     * @param array $advice
     * @return array
     */
    public static function cleanTextField(array $advice)
    {
        $advice['text'] = html_entity_decode($advice['text'], ENT_COMPAT, 'UTF-8');

        return $advice;
    }

    /**
     * Returns a sound file URL if it exists.
     *
     * @param array $advice Advice array
     * @return string|null
     */
    public static function getSoundUrl(array $advice)
    {
        if (self::hasSoundAvailable($advice)) {
            return self::SOUND_URL . '/' . $advice['sound'];
        }

        return null;
    }

    /**
     * Checks the availability of a sound file.
     *
     * @param array $advice
     * @return bool
     */
    public static function hasSoundAvailable(array $advice)
    {
        return key_exists('sound', $advice) && $advice['sound'];
    }

    /**
     * Request to the server.
     *
     * @param string $path Requested path
     * @return array API response
     */
    protected function _requestUri($path)
    {
        $response = self::getHttpClient()->resetParameters(true)
                                         ->setUri(self::API_URL . '/' . $path)
                                         ->request(Zend_Http_Client::GET);

        if ($response->getStatus() != 200) {
            throw new RuntimeException("Invalid API response status.");
        }

        return Zend_Json::decode($response->getBody());
    }

    /**
     * Random advice.
     *
     * @param bool $censored Censorship version flag
     * @return array
     */
    public function getRandom($censored = false)
    {
        return self::cleanTextField($this->_requestUri($censored ? 'random/censored/' : 'random'));
    }

    /**
     * Random advice by tag.
     *
     * @param string $tag
     * @return array
     */
    public function getRandomByTag($tag)
    {
        if (!$tag) {
            throw new InvalidArgumentException('Parameter can not be empty.');
        }

        return self::cleanTextField($this->_requestUri('random_by_tag/' . urlencode($tag)));
    }

    /**
     * Last few advices.
     *
     * @param int $count Number of advices.
     * @return array
     */
    public function getLatests($count = 1)
    {
        $count = 1 > $count ? 1 : (int) $count;

        return array_map(
            array('Ulrika_Service_FuckingGreatAdvice', 'cleanTextField'),
            $this->_requestUri('latest/' . $count)
        );
    }
}
