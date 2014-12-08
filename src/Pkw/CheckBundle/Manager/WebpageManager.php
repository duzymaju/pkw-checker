<?php
namespace Pkw\CheckBundle\Manager;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\InvalidParameterException;

/**
 * Manager
 */
class WebpageManager
{
    /** @var string */
    protected $url;

    /** @var string */
    protected $host;

    /** @var string */
    protected $path;

    /** @var string */
    protected $fileName;

    /** @var string */
    protected $content;

    /** @var integer */
    protected $redirectCount;

    /**
     * Constructor
     *
     * @param string    $url    URL
     * @param self|null $parent parent
     *
     * @throws InvalidParameterException
     * @throws ResourceNotFoundException
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function __construct($url, self $parent = null)
    {
        if (empty($url)) {
            throw new InvalidParameterException('URL should be defined');
        }
        $this->setUrl($url, $parent);

        $options = array(
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 30,
        );
        $response = array();

        $curl = curl_init($this->url);
        curl_setopt_array($curl, $options);
        $response['content'] = curl_exec($curl);
        $response['effectiveUrl'] = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
        $response['errorMessage'] = curl_error($curl);
        $response['errorNumber'] = curl_errno($curl);
        $response['httpCode'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $response['redirectCount'] = curl_getinfo($curl, CURLINFO_REDIRECT_COUNT);
        curl_close($curl);

        if ($url != $response['effectiveUrl']) {
            $this->setUrl($response['effectiveUrl'], $parent);
        }

        if (($response['httpCode'] == 200) && ($response['errorNumber'] == 0)) {
            $this->content = $response['content'];
            $this->redirectCount = $response['redirectCount'];
        } else {
            throw new ResourceNotFoundException(sprintf(
                "Can't fetch response from URL: %s, httpCode: %d, errorNumber: %d, errorMessage: %s",
                $this->url, $response['httpCode'], $response['errorNumber'], $response['errorMessage']));
        }
    }

    /**
     * Get elements
     *
     * @param string $selector selector
     *
     * @return Crawler
     */
    public function getElements($selector)
    {
        $document = new Crawler($this->getContent());
        $elements = $document->filter($selector);

        return $elements;
    }

    /**
     * Get host
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get file name
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Get content
     *
     * @return string
     */
    protected function getContent()
    {
        return $this->content;
    }

    /**
     * Get redirect count
     *
     * @return integer
     */
    public function getRedirectCount()
    {
        return $this->redirectCount;
    }

    /**
     * Set URL
     *
     * @param string    $url    URL
     * @param self|null $parent parent
     *
     * @return $this
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function setUrl($url, self $parent = null)
    {
        if (preg_match('#^https?://#', $url) || !isset($parent)) {
            $this->url = $url;
        } elseif (strpos($url, '/') === 0) {
            $this->url = $parent->getHost() . $url;
        } else {
            $this->url = $parent->getHost() . $parent->getPath() . '/' . $url;
        }

        $parsedUrl = parse_url($this->url);
        $this->host = (isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '') .
            (isset($parsedUrl['host']) ? $parsedUrl['host'] : '') .
            (isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '');
        $path = explode('/', isset($parsedUrl['path']) ? ltrim($parsedUrl['path'], '/') : '');
        $this->fileName = array_pop($path);
        $this->path = (count($path) > 0 ? '/' : '') . implode('/', $path);

        return $this;
    }
}
