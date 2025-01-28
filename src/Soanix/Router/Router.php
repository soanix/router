<?php

/**
 * @author      Bram(us) Van Damme <bramus@bram.us>, Pedro Soanix Oliver <soanix91@gmail.com>
 * @copyright   Copyright (c), 2013 Bram(us) Van Damme
 * @license     MIT public license
 */

namespace Soanix\Router;

use ReflectionException;
use ReflectionMethod;

/**
 * Class Router.
 */
class Router
{
    /**
     * @var array The route patterns and their handling functions
     */
    protected static array $afterRoutes = [];

    /**
     * @var array The before middleware route patterns and their handling functions
     */
    protected static array $beforeRoutes = [];

    /**
     * @var array [object|callable] The function to be executed when no route has been matched
     */
    protected static array $notFoundCallback = [];

    /**
     * @var string Current base route, used for (sub)route mounting
     */
    protected static string $baseRoute = '';

    /**
     * @var string The Request Method that needs to be handled
     */
    protected static string $requestedMethod = '';

    /**
     * @var ?string The Server Base Path for Router Execution
     */
    protected static ?string $serverBasePath = null;

    /**
     * @var string Default Controllers Namespace
     */
    protected static string $namespace = '';

    public static function clear(): void
    {
        self::$baseRoute = '';
        self::$requestedMethod = '';
        self::$serverBasePath = null;
        self::$afterRoutes = [];
        self::$beforeRoutes = [];
        self::$notFoundCallback = [];
        self::$namespace = '';

    }

    /**
     * Store a before middleware route and a handling function to be executed when accessed using one of the specified methods.
     *
     * @param string $methods Allowed methods, | delimited
     * @param string $pattern A route pattern such as /about/system
     * @param callable|object|string $fn The handling function to be executed
     */
    public static function middleware(string $methods, string $pattern, callable|object|string $fn): void
    {
        $pattern = self::$baseRoute . '/' . trim($pattern, '/');
        $pattern = self::$baseRoute ? rtrim($pattern, '/') : $pattern;

        foreach (explode('|', $methods) as $method) {
            self::$beforeRoutes[$method][] = array(
                'pattern' => $pattern,
                'fn' => $fn
            );
        }
    }

    /**
     * Store a route and a handling function to be executed when accessed using one of the specified methods.
     *
     * @param string $methods Allowed methods, | delimited
     * @param string $pattern A route pattern such as /about/system
     * @param callable|object|string $fn The handling function to be executed
     */
    public static function match(string $methods, string $pattern, callable|object|string $fn, $middleware = null): void
    {
        $pattern = self::$baseRoute . '/' . trim($pattern, '/');
        $pattern = self::$baseRoute ? rtrim($pattern, '/') : $pattern;

        foreach (explode('|', $methods) as $method) {
            self::$afterRoutes[$method][] = array(
                'pattern' => $pattern,
                'fn' => $fn,
                'middleware' => $middleware
            );
        }
    }

    /**
     * Shorthand for a route accessed using any method.
     *
     * @param string $pattern A route pattern such as /about/system
     * @param callable|object|string $fn The handling function to be executed
     */
    public static function all(string $pattern, callable|object|string $fn, $middleware = null): void
    {
        self::match('GET|POST|PUT|DELETE|OPTIONS|PATCH|HEAD', $pattern, $fn, $middleware);
    }

    /**
     * Shorthand for a route accessed using GET.
     *
     * @param string $pattern A route pattern such as /about/system
     * @param callable|object|string $fn The handling function to be executed
     */
    public static function get(string $pattern, callable|object|string $fn, $middleware = null): void
    {
        self::match('GET', $pattern, $fn, $middleware);
    }

    /**
     * Shorthand for a route accessed using POST.
     *
     * @param string $pattern A route pattern such as /about/system
     * @param callable|object|string $fn The handling function to be executed
     */
    public static function post(string $pattern, callable|object|string $fn, $middleware = null): void
    {
        self::match('POST', $pattern, $fn, $middleware);
    }

    /**
     * Shorthand for a route accessed using PATCH.
     *
     * @param string $pattern A route pattern such as /about/system
     * @param callable|object|string $fn The handling function to be executed
     */
    public static function patch(string $pattern, callable|object|string $fn, $middleware = null): void
    {
        self::match('PATCH', $pattern, $fn, $middleware);
    }

    /**
     * Shorthand for a route accessed using DELETE.
     *
     * @param string $pattern A route pattern such as /about/system
     * @param callable|object|string $fn The handling function to be executed
     */
    public static function delete(string $pattern, callable|object|string $fn, $middleware = null): void
    {
        self::match('DELETE', $pattern, $fn, $middleware);
    }

    /**
     * Shorthand for a route accessed using PUT.
     *
     * @param string $pattern A route pattern such as /about/system
     * @param callable|object|string $fn The handling function to be executed
     */
    public static function put(string $pattern, callable|object|string $fn, $middleware = null): void
    {
        self::match('PUT', $pattern, $fn, $middleware);
    }

    /**
     * Shorthand for a route accessed using OPTIONS.
     *
     * @param string $pattern A route pattern such as /about/system
     * @param callable|object|string $fn The handling function to be executed
     */
    public static function options(string $pattern, callable|object|string $fn, $middleware = null): void
    {
        self::match('OPTIONS', $pattern, $fn, $middleware);
    }

    /**
     * Mounts a collection of callbacks onto a base route.
     *
     * @param string $baseRoute The route sub pattern to mount the callbacks on
     * @param callable $fn The callback method
     */
    public static function mount(string $baseRoute, callable $fn): void
    {
        // Track current base route
        $curBaseRoute = self::$baseRoute;

        // Build new base route string
        self::$baseRoute .= $baseRoute;


        // Call the callable
        call_user_func($fn);

        // Restore original base route
        self::$baseRoute = $curBaseRoute;
    }

    /**
     * Get all request headers.
     *
     * @return false|array The request headers
     */
    public static function getRequestHeaders(): false|array
    {
        $headers = [];

        // If getallheaders() is available, use that
        if (function_exists('getallheaders')) {
            $headers = getallheaders();

            // getallheaders() can return false if something went wrong
            if ($headers !== false) {
                return $headers;
            }
        }

        // Method getallheaders() not available or went wrong: manually extract 'm
        foreach ($_SERVER as $name => $value) {
            if ((str_starts_with($name, 'HTTP_')) || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
                $headers[str_replace(array(' ', 'Http'), array('-', 'HTTP'), ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }

        return $headers;
    }

    /**
     * Get the request method used, taking overrides into account.
     *
     * @return string The Request method to handle
     */
    public static function getRequestMethod(): string
    {
        // Take the method as found in $_SERVER
        $method = $_SERVER['REQUEST_METHOD'];

        // If it's a HEAD request override it to being GET and prevent any output, as per HTTP Specification
        // @url http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.4
        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_start();
            $method = 'GET';
        } // If it's a POST request, check for a method override header
        elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $headers = self::getRequestHeaders();
            if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], array('PUT', 'DELETE', 'PATCH'))) {
                $method = $headers['X-HTTP-Method-Override'];
            }
        }

        return $method;
    }

    /**
     * Set a Default Lookup Namespace for Callable methods.
     *
     * @param string $namespace A given namespace
     */
    public static function setNamespace(string $namespace): void
    {
        self::$namespace = $namespace;
    }

    /**
     * Get the given Namespace before.
     *
     * @return string The given Namespace if exists
     */
    public static function getNamespace(): string
    {
        return self::$namespace;
    }

    /**
     * Execute the router: Loop all defined before middleware's and routes, and execute the handling function if a match was found.
     *
     * @param callable|object|null $callback Function to be executed after a matching route was handled (= after router middleware)
     *
     * @return bool
     * @throws RouterException
     */
    public static function run(callable|object|null $callback = null): bool
    {
        // Define which method we need to handle
        self::$requestedMethod = self::getRequestMethod();

        // Handle all before middlewares
        if (isset(self::$beforeRoutes[self::$requestedMethod])) {
            self::handle(self::$beforeRoutes[self::$requestedMethod]);
        }

        // Handle all routes
        $numHandled = 0;
        if (isset(self::$afterRoutes[self::$requestedMethod])) {
            $numHandled = self::handle(self::$afterRoutes[self::$requestedMethod], true);
        }

        // If no route was handled, trigger the 404 (if any)
        if ($numHandled === 0) {
            self::trigger404(self::$afterRoutes[self::$requestedMethod] ?? []);
        } // If a route was handled, perform the finish callback (if any)
        else {
            if ($callback && is_callable($callback)) {
                $callback();
            }
        }

        // If it originally was a HEAD request, clean up after ourselves by emptying the output buffer
        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_end_clean();
        }

        // Return true if a route was handled, false otherwise
        return $numHandled !== 0;
    }

    /**
     * Set the 404 handling function.
     *
     * @param callable|object|string $match_fn The function to be executed
     * @param callable|object|string|null $fn The function to be executed
     */
    public static function set404(callable|object|string $match_fn, callable|object|string|null $fn = null): void
    {
        if (!is_null($fn)) {
            self::$notFoundCallback[$match_fn] = $fn;
        } else {
            self::$notFoundCallback['/'] = $match_fn;
        }
    }

    /**
     * Triggers 404 response
     *
     * @throws RouterException
     */
    public static function trigger404(): void
    {

        // Counter to keep track of the number of routes we've handled
        $numHandled = 0;

        // handle 404 pattern
        if (count(self::$notFoundCallback) > 0) {
            // loop fallback-routes
            foreach (self::$notFoundCallback as $route_pattern => $route_callable) {

                // matches result
                $matches = [];

                // check if there is a match and get matches as $matches (pointer)
                $is_match = self::patternMatches($route_pattern, self::getCurrentUri(), $matches);

                // is fallback route match?
                if ($is_match) {

                    // Rework matches to only contain the matches, not the orig string
                    list($matches, $params) = self::extractOnlyMatches($matches);

                    self::invoke($route_callable);

                    ++$numHandled;
                }
            }
            if ($numHandled == 0 and self::$notFoundCallback['/']) {
                self::invoke(self::$notFoundCallback['/']);
            } elseif ($numHandled == 0) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            }
        }
    }

    /**
     * Replace all curly braces matches {} into word patterns (like Laravel)
     * Checks if there is a routing match
     *
     * @param $pattern
     * @param $uri
     * @param $matches
     *
     * @return bool -> is match yes/no
     */
    private static function patternMatches($pattern, $uri, &$matches): bool
    {
        // Replace all curly braces matches {} into word patterns (like Laravel)
        $pattern = preg_replace('/\/{(.*?)}/', '/(.*?)', $pattern);

        // we may have a match!
        return boolval(preg_match_all('#^' . $pattern . '$#', $uri, $matches, PREG_OFFSET_CAPTURE));
    }

    /**
     * Handle a set of routes: if a match is found, execute the relating handling function.
     *
     * @param array $routes Collection of route patterns and their handling functions
     * @param bool $quitAfterRun Does the handle function need to quit after one route was matched?
     *
     * @return int The number of routes handled
     * @throws RouterException
     */
    private static function handle(array $routes, bool $quitAfterRun = false): int
    {
        // Counter to keep track of the number of routes we've handled
        $numHandled = 0;

        // The current page URL
        $uri = self::getCurrentUri();

        // Loop all routes
        foreach ($routes as $route) {

            // get routing matches
            $is_match = self::patternMatches($route['pattern'], $uri, $matches);

            // is there a valid match?
            if ($is_match) {

                // Rework matches to only contain the matches, not the orig string
                list($matches, $params) = self::extractOnlyMatches($matches);

                $middlewares = [];

                if (!empty($route['middleware']))
                    if (!is_array($route['middleware']))
                        $middlewares[] = $route['middleware'];
                    else
                        $middlewares = $route['middleware'];

                foreach ($middlewares as $middleware)
                    self::invoke($middleware, $params);


                // Call the handling function with the URL parameters if the desired input is callable
                self::invoke($route['fn'], $params);

                ++$numHandled;

                // If we need to quit, then quit
                if ($quitAfterRun) {
                    break;
                }
            }
        }

        // Return the number of routes handled
        return $numHandled;
    }


    /**
     * @param $fn
     * @param array $params
     * @return void
     * @throws RouterException
     */
    private static function invoke($fn, array $params = []): void
    {
        if (is_callable($fn)) {
            call_user_func_array($fn, $params);
        } // If not, check the existence of special parameters
        elseif (stripos($fn, '@') !== false) {
            // Explode segments of given route
            list($controller, $method) = explode('@', $fn);

            $extraParams = explode(':', $method);

            $method = $extraParams[0];

            array_shift($extraParams);

            // Adjust controller class if namespace has been set
            if (self::getNamespace() !== '') {
                $controller = self::getNamespace() . '\\' . $controller;
            }

            try {
                $reflectedMethod = new ReflectionMethod($controller, $method);
                // Make sure it's callable
                if ($reflectedMethod->isPublic() && (!$reflectedMethod->isAbstract())) {
                    if ($reflectedMethod->isStatic()) {
                        forward_static_call_array(array($controller, $method), array_merge($params, $extraParams));
                    } else {
                        // Make sure we have an instance, because a non-static method must not be called statically
                        if (is_string($controller)) {
                            $controller = new $controller();
                        }
                        call_user_func_array(array($controller, $method), $params);
                    }
                }
            } catch (ReflectionException $reflectionException) {
                throw new RouterException($reflectionException->getMessage(), 404);
                // The controller class is not available or the class does not have the method $method
            }
        }
    }

    /**
     * Define the current relative URI.
     *
     * @return string
     */
    public static function getCurrentUri(): string
    {
        // Get the current Request URI and remove rewrite base path from it (= allows one to run the router in a sub folder)
        $uri = substr(rawurldecode($_SERVER['REQUEST_URI']), strlen(self::getBasePath()));

        // Don't take query params into account on the URL
        if (str_contains($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        // Remove trailing slash + enforce a slash at the start
        return '/' . trim($uri, '/');
    }

    /**
     * Return server base Path, and define it if isn't defined.
     *
     * @return string|null
     */
    public static function getBasePath(): ?string
    {
        // Check if server base path is defined, if not define it.
        if (self::$serverBasePath === null) {
            self::$serverBasePath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        }

        return self::$serverBasePath;
    }

    /**
     * Explicilty sets the server base path. To be used when your entry script path differs from your entry URLs.
     * @see https://github.com/bramus/router/issues/82#issuecomment-466956078
     *
     * @param string $serverBasePath
     */
    public static function setBasePath(string $serverBasePath): void
    {
        self::$serverBasePath = $serverBasePath;
    }

    /**
     * @param mixed $matches
     * @return array
     */
    private static function extractOnlyMatches(mixed $matches): array
    {
        $matches = array_slice($matches, 1);

        // Extract the matched URL parameters (and only the parameters)
        $params = array_map(function ($match, $index) use ($matches) {

            // We have a following parameter: take the substring from the current param position until the next one's position (thank you PREG_OFFSET_CAPTURE)
            if (isset($matches[$index + 1][0]) && is_array($matches[$index + 1][0])) {
                if ($matches[$index + 1][0][1] > -1) {
                    return trim(substr($match[0][0], 0, $matches[$index + 1][0][1] - $match[0][1]), '/');
                }
            }
            // We have no following parameters: return the lot

            return isset($match[0][0]) && $match[0][1] != -1 ? trim($match[0][0], '/') : null;
        }, $matches, array_keys($matches));
        return array($matches, $params);
    }
}
