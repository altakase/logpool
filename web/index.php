<?php
include_once(dirname(__FILE__) . '/../lib/Core.php');

session_cache_limiter(false);
session_name(\lib\Constants::SESSION_NAME);
//session_start();

//Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem(dirname(__FILE__) . '/../templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => dirname( __FILE__ ) . '/../.twigcache',
    'cache' => false
));

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim(array(
    'cookies.encrypt' => true,
    'cookies.secret_key' => CONFIG_SESSION_SECRET,
    'cookies.cipher' => MCRYPT_RIJNDAEL_256,
    'cookies.cipher_mode' => MCRYPT_MODE_CBC
));

const CSRF_VALIDATE_PARAMETER = "checksid";

$app->valid_csrf_param = false;
function refreshCsrfParam()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION[CSRF_VALIDATE_PARAMETER] = lib\Auth::makeRandom(64);
    }
}

function getCsrfParam()
{
    return array("key" => CSRF_VALIDATE_PARAMETER,
        "value" => $_SESSION[CSRF_VALIDATE_PARAMETER]);
}

$app->hook('slim.before.dispatch', function () use ($app) {
    $publicRoutes = array('/'); // ログインがいるシステムの場合は $publicRoutes = array('/login');
    $basicAuthRoutes = array(); // BASIC認証がいるシステムの場合は $publicRoutes = array('/');
    $app->valid_csrf_param = false;
    $app->baseUrl = $app->request->getScheme() . '://' . CONFIG_AUTH_BASIC_USER . ':' . CONFIG_AUTH_BASIC_PASS . '@' . $app->request->getHostWithPort();
    function url_match($url, $arr)
    {
        foreach ($arr as $v) {
            $w = preg_quote(rtrim($v, '/'), '/');
            if (preg_match('/' . $w . '(\/.*)?/', $url)) {
                return true;
            }
        }
    }

    // CHECK CSRF
    {
        if (!isset($_SESSION[CSRF_VALIDATE_PARAMETER])) {
            $_SESSION[CSRF_VALIDATE_PARAMETER] = lib\Auth::makeRandom(64);
        }

        if ($app->request->isPost()) {
            if ($app->request->post(CSRF_VALIDATE_PARAMETER)
                === $_SESSION[CSRF_VALIDATE_PARAMETER]
            ) {
                $app->valid_csrf_param = true;
            }
        }
    }

    if (!url_match($app->request->getPath(), $publicRoutes)) {
        if (isset($_SESSION['username'])) {
            return true;
        } else {
            $_SESSION['first_url'] = $app->request->getPath();
            $app->redirect('/login');
        }
    } else if (url_match($app->request->getPath(), $basicAuthRoutes)) {
        switch (true) {
            case !isset($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']):
            case $_SERVER['PHP_AUTH_USER'] !== CONFIG_AUTH_BASIC_USER:
            case $_SERVER['PHP_AUTH_PW'] !== CONFIG_AUTH_BASIC_PASS:
                header('WWW-Authenticate: Basic realm="Enter username and password."');
                header('Content-Type: text/plain; charset=utf-8');
                die('FORBIDDEN');
        }
    }

    session_name(\lib\Constants::SESSION_NAME);
});

/** セッションエラー */
$app->get('/invalid', function () use ($app) {

});

/** ログイン */
$app->get('/login', function () use ($app, $twig) {
    print $twig->render('login.twig', array("csrf" => getCsrfParam()));
});

$app->post(
    '/login',
    function () use ($app, $twig) {
        if ($app->valid_csrf_param && lib\Auth::validate($app->request->post('inputUsername'), $app->request->post('inputPassword'))) {
            $redirect_uri = '/';
            if (!empty($_SESSION['first_url'])) {
                $redirect_uri = $_SESSION['first_url'];
            }

            session_regenerate_id();
            $_SESSION['username'] = $app->request->post('inputUsername');
            refreshCsrfParam();
            $app->redirect($redirect_uri);
        } else {
            if (!$app->valid_csrf_param) {
                $msgs = array("セッションがタイムアウトしました。もう一度おためしください。");
            } else {
                $msgs = array("ユーザー名かパスワードが違います。");
            }

            print $twig->render('login.twig', array("inputUsername" => $app->request->post('inputUsername')
            , "csrf" => getCsrfParam()
            , "msgs" => $msgs));
        }
    }
);

/** ログアウト */
$app->get('/logout', function () use ($app) {
    $app->deleteCookie(\lib\Constants::SESSION_NAME);
    session_destroy();
    $app->redirect('/');
});

/** ルーティング読み込み */
require(dirname(__FILE__) . '/../routes/index.php');

$app->run();
