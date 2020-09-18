<?php

namespace FryskeOranjekoeke\Core;

/**
 * The Object containing all the information about a Request.
 *
 * @author Sander Tuinstra <sandert2001@hotmail.com>
 */
class RequestObject
{
    /**
     * The Controller & Action that was requested.
     *
     * @var array
     */
    protected $destination = [
        'controller' => null,
        'action'     => null,
        'arguments'  => [
        ]
    ];

    /**
     * The send Headers for the request.
     *
     * @var array
     */
    protected $headers = [
    ];

    /**
     * The method which was used to make the Request.
     *
     * @var string
     */
    protected $method = null;

    /**
     * The ip used to make the Request.
     */
    protected $ip = null;

    /**
     * The POST/PUT data that has been send with this Request.
     *
     * @var stdClass
     */
    protected $data = null;

    /**
     * @param array $serverVars Must contain the following keys:
     *                          array(
     *                              'REQUEST_URI' => '/controller/action',
     *                              'PHP_SELF'    => '/index.php'
     *                          )
     * @param array $routes     All the defined routes.
     */
    public function __construct(array $serverVars, array $routes = [])
    {
        // Determine and set Request Destination.
        $requestedPath = $serverVars['REQUEST_URI'];
        $indexPath     = $serverVars['PHP_SELF'];

        // index.php is the name of the file containing the Front Controller.
        $root = str_replace('index.php', '', $indexPath);
        // If the root is /, then don't perform a str_replace, because that would also remove all the / in the requestedpath
        $controllerAction = (strlen($root) === 1) ? ltrim($requestedPath, '/') : str_replace($root, '', $requestedPath);
        if (empty($controllerAction)) {
            $controllerAction = '/';
        }

        // First try to get the Destination by the Route, then by URL only.
        $destination = $this->getDestinationByRoute($controllerAction, $routes);
        if ($destination === null) {
            $destination = $this->getDestinationByUrl($controllerAction);
        }
        $this->setDestination($destination);

        $headers = [];
        foreach ($serverVars as $header => $value) {
            if (substr($header, 0, 4) !== 'HTTP') {
                continue;
            }

            $headers[$header] = $value;
        }
        $this->setHeaders($headers);

        $this->setMethod($serverVars['REQUEST_METHOD'])
             ->setIp($serverVars['REMOTE_ADDR']);

        // Open in Read Mode.
        $requestDataFile = fopen('php://input', 'r');
        $requestData = '';
        // Read 1024 bytes at the time
        while ($data = fread($requestDataFile, 1024)) {
            $requestData .= $data;
        }
        fclose($requestDataFile);
        if (!empty($requestData)) {
            $this->setData(json_decode($requestData));
        }
    }

    public function getDestination(): array
    {
        return $this->destination;
    }

    public function setDestination(array $data): void
    {
        if (!array_keys_exists(['controller', 'action'], $data)) {
            throw new \InvalidArgumentException('setDestination must contain `controller` and `action` as array keys.');
        }

        $data['controller'] = ucfirst($data['controller']);
        $this->destination = $data;
    }

    public function getHeader(string $header): ?string
    {
        return $this->headers['HTTP_' . strtoupper($header)] ?? null;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;
        return $this;
    }

    public function getData(): \stdClass
    {
        return ($this->data instanceof \stdClass === true) ? $this->data : new \stdClass();
    }

    public function setData(\stdClass $data): void
    {
        $this->data = $data;
    }

    /**
     * Gets the Destination Controller/Action by the Routes.
     *
     * @param string $url    The incoming URL.
     * @param array  $routes All the routes. A route array element should look like this:
     *                         array(
     *                             'controller' => 'string',
     *                             'action'     => 'string'
     *                         )
     *
     * @return array|null Null when no route could be matched against the given URL.
     *                    Array when a route is found by the given URL. The array looks the same like a single route element.
     */
    private function getDestinationByRoute(string $url, array $routes): ?array
    {
        $output = null;
        foreach ($routes as $incomingPath => $route) {
            if ($incomingPath !== $url) {
                continue;
            }

            $output = $route;
        }

        return $output;
    }

    /**
     * Gets the Destination Controller/Action by the URL.
     *
     * @param string $url
     */
    private function getDestinationByUrl(string $url): array
    {
        $output = [
            'controller' => null,
            'action'     => 'index',
            'arguments'  => []
        ];
        if (strpos($url, '/') === false) {
            $output['controller'] = $url;
        } else {
            $destinationSeparated = explode('/', $url);
            $output['controller'] = $destinationSeparated[0];
            if (!empty($destinationSeparated[1])) {
                $output['action'] = $destinationSeparated[1];
            }

            $args = $destinationSeparated;
            // Unset Controller/Action
            unset($args[0], $args[1]);
            $output['arguments'] = $args;
        }
        return $output;
    }
}
