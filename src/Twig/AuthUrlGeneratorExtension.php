<?php

namespace App\Twig;

use App\Tools\Routes\Annotations\RouteActionManager;
use Exception;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;

class AuthUrlGeneratorExtension extends AbstractExtension
{


    protected $requestStack;

    protected $router;

    protected $routeActionManager;

    protected $tokenManager;

    protected $translatorTwigExtension;

    public function __construct(
        RequestStack $requestStack,
        RouterInterface $router,
        RouteActionManager $routeActionManager,
        CsrfTokenManagerInterface $tokenManager,
        TranslatorTwigExtension $translatorTwigExtension,
    ) {
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->routeActionManager = $routeActionManager;
        $this->tokenManager = $tokenManager;
        $this->translatorTwigExtension = $translatorTwigExtension;
    }


    public function getFunctions(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFunction('create_link', [$this, 'link'], ['is_safe' => ['html']]),
            new TwigFunction('create_form', [$this, 'form'], ['is_safe' => ['html']]),
            new TwigFunction('menu_routes', [$this, 'menu'], ['is_safe' => ['html']]),
            new TwigFunction('js_confirm', [$this, 'JsConfirm'], ['is_safe' => ['html']])

        ];
    }

    public function getFilters()
    {
        return [];
    }

    public function link(string $path, string $label, array $params = [], array $attrs = [], array $config = [])
    {
        $returnBool = $config['boolean'] ?? $attrs['boolean'] ?? false;
        $valid = $config['valid'] ?? $attrs['valid'] ?? true;
        if ($valid) {
            $debug = $config['debug'] ?? $attrs['debug'] ?? false;
            $isAuthorize = $this->routeActionManager->isAuthorize($path);
            if ($debug) {
                dump($isAuthorize);
            }
            if ($isAuthorize === 1) {
                if ($returnBool) return true;

                $title = $config['title'] ?? $attrs['title'] ?? false;
                //$params['_locale'] = $params['_locale'] ?? $this->getLocale();
                $style = $attrs['style'] ?? $config['style'] ?? null;

                try {
                    $route = $this->router->generate($path, $params);
                } catch (Exception $e) {
                    dd($e->getMessage());
                }

                $class = isset($attrs['class']) ? 'class="' . $attrs['class'] . '"' : '';
                $style = $style ? 'style="' . $attrs['style'] . '"' : '';
                $title = $title ? 'title="' . $title . '"' : '';
                return "<a href='{$route}' {$class} {$style} {$title}>{$label}</a>";
            }
        }
        return $returnBool ? false : '';
    }

    public function form($path, string $label, array $options = [], string $methods = 'post')
    {
        if (isset($options['valid']) and $options['valid'] === false) return '';
        if (is_array($path)) {
            $params = $path[1];
            //$params['_locale'] = $params['_locale'] ?? $this->getLocale();
            $path = $this->router->generate($path[0], $params);
        }
        $style = isset($options['style']) ? ' style="' . $options['style'] . '"' : '';
        $btn_style = isset($options['btn_style']) ? ' style="' . $options['btn_style'] . '"' : '';
        $confirm = isset($options['confirm']) ? ' onsubmit="return confirm(\'' . $options['confirm'] . '\')"'  : '';
        $csrf_name = $options['csrf_name'] ?? null;
        $btn = $options['btn'] ?? 'primary';
        $class = 'btn btn-' . $btn . ' ';
        $class .= isset($options['class']) ? $options['class'] : '';
        $csrf_value = $csrf_name ? 'value="' . $this->tokenManager->getToken($csrf_name)->getValue() . '"' : '';

        return "<form action='$path' method='$methods' " . $style  . $confirm  . ">
        <input type='hidden' name='_csrf_token'  $csrf_value/> 
        <button class='$class' {$btn_style}>$label</button></form>";
    }


    public function JsConfirm(?string $message): string
    {
        return $message ? 'return confirm(\'' . $message . '\')' : '';
    }

    public function menu(array $menu_routes = [])
    {
        $menu = '';
        $menuLabel = '';
        $buildmenu = '';
        foreach ($menu_routes as $key => $routes) {
            $menuLabel = '<li class="nav-item">
            <div class="my-dropdown">
                <div class="dropbtn">' .
                $this->translatorTwigExtension->__u($key)
                . '</div>
                <div class="dropdown-content bg-chocolate">';
            $i = 0;
            $menu = '';
            foreach ($routes as $k => $route) {
                $icon = '';
                if (is_array($route)) {

                    $icon = "<i class='{$route[1]}' ></i>";
                    $route = $route[0];
                }
                $icon = '';
                $label =  $this->translatorTwigExtension->__u($k) . ' ' . $icon;
                $menu .= $this->link($route, $label, [], ['class' => ($i !== 0 ? 'border-top' : '') . ' nav-link']);
                $i++;
            }
            $buildmenu .= empty($menu) ? '' : ($menuLabel . $menu .  '</div></div></li>');
        }
        return $buildmenu;
    }

    private function getLocale(): string
    {
        $request = $this->requestStack->getMasterRequest();
        return $request->attributes->get('_locale') ?? $request->getLocale();
    }
}
