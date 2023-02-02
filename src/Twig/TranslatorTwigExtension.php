<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\CoreExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TranslatorTwigExtension extends AbstractExtension
{
    protected $translator;

    protected $requestStack;

    protected $locale;

    protected $twig;

    protected static $instance;


    public function __construct(TranslatorInterface $translator, RequestStack $requestStack, Environment $twig)
    {
        $this->translator = $translator;
        $this->requestStack = $requestStack;
        $this->twig = $twig;
        self::$instance = $this;
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            //new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('__', [$this, '__']),
            new TwigFunction('__U', [$this, '__u']),
            new TwigFunction('_U', [$this, '__u']),
            new TwigFunction('__u', [$this, '__u']),
            new TwigFunction('_u', [$this, '__u']),
            new TwigFunction('_t', [$this, '__t']),
            new TwigFunction('_T', [$this, '__t']),
            new TwigFunction('__t', [$this, '__t']),
            new TwigFunction('__T', [$this, '__t']),
            new TwigFunction('confirm_delete', [$this, 'confirmDelete'], ['is_safe' => ['html']])
        ];
    }

    public function confirmDelete(string $entity)
    {
        $confirmMessage = $this->__u('are you sure to want delete this') . ' ' . $this->__($entity) . '?';
        $ifYes = $this->__u('if you want to delete, press');
        $delete = $this->__u('delete');
        $ifNo = $this->__u('if you do not want to delete, press');
        $cancel = $this->__u('cancel');
        $string =  <<<HTML
<p>$confirmMessage</p>
<ul>
<li>$ifYes <span class="text-danger">$delete</span> </li>
<li>$ifNo <span class="text-primary">$cancel</span></li>
</ul>
HTML;
        return $string;
    }

    public function __(string $id, $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        $id = strtolower($id);
        return $this->translator->trans($id, $parameters, $domain, $locale ?? $this->getRequestLocale());
    }

    public function __u(string $id, $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        return ucfirst($this->__($id, $parameters, $domain, $locale ?? $this->getRequestLocale()));
    }

    public function __t(string $id, $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        return $this->title(
            $this->twig,
            $this->translator->trans($id, $parameters, $domain, $locale ?? $this->getRequestLocale())
        );
    }

    public function getRequestLocale()
    {
        if ($this->locale === null) {
            $this->locale = $this->requestStack->getMasterRequest()->getLocale();
        }
        return $this->locale;
    }

    /**
     * Returns a titlecased string.
     *
     * @param string $string A string
     *
     * @return string The titlecased string
     */
    public function title(Environment $env, $string)
    {
        if (null !== $charset = $env->getCharset()) {
            return mb_convert_case($string, MB_CASE_TITLE, $charset);
        }

        return ucwords(strtolower($string));
    }

    /**
     * Get the value of instance
     */
    public static function getInstance(): ?self
    {
        return self::$instance;
    }
}
