<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait FilterableResourceTrait
{
    /** @var Collection */
    public $collection;

    /**
     * Fields to filtrate.
     *
     * @var array
     */
    protected $fieldsToFiltrate = [];

    /**
     * Filtrate method name.
     *
     * @var string
     */
    protected $filtrateMethod;

    /**
     * Set properties to exclude specified fields.
     *
     * @param  array|string $fieldsToFiltrate
     * @return object $this
     */
    public function except(...$fieldsToFiltrate)
    {
        $this->setFiltrateProps(__FUNCTION__, $fieldsToFiltrate);

        return $this;
    }

    /**
     * Set properties to make visible specified fields.
     * /**
     * @param mixed ...$fieldsToFiltrate
     * @return $this
     */
    public function only(...$fieldsToFiltrate)
    {
        $this->setFiltrateProps(__FUNCTION__, $fieldsToFiltrate);

        return $this;
    }

    /**
     * Filtrate fields.
     *
     * @param  array $data
     * @return array       [filtrated data]
     */
    public function filtrateFields($data)
    {
        if ($this->filtrateMethod) {
            $filter = $this->filtrateMethod;

            return collect($data)->{$filter}($this->fieldsToFiltrate)->toArray();
        }

        return $data;
    }

    /**
     * Set properties for filtrating.
     *
     * @param string $method
     * @param array $fields
     */
    protected function setFiltrateProps($method, $fields)
    {
        $this->filtrateMethod = $method;
        $this->fieldsToFiltrate = is_array($fields[0]) ? $fields[0] : $fields;
    }

    /**
     * Send fields to filtrate to Resource while processing the collection.
     *
     * @param  $request
     * @return array|Collection
     */
    protected function processCollection($request)
    {
        if (! $this->filtrateMethod) {
            return $this->collection;
        }

        return $this->collection->map(function ($resource) use ($request) {
            $method = $this->filtrateMethod;

            return $resource->$method($this->fieldsToFiltrate)->toArray($request);
        })->all();
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function groupOfHiddenFields($name)
    {
        return $this->hiddenFields[$name] ?? null;
    }
}
