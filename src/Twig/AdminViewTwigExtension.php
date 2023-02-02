<?php

namespace App\Twig;

use Mpdf\Tag\B;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AdminViewTwigExtension extends AbstractExtension
{

    private $colors = ['edit', 'save', 'update', 'chocolate', 'purple'];

    private $bindColors = [
        'edit' => 'save',
        'save' => 'update',
        'update' => 'save',
        'chocolate' => 'edit',
        'purple' => 'edit'
    ];
    private $index = 0;

    public function next()
    {
        if ($this->index === count($this->colors))  $this->index = 0;
        $color =  $this->colors[$this->index];
        $this->index++;
        return $color;
    }


    public function __construct(
        private TranslatorTwigExtension $translator,
        private array $items = []
    ) {
    }

    public function getFilters(): array
    {
        return [];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_admin_view', [$this, 'render']),
            new TwigFunction('add_admin_item', [$this, 'addItem']),
            new TwigFunction('set_admin_items', [$this, 'setItems'])
        ];
    }

    public function setItems(array $items)
    {
        foreach ($items as $key => $values) {
            if ($key === 'data') {
                foreach ($values as $key => $value) {
                    $name = $value['title'] ?? $key;
                    $this->addItem('<div class="card radius-6 text-center text-white style="height: 100%;">
                <div class="card-header">
                    <h3>' . $values["count"]  . '</h3>
                </div>
                <div class="card-body">
                    <p>' .
                        $this->translator->__u(
                            ($value["count"] > 1 ? ($name) . "s" : $name),
                            (stripos($key, "_balance") and $value["base"] === null) ? ["%balance%" =>  (int) $value["base"]] : []
                        )   .
                        '</p>
                </div>
                <div class="card-footer">
                    <a href="' . $value["path"] . '">' .  $this->translator->__u("show") . '</a>
                </div>
            </div>');
                }
            }
        }
    }

    public function addItem(string $item)
    {
        $this->items[] = $item;
    }

    public function render(): string
    {
        $html = '<div class="row">';
        //$color = $this->next();
        $color = 'update';
        foreach ($this->items as $key => $item) {
            $html .= '<div class="col-md-4 p-2">';
            $item = str_replace('{{ color }}', $color, $item);
            $item = str_replace('{{ cd-color }}', $this->bindColors[$color], $item);
            $html .= $item . '</div>';
            if (($key + 1) % 3 === 0) {
                //$color = $this->next();
                $html .= '</div><hr/><div class="row ">';
            }
        }

        return $html . ' </div>';
    }
}
