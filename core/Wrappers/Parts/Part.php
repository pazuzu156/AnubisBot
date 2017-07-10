<?php

namespace Core\Wrappers\Parts;

class Part
{
	protected $part;

	/**
	 * Ctor.
	 *
	 * @param mixed \Discord\Parts\Part
	 *
	 * @return void
	 */
	public function __construct($part)
	{
		$this->part = $part;
	}

    /**
     * Used to dynamically call on Part class methods.
     *
     * @param string $method
     * @param array  $params
     *
     * @return mixed
     */
    public function __call($method, $params)
    {
        return call_user_func_array([$this->part, $method], $params);
    }

    /**
     * Used to dynamically call on Part class properties.
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        try {
            return $this->part->{$property};
        } catch (\Exception $ex) {
            return null;
        }
    }
}
