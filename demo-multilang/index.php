<?php

// In case one is using PHP 5.4+'s built-in server
$filename = __DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

// Include the Router class
// @note: it's recommended to just use the composer autoloader when working with other packages too
require_once __DIR__ . '/../src/Soanix/Router/Router.php';

/**
 * A Multilingual Router
 */
class MultilangRouter extends \src\Router\Router
{
    /**
     * The Default langauge
     * @var string
     */
    private static string $defaultLanguage;

    /**
     * List of allowed languages
     * @var array
     */
    private static array $allowedLanguages = array();

    /**
     * A Multilingual Router
     * @param array $allowedLanguages
     * @param string $defaultLanguage
     */
    public static function create(array $allowedLanguages, string $defaultLanguage)
    {

        // Store passed in data
        self::$allowedLanguages = $allowedLanguages;
        self::$defaultLanguage = (in_array($defaultLanguage, $allowedLanguages) ? $defaultLanguage : $allowedLanguages[0]);

        // Visiting the root? Redirect to the default language index
        self::match('GET|POST|PUT|DELETE|HEAD', '/', function () {
            header('location: /' . self::$defaultLanguage);
            exit();
        });

        // Create a before handler to make sure the language checks out when visiting anything but the root.
        // If the language doesn't check out, redirect to the default language index
        self::middleware('GET|POST|PUT|DELETE|HEAD', '/([a-z0-9_-]+)(/.*)?', function ($language, $slug = null) {

            // The given language does not appear in the array of allowed languages
            if (!in_array($language, self::$allowedLanguages)) {
                header('location: /' . self::$defaultLanguage);
                exit();
            }
        });
    }
}

// Create a Router
MultilangRouter::create(
    array('en', 'nl', 'fr'), //= allowed languages
    'nl' // = default language
);

MultilangRouter::get('/([a-z0-9_-]+)', function ($language) {
    exit('This is the ' . htmlentities($language) . ' index');
});

MultilangRouter::get('/([a-z0-9_-]+)/([a-z0-9_-]+)', function ($language, $slug) {
    exit('This is the ' . htmlentities($language) . ' version of ' . htmlentities($slug));
});

MultilangRouter::get('/([a-z0-9_-]+)/(.*)', function ($language, $slug) {
    exit('This is the ' . htmlentities($language) . ' version of ' . htmlentities($slug) . ' (multiple segments allowed)');
});

// Thunderbirds are go!
MultilangRouter::run();

// EOF
