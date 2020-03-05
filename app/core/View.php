<?php


namespace App\Core;


/**
 * View
 */
class View
{
    /**
     * @var \Twig_Environment $engine
     */
    public static $engine;

    /**
     * Render a view file
     *
     * @param string $view  The view file
     *
     * @return void
     */
    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);

        //$file = "../view/$view";  // relative to Core directory
        $file = $_SERVER['DOCUMENT_ROOT']."app/view/$view";  // absolute paths

        if (is_readable($file)) {
            require $file;
        } else {
            echo "$file not found";
        }
    }

    /**
     * Render a view template using Twig
     *
     * @param string $template The template file
     * @param array $args Associative array of data to display in the view (optional)
     *
     * @param $template
     * @param array $args
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function renderTemplate($template, $args = [])
    {
        if (self::$engine === null) {

            $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/view');

            self::$engine = new \Twig\Environment($loader, array('debug' => true));
            self::$engine->addExtension(new \Twig\Extension\DebugExtension());
            self::$engine->addExtension(new \Twig_Extensions_Extension_Text());
        }

        echo self::$engine->render($template, $args);
    }
}