<?php

namespace App\Twig;

use App\Entity\Main\Users\User;
use App\Tools\Routes\Annotations\RouteActionManager;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;
use Twig\ExpressionParser;
use Twig\Extension\AbstractExtension;
use Twig\Node\Expression\Binary\AndBinary;
use Twig\Node\Expression\Binary\OrBinary;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

class UserTwigExtension extends AbstractExtension
{
    protected $security;

    private $auth;

    public function __construct(
        Security $security,
        RouteActionManager $routeActionManager,
        Environment $twig
    ) {
        $this->security = $security;
        $this->routeActionManager = $routeActionManager;
        $twig->addGlobal('routeActionManager', $routeActionManager);
    }

    public function getTokenParsers()
    {
        return [
            new AuthTokenParser($this->routeActionManager)
        ];
    }

    public function getOperators()
    {
        return [
            [],
            [
                '||' => ['precedence' => 10, 'class' => OrBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
                '&&' => ['precedence' => 15, 'class' => AndBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
            ]
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('format_name', [$this, 'FormatName']),
            new TwigFilter('name_format', [$this, 'FormatName']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_auth', [$this, 'getAuth']),
            new TwigFunction('is_admin', [$this, 'getAdmin']),
        ];
    }

    public function formatName($name)
    {
        $names = explode(" ", $name);
        $count = count($names);
        $newName = "";
        if ($count > 2) {
            foreach ($names as $key => $name) {
                if ($key == 0 || $key == ($count - 1)) {
                    $newName .= $name . ' ';
                } else {
                    $newName .= ucfirst($name[0]) . '. ';
                }
            }
            return substr($newName, 0, -1);
        }
        return $name;
    }

    public function getAuth(): ?user
    {
        if (is_null($this->auth)) {
            $this->auth = $this->security->getUser();
        }
        return $this->auth;
    }

    public function getAdmin(): bool
    {
        return $this->getAuth() && in_array($this->getAuth()->getRole()->getName(), ['ROLE_ADMIN', 'ROLE_SUPERADMIN']);
    }
}
