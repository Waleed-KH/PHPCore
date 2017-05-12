<?php
namespace Core\Database;

class Data
{
	protected $id;
	protected $data = array();

	protected function __construct($data)
	{
		$this->id = $data['id'];
		$this->data = $data;
	}

	private function __construct1()
	{
	}

	public function __get($name)
	{
		if (array_key_exists($name, $this->data))
			return $this->data[$name];
		$trace = debug_backtrace();
		trigger_error(
			'Undefined property via __get(): ' . $name .
			' in ' . $trace[0]['file'] .
			' on line ' . $trace[0]['line'],
			E_USER_NOTICE);
		return null;
	}

	public function ToArray()
	{
		return $this->data;
	}
}
