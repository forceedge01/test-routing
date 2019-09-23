<?php

namespace Genesis\TestRouting;

use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkAwareContext;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Exception;


/**
 * RoutingContext class.
 */
class RoutingContext implements MinkAwareContext
{
    private $router;

    private $config;

    public function __construct($routesFilePath, $windowSizeRes = null, $windowSizeDevice = null)
    {
        if (! file_exists($routesFilePath)) {
            throw new Exception('File not found at path: '. $routesFilePath);
        }

        $this->config['windowSizeDevice'] = $windowSizeDevice;
        $this->config['windowSizeRes'] = $windowSizeRes;
        $this->router = new Routing();
        $this->router::addRoutes(include $routesFilePath);
    }

    /**
     * @param Mink $mink
     */
    public function setMink(Mink $mink)
    {
        $this->mink = $mink;

        if ($this->config['windowSizeRes']) {
            list($width, $height) = explode('x', $this->config['windowSizeRes']);
            $this->getSession()->getDriver()->resizeWindow($width, $height);
        }
    }

    /**
     * @param array $params
     */
    public function setMinkParameters(array $params = [])
    {
        $this->minkParams = $params;
    }

    /**
     * @Given I am on the ":page" page
     * @Given I am on the ":page" page with the following param(s):
     * @Given I visit the ":page" page
     * @Given I visit the ":page" page on :device
     * @Given I visit the ":page" page on :device with the following params:
     *
     * @param string   $page
     * @param mixed    $device
     * @param callable $callable The callable should be passed in from feature context to resolve the
     *                           url correctly if you have placeholders in them.
     *
     * @return void
     */
    public function visit($page, $device = null, TableNode $params = null)
    {
        if (isset($this->config['windowSizeDevice'])) {
            $device = $this->config['windowSizeDevice'];
        }

        switch($device) {
            case 'desktop':
                $this->desktop();
                break;
            case 'mobile':
                $this->mobile();
                break;
            case 'tablet':
                $this->tablet();
                break;
            case null:
                break;
            default:
                throw new Exception('Invalid device specified.');
        }

        $queryString = '';
        if ($params) {
            $queryParams = $params->getRowsHash();
            $queryString = '?' . http_build_query($queryParams);
        }

        $route = $this->router::getRoute($page);
        $url = $this->getCallable()($route . $queryString);

        $this->getSession()->visit($this->locatePath($url));
    }

    /**
     * Use when testing static content available on different pages such as header and footer.
     *
     * @Given I am on any page
     * @Given I am on any page on :device
     * @param null|mixed $device
     */
    public function iAmOnAnyPage($device = null)
    {
        $allRoutes = $this->router::getRoutes();
        $this->visit(array_rand($allRoutes, 1), $device);
    }

    /**
     * @Then I should be on the :arg1 page
     * @param mixed $arg1
     */
    public function iShouldBeOnThePage($arg1)
    {
        $url = $this->router::getRoute($arg1, $this->getCallable());
        $session = $this->getSession();

        $this->spin(function() use ($url, $session) {
            $currentUrl = parse_url($session->getCurrentUrl());
            if ($url !== $currentUrl['path']) {
                throw new \Exception(sprintf('Expected to be on page %s, but on %s.', $url, $currentUrl['path']));
            }

            return true;
        }, 3);
    }

    /**
     * @overridable Override this method to replace placeholders if you have them in your query strings.
     *
     * @return callable|null
     */
    protected function getCallable()
    {
        return null;
    }

    /**
     * @return Session
     */
    protected function mobile(): Session
    {
        $this->getSession()->getDriver()->resizeWindow(568, 1024);

        return $this->getSession();
    }

    /**
     * @return Session
     */
    protected function tablet(): Session
    {
        $this->getSession()->getDriver()->resizeWindow(780, 1024);

        return $this->getSession();
    }

    /**
     * @return Session
     */
    protected function desktop(): Session
    {
        $this->getSession()->getDriver()->resizeWindow(1280, 1024);

        return $this->getSession();
    }

    /**
     * @param callable $lambda Must return true or throw an exception.
     * @param integer  $wait
     *
     * @return void
     */
    protected function spin(callable $lambda, $wait = 5)
    {
        $lastErrorMessage = '';

        for ($i = 0; $i < $wait; $i++) {
            try {
                if ($lambda($this)) {
                    return true;
                }
            } catch (\Exception $e) {
                // do nothing
                $lastErrorMessage = $e->getMessage();
            }

            sleep(1);
        }

        throw new Exception($lastErrorMessage);
    }

    /**
     * Locates url, based on provided path.
     * Override to provide custom routing mechanism.
     *
     * @param string $path
     *
     * @return string
     */
    public function locatePath(string $path): string
    {
        $startUrl = rtrim($this->minkParams['base_url'], '/') . '/';

        return 0 !== strpos($path, 'http') ? $startUrl . ltrim($path, '/') : $path;
    }

    /**
     * @return Session
     */
    protected function getSession(): Session
    {
        return $this->mink->getSession('javascript');
    }
}
