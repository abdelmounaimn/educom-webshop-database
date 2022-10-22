<?php

session_start();
include("./dataAccessObject/database_repository.php");
include("./presentation/components.php");
include("./presentation/shoppingCart.php");
include("./presentation/container.php");
include("./validations/validations.php");
include("./presentation/thinks.php");
include("./presentation/home.php");
include("./presentation/about.php");
include("./sessionManager/session_manager.php");
include("./presentation/webshop.php");
include("services/webshopServices.php");
include("./dataAccessObject/user_repository.php");


$requested_page = getRequestedPage();
$data = processRequest($requested_page);
showResponsePage($data);


/**
 * 
 *  getRequestedPage
 * 
 */

function getRequestedPage()
{

    $requested_type = $_SERVER['REQUEST_METHOD'];
    if ($requested_type == 'POST') {
        $requested_page = getPostVar('page', 'home');
    } else {
        $requested_page = getUrlVar('page', 'home');
    }
    return $requested_page;
}

function getPostVar($key, $default = '')
{
    return getArrayVar($_POST, $key, $default);
}
function getArrayVar($array, $key, $default = '')
{
    return isset($array[$key]) ? $array[$key] : $default;
}

function getUrlVar($key, $default = 'other')
{

    if (count(array_keys($_GET)) == 0) return 'home';
    return filter_input(INPUT_GET, $key);
    //$nbrArgs = count(array_keys($_GET));

    //return  $nbrArgs == 1 ? filter_input(INPUT_GET, $key) :  $default;
}

/**
 * 
 * 
 * processRequest($requested_page)
 * 
 */
function processRequest($requested_page)
{
    switch ($requested_page) {
        case 'contact':
            $data = validateContact();
            if ($data['validForm']) {
                $requested_page = 'thinks';
            }
            break;
        case 'register':
            $data = validateRegister();
            if ($data['validForm']) {
                $data = validateLogin();
                $requested_page = 'login';
            }
            break;
        case 'login':
            $data = validateLogin();
            if ($data['validForm']) {
                $requested_page = 'home';
            }
            break;
        case 'logout':
            do_user_logout();
            $requested_page = 'home';
            break;
        case 'detail':
            $data['productId'] = getUrlVar('id', "");
            break;
        case 'cart':
            $data = validateCart();
            $requested_page = $data['page'];
            break;
    }
    $data['page'] = $requested_page;
    return $data;
}

/**
 * 
 * showResponsePage
 * 
 */

function showResponsePage($data)
{

    switch ($data['page']) {
        case 'home':
            echo_html_document(array("title" => "Home", "script" => "", "style" => "css/stylesheet.css"),  showHomeBody());
            break;
        case 'about':
            echo_html_document(array("title" => "about", "script" => "", "style" => "css/stylesheet.css"), is_user_logged_in() ? showAboutBody() : 'log eerst in ');
            break;
        case 'contact':
            echo_html_document(array("title" => "contact", "script" => "", "style" => "css/stylesheet.css"), generateForm($data));
            break;
        case 'register':
            echo_html_document(array("title" => "Register", "script" => "", "style" => "css/stylesheet.css"), generateForm($data));
            break;
        case 'login':
            echo_html_document(array("title" => "Log in", "script" => "", "style" => "css/stylesheet.css"), generateForm($data));
            break;
        case 'thinks':
            echo_html_document(array("title" => "Log in", "script" => "", "style" => "css/stylesheet.css"), showContactThinks($data));
            break;
        case 'webshop':
            echo_html_document(array("title" => "Log in", "script" => "", "style" => "css/stylesheet.css"), showShoppinPage());
            break;
        case 'detail':
            echo_html_document(array("title" => "Log in", "script" => "", "style" => "css/stylesheet.css"), showDetailPage($data['productId']));
            break;
        case 'cart':
            echo_html_document(array("title" => "Log in", "script" => "", "style" => "css/stylesheet.css"), cart($data['cart']));

        default:
            echo $data['page'] . ' URL is niet geldig';
    }
}
