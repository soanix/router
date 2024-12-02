<?php

namespace {

    class Handler
    {
        public function notfound()
        {
            // Clear all routes
            \src\Router\Router::clear();

            echo 'route not found';
        }
    }

    class RouterTest extends PHPUnit_Framework_TestCase
    {
        protected function setUp()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Clear SCRIPT_NAME because bramus/router tries to guess the subfolder the script is run in
            $_SERVER['SCRIPT_NAME'] = '/index.php';

            // Default request method to GET
            $_SERVER['REQUEST_METHOD'] = 'GET';

            // Default SERVER_PROTOCOL method to HTTP/1.1
            $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        }

        protected function tearDown()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // nothing
        }

        public function testInit()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // $this->assertInstanceOf('\src\Router\Router', new Router());
        }

        public function testUri()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router
            \src\Router\Router::match('GET', '/about', function () {
                echo 'about';
            });

            // Fake some data
            $_SERVER['SCRIPT_NAME'] = '/sub/folder/index.php';
            $_SERVER['REQUEST_URI'] = '/sub/folder/about/whatever';


            $this->assertEquals(
                '/about/whatever',
                \src\Router\Router::getCurrentUri()

            );
        }

        public function testBasePathOverride()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::match('GET', '/about', function () {
                echo 'about';
            });

            // Fake some data
            $_SERVER['SCRIPT_NAME'] = '/public/index.php';
            $_SERVER['REQUEST_URI'] = '/about';

            \src\Router\Router::setBasePath('/');

            $this->assertEquals(
                '/',
                \src\Router\Router::getBasePath()
            );

            // Test the /about route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/about';
            \src\Router\Router::run();
            $this->assertEquals('about', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testBasePathThatContainsEmoji()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::match('GET', '/about', function () {
                echo 'about';
            });

            // Fake some data
            $_SERVER['SCRIPT_NAME'] = '/sub/folder/💩/index.php';
            $_SERVER['REQUEST_URI'] = '/sub/folder/%F0%9F%92%A9/about';

            // Test the /hello/bramus route
            ob_start();
            \src\Router\Router::run();
            $this->assertEquals('about', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testStaticRoute()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::match('GET', '/about', function () {
                echo 'about';
            });

            // Test the /about route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/about';
            \src\Router\Router::run();
            $this->assertEquals('about', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testStaticRouteUsingShorthand()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('/about', function () {
                echo 'about';
            });

            // Test the /about route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/about';
            \src\Router\Router::run();
            $this->assertEquals('about', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testRequestMethods()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('/', function () {
                echo 'get';
            });
            \src\Router\Router::post('/', function () {
                echo 'post';
            });
            \src\Router\Router::put('/', function () {
                echo 'put';
            });
            \src\Router\Router::patch('/', function () {
                echo 'patch';
            });
            \src\Router\Router::delete('/', function () {
                echo 'delete';
            });
            \src\Router\Router::options('/', function () {
                echo 'options';
            });

            // Test GET
            ob_start();
            $_SERVER['REQUEST_URI'] = '/';
            \src\Router\Router::run();
            $this->assertEquals('get', ob_get_contents());

            // Test POST
            ob_clean();
            $_SERVER['REQUEST_METHOD'] = 'POST';
            \src\Router\Router::run();
            $this->assertEquals('post', ob_get_contents());

            // Test PUT
            ob_clean();
            $_SERVER['REQUEST_METHOD'] = 'PUT';
            \src\Router\Router::run();
            $this->assertEquals('put', ob_get_contents());

            // Test PATCH
            ob_clean();
            $_SERVER['REQUEST_METHOD'] = 'PATCH';
            \src\Router\Router::run();
            $this->assertEquals('patch', ob_get_contents());

            // Test DELETE
            ob_clean();
            $_SERVER['REQUEST_METHOD'] = 'DELETE';
            \src\Router\Router::run();
            $this->assertEquals('delete', ob_get_contents());

            // Test OPTIONS
            ob_clean();
            $_SERVER['REQUEST_METHOD'] = 'OPTIONS';
            \src\Router\Router::run();
            $this->assertEquals('options', ob_get_contents());

            // Test HEAD
            ob_clean();
            $_SERVER['REQUEST_METHOD'] = 'HEAD';
            \src\Router\Router::run();
            $this->assertEquals('', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testShorthandAll()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::all('/', function () {
                echo 'all';
            });

            $_SERVER['REQUEST_URI'] = '/';

            // Test GET
            ob_start();
            $_SERVER['REQUEST_METHOD'] = 'GET';
            \src\Router\Router::run();
            $this->assertEquals('all', ob_get_contents());

            // Test POST
            ob_clean();
            $_SERVER['REQUEST_METHOD'] = 'POST';
            \src\Router\Router::run();
            $this->assertEquals('all', ob_get_contents());

            // Test PUT
            ob_clean();
            $_SERVER['REQUEST_METHOD'] = 'PUT';
            \src\Router\Router::run();
            $this->assertEquals('all', ob_get_contents());

            // Test DELETE
            ob_clean();
            $_SERVER['REQUEST_METHOD'] = 'DELETE';
            \src\Router\Router::run();
            $this->assertEquals('all', ob_get_contents());

            // Test OPTIONS
            ob_clean();
            $_SERVER['REQUEST_METHOD'] = 'OPTIONS';
            \src\Router\Router::run();
            $this->assertEquals('all', ob_get_contents());

            // Test PATCH
            ob_clean();
            $_SERVER['REQUEST_METHOD'] = 'PATCH';
            \src\Router\Router::run();
            $this->assertEquals('all', ob_get_contents());

            // Test HEAD
            ob_clean();
            $_SERVER['REQUEST_METHOD'] = 'HEAD';
            \src\Router\Router::run();
            $this->assertEquals('', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testDynamicRoute()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('/hello/(\w+)', function ($name) {
                echo 'Hello ' . $name;
            });

            // Test the /hello/bramus route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/hello/bramus';
            \src\Router\Router::run();
            $this->assertEquals('Hello bramus', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testDynamicRouteWithMultiple()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('/hello/(\w+)/(\w+)', function ($name, $lastname) {
                echo 'Hello ' . $name . ' ' . $lastname;
            });

            // Test the /hello/bramus route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/hello/bramus/sumarb';
            \src\Router\Router::run();
            $this->assertEquals('Hello bramus sumarb', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testCurlyBracesRoutes()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('/hello/{name}/{lastname}', function ($name, $lastname) {
                echo 'Hello ' . $name . ' ' . $lastname;
            });

            // Test the /hello/bramus route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/hello/bramus/sumarb';
            \src\Router\Router::run();
            $this->assertEquals('Hello bramus sumarb', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testCurlyBracesRoutesWithNonAZCharsInPlaceholderNames()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('/hello/{arg1}/{arg2}', function ($arg1, $arg2) {
                echo 'Hello ' . $arg1 . ' ' . $arg2;
            });

            // Test the /hello/bramus route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/hello/bramus/sumarb';
            \src\Router\Router::run();
            $this->assertEquals('Hello bramus sumarb', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testCurlyBracesRoutesWithCyrillicCharactersInPlaceholderNames()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('/hello/{това}/{това}', function ($arg1, $arg2) {
                echo 'Hello ' . $arg1 . ' ' . $arg2;
            });

            // Test the /hello/bramus route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/hello/bramus/sumarb';
            \src\Router\Router::run();
            $this->assertEquals('Hello bramus sumarb', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testCurlyBracesRoutesWithEmojiInPlaceholderNames()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('/hello/{😂}/{😅}', function ($arg1, $arg2) {
                echo 'Hello ' . $arg1 . ' ' . $arg2;
            });

            // Test the /hello/bramus route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/hello/bramus/sumarb';
            \src\Router\Router::run();
            $this->assertEquals('Hello bramus sumarb', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testCurlyBracesWithCyrillicCharacters()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('/bg/{arg}', function ($arg) {
                echo 'BG: ' . $arg;
            });

            // Test the /hello/bramus route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/bg/това';
            \src\Router\Router::run();
            $this->assertEquals('BG: това', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testCurlyBracesWithMultipleCyrillicCharacters()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('/bg/{arg}/{arg}', function ($arg1, $arg2) {
                echo 'BG: ' . $arg1 . ' - ' . $arg2;
            });

            // Test the /hello/bramus route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/bg/това/слъг';
            \src\Router\Router::run();
            $this->assertEquals('BG: това - слъг', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testCurlyBracesWithEmoji()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('/emoji/{emoji}', function ($emoji) {
                echo 'Emoji: ' . $emoji;
            });

            // Test the /hello/bramus route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/emoji/%F0%9F%92%A9'; // 💩
            \src\Router\Router::run();
            $this->assertEquals('Emoji: 💩', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testCurlyBracesWithEmojiCombinedWithBasePathThatContainsEmoji()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('/emoji/{emoji}', function ($emoji) {
                echo 'Emoji: ' . $emoji;
            });

            // Fake some data
            $_SERVER['SCRIPT_NAME'] = '/sub/folder/💩/index.php';
            $_SERVER['REQUEST_URI'] = '/sub/folder/%F0%9F%92%A9/emoji/%F0%9F%A4%AF'; // 🤯

            // Test the /hello/bramus route
            ob_start();
            \src\Router\Router::run();
            $this->assertEquals('Emoji: 🤯', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testDynamicRouteWithOptionalSubpatterns()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('/hello(/\w+)?', function ($name = null) {
                echo 'Hello ' . (($name) ? $name : 'stranger');
            });

            // Test the /hello route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/hello';
            \src\Router\Router::run();
            $this->assertEquals('Hello stranger', ob_get_contents());

            // Test the /hello/bramus route
            ob_clean();
            $_SERVER['REQUEST_URI'] = '/hello/bramus';
            \src\Router\Router::run();
            $this->assertEquals('Hello bramus', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testDynamicRouteWithMultipleSubpatterns()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('/(.*)/page([0-9]+)', function ($place, $page) {
                echo 'Hello ' . $place . ' page : ' . $page;
            });

            // Test the /hello/bramus/page3 route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/hello/bramus/page3';
            \src\Router\Router::run();
            $this->assertEquals('Hello hello/bramus page : 3', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testDynamicRouteWithOptionalNestedSubpatterns()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('/blog(/\d{4}(/\d{2}(/\d{2}(/[a-z0-9_-]+)?)?)?)?', function ($year = null, $month = null, $day = null, $slug = null) {
                if ($year === null) {
                    echo 'Blog overview';

                    return;
                }
                if ($month === null) {
                    echo 'Blog year overview (' . $year . ')';

                    return;
                }
                if ($day === null) {
                    echo 'Blog month overview (' . $year . '-' . $month . ')';

                    return;
                }
                if ($slug === null) {
                    echo 'Blog day overview (' . $year . '-' . $month . '-' . $day . ')';

                    return;
                }
                echo 'Blogpost ' . htmlentities($slug) . ' detail (' . $year . '-' . $month . '-' . $day . ')';
            });

            // Test the /blog route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/blog';
            \src\Router\Router::run();
            $this->assertEquals('Blog overview', ob_get_contents());

            // Test the /blog/year route
            ob_clean();
            $_SERVER['REQUEST_URI'] = '/blog/1983';
            \src\Router\Router::run();
            $this->assertEquals('Blog year overview (1983)', ob_get_contents());

            // Test the /blog/year/month route
            ob_clean();
            $_SERVER['REQUEST_URI'] = '/blog/1983/12';
            \src\Router\Router::run();
            $this->assertEquals('Blog month overview (1983-12)', ob_get_contents());

            // Test the /blog/year/month/day route
            ob_clean();
            $_SERVER['REQUEST_URI'] = '/blog/1983/12/26';
            \src\Router\Router::run();
            $this->assertEquals('Blog day overview (1983-12-26)', ob_get_contents());

            // Test the /blog/year/month/day/slug route
            ob_clean();
            $_SERVER['REQUEST_URI'] = '/blog/1983/12/26/bramus';
            \src\Router\Router::run();
            $this->assertEquals('Blogpost bramus detail (1983-12-26)', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testDynamicRouteWithNestedOptionalSubpatterns()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('/hello(/\w+(/\w+)?)?', function ($name1 = null, $name2 = null) {
                echo 'Hello ' . (($name1) ? $name1 : 'stranger') . ' ' . (($name2) ? $name2 : 'stranger');
            });

            // Test the /hello/bramus route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/hello/bramus';
            \src\Router\Router::run();
            $this->assertEquals('Hello bramus stranger', ob_get_contents());

            // Test the /hello/bramus/bramus route
            ob_clean();
            $_SERVER['REQUEST_URI'] = '/hello/bramus/bramus';
            \src\Router\Router::run();
            $this->assertEquals('Hello bramus bramus', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testDynamicRouteWithWildcard()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('(.*)', function ($name) {
                echo 'Hello ' . $name;
            });

            // Test the /hello/bramus route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/hello/bramus';
            \src\Router\Router::run();
            $this->assertEquals('Hello hello/bramus', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testDynamicRouteWithPartialWildcard()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('/hello/(.*)', function ($name) {
                echo 'Hello ' . $name;
            });

            // Test the /hello/bramus route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/hello/bramus/sumarb';
            \src\Router\Router::run();
            $this->assertEquals('Hello bramus/sumarb', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function test404()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('/', function () {
                echo 'home';
            });
            \src\Router\Router::set404(function () {
                echo 'route not found';
            });

            \src\Router\Router::set404('/api(/.*)?', function () {
                echo 'api route not found';
            });

            // Test the /hello route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/';
            \src\Router\Router::run();
            $this->assertEquals('home', ob_get_contents());

            // Test the /hello/bramus route
            ob_clean();
            $_SERVER['REQUEST_URI'] = '/foo';
            \src\Router\Router::run();
            $this->assertEquals('route not found', ob_get_contents());

            // Test the custom api 404
            ob_clean();
            $_SERVER['REQUEST_URI'] = '/api/getUser';
            \src\Router\Router::run();
            $this->assertEquals('api route not found', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function test404WithClassAtMethod()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Route

            \src\Router\Router::get('/', function () {
                echo 'home';
            });

            \src\Router\Router::set404('Handler@notFound');

            // Test the /hello route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/';
            \src\Router\Router::run();
            $this->assertEquals('home', ob_get_contents());

            // Test the /hello/bramus route
            ob_clean();
            $_SERVER['REQUEST_URI'] = '/foo';
            \src\Router\Router::run();
            $this->assertEquals('route not found', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function test404WithClassAtStaticMethod()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('/', function () {
                echo 'home';
            });

            \src\Router\Router::set404('Handler@notFound');

            // Test the /hello route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/';
            \src\Router\Router::run();
            $this->assertEquals('home', ob_get_contents());

            // Test the /hello/bramus route
            ob_clean();
            $_SERVER['REQUEST_URI'] = '/foo';
            \src\Router\Router::run();
            $this->assertEquals('route not found', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function test404WithManualTrigger()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('/', function () {
                \src\Router\Router::trigger404();
            });
            \src\Router\Router::set404(function () {
                echo 'route not found';
            });

            // Test the / route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/';
            \src\Router\Router::run();
            $this->assertEquals('route not found', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testBeforeRouterMiddleware()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::middleware('GET|POST', '/.*', function () {
                echo 'middleware ';
            });
            \src\Router\Router::get('/', function () {
                echo 'root';
            });
            \src\Router\Router::get('/about', function () {
                echo 'about';
            });
            \src\Router\Router::get('/contact', function () {
                echo 'contact';
            });
            \src\Router\Router::post('/post', function () {
                echo 'post';
            });

            // Test the / route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/';
            \src\Router\Router::run();
            $this->assertEquals('middleware root', ob_get_contents());

            // Test the /about route
            ob_clean();
            $_SERVER['REQUEST_URI'] = '/about';
            \src\Router\Router::run();
            $this->assertEquals('middleware about', ob_get_contents());

            // Test the /contact route
            ob_clean();
            $_SERVER['REQUEST_URI'] = '/contact';
            \src\Router\Router::run();
            $this->assertEquals('middleware contact', ob_get_contents());

            // Test the /post route
            ob_clean();
            $_SERVER['REQUEST_URI'] = '/post';
            $_SERVER['REQUEST_METHOD'] = 'POST';
            \src\Router\Router::run();
            $this->assertEquals('middleware post', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testAfterRouterMiddleware()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('/', function () {
                echo 'home';
            });

            // Test the / route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/';
            \src\Router\Router::run(function () {
                echo 'finished';
            });
            $this->assertEquals('homefinished', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testBasicController()
        {
            // Clear all routes
            \src\Router\Router::clear();

            \src\Router\Router::setNamespace('\hello');

            \src\Router\Router::get('/show/(.*)', 'RouterTestController@show');

            ob_start();
            $_SERVER['REQUEST_URI'] = '/show/foo';
            \src\Router\Router::run();

            $this->assertEquals('foo', ob_get_contents());

            // cleanup
            ob_end_clean();
        }

        public function testDefaultNamespace()
        {
            // Clear all routes
            \src\Router\Router::clear();


            \src\Router\Router::setNamespace('\Hello');

            \src\Router\Router::get('/show/(.*)', 'HelloRouterTestController@show');

            ob_start();
            $_SERVER['REQUEST_URI'] = '/show/foo';
            \src\Router\Router::run();

            $this->assertEquals('foo', ob_get_contents());

            // cleanup
            ob_end_clean();
        }

        public function testSubfolders()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::get('/', function () {
                echo 'home';
            });

            // Test the / route in a fake subfolder
            ob_start();
            $_SERVER['SCRIPT_NAME'] = '/about/index.php';
            $_SERVER['REQUEST_URI'] = '/about/';
            \src\Router\Router::run();
            $this->assertEquals('home', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testSubrouteMouting()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router

            \src\Router\Router::mount('/movies', function () {
                \src\Router\Router::get('/', function () {
                    echo 'overview';
                });
                \src\Router\Router::get('/(\d+)', function ($id) {
                    echo htmlentities($id);
                });
            });

            // Test the /movies route
            ob_start();
            $_SERVER['REQUEST_URI'] = '/movies';
            \src\Router\Router::run();
            $this->assertEquals('overview', ob_get_contents());

            // Test the /hello/bramus route
            ob_clean();
            $_SERVER['REQUEST_URI'] = '/movies/1';
            \src\Router\Router::run();
            $this->assertEquals('1', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }

        public function testHttpMethodOverride()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Fake the request method to being POST and override it
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] = 'PUT';

            $method = new ReflectionMethod(
                '\src\Router\Router',
                'getRequestMethod'
            );

            $method->setAccessible(true);

            $this->assertEquals(
                'PUT',
                \src\Router\Router::getRequestMethod()
            );
        }

        public function testControllerMethodReturningFalse()
        {
            // Clear all routes
            \src\Router\Router::clear();

            // Create Router
            \src\Router\Router::setNamespace('\Hello');

            \src\Router\Router::get('/false', 'RouterTestController@returnFalse');
            \src\Router\Router::get('/static-false', 'RouterTestController@staticReturnFalse');

            // Test returnFalse
            ob_start();
            $_SERVER['REQUEST_URI'] = '/false';
            \src\Router\Router::run();
            $this->assertEquals('returnFalse', ob_get_contents());

            // Test staticReturnFalse
            ob_clean();
            $_SERVER['REQUEST_URI'] = '/static-false';
            \src\Router\Router::run();
            $this->assertEquals('staticReturnFalse', ob_get_contents());

            // Cleanup
            ob_end_clean();
        }
    }
}

namespace Hello {
    class RouterTestController
    {
        public function show($id)
        {
            echo $id;
        }

        public function returnFalse()
        {

            echo 'returnFalse';

            return false;
        }

        public static function staticReturnFalse()
        {
            echo 'staticReturnFalse';

            return false;
        }
    }
}

namespace Hello {
    class HelloRouterTestController
    {
        public function show($id)
        {
            echo $id;
        }
    }
}

// EOF
