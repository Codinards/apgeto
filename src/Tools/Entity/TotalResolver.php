<?php

namespace App\Tools\Entity;

use Illuminate\Support\Collection;
use Traversable;

use function foo\func;

class TotalResolver
{

    private $fields = [
        'fund' => [
            'fields' => ['cashInFlows', 'cashOutFlows'],
            'soldes' => [
                'cashBalances' => ['cashInFlows', 'cashOutFlows']
            ]
        ],
        'debt' => [
            'fields' => ['loanInFlows', 'loanOutFlows', 'interests'],
            'soldes' => [
                'loanBalances' => ['loanInFlows', 'loanOutFlows']
            ]
        ],
        'account' => [
            'fields' => ['cashInFlows', 'cashOutFlows', 'loanInFlows', 'loanOutFlows'],
            'soldes' => [
                'cashBalances' => ['cashInFlows', 'cashOutFlows'],
                'loanBalances' => ['loanInFlows', 'loanOutFlows']
            ]
        ],
    ];
    public function resolve(iterable $data, string $type, $olbBalance = null): self
    {
        $fields = $this->fields[$type];
        if (!$data instanceof Collection) {
            $data = new Collection(\is_object($data) ? $data->toArray() : $data);
        }
        foreach ($fields['fields'] as $property) {
            $this->$property = null;
        }
        foreach ($this as $key => $property) {

            if ($key == 'fields') continue;

            $method = 'get'  .  \ucfirst($key);
            $this->$key = $data->sum(function ($item) use ($method) {
                if ($item && method_exists($item, $method)) {
                    return $item->$method();
                }
                return 0;
            });
        }
        foreach ($fields['soldes'] as $key => $value) {
            $first = $value[0];
            $second = $value[1];
            $this->$key = $this->$first - $this->$second + ($olbBalance ? $olbBalance->getCashBalances() : 0);
        }
        return $this;
    }
}
