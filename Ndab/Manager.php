<?php

/**
 * This file is part of the Ndab
 *
 * Copyright (c) 2012 Jan Skrasek (http://jan.skrasek.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Ndab;

use Nette;
use Nette\Database\Table;
use Nette\SmartObject;



/**
 * Ndab base entity manager
 *
 * @author  Jan Skrasek

 */
abstract class Manager
{
	use SmartObject;


	/** @var Nette\Database\Context */
	protected $context;

	/** @var Nette\Database\IConventions */
	protected $conventions;

	/** @var string */
	protected $tableName;

	/** @var string */
	protected $primaryColumn;

	/** @var Settings */
	protected $settings;

    /** @var string */
    protected $rowClass;


	/**
	 * Manager constructor.
	 * @param  Nette\Database\Connection $context
	 * @param  string
	 * @param  string
	 */
	public function __construct(Nette\Database\Context $context, Settings $settings, $tableName = NULL)
	{
		$this->context = $context;
		$this->settings   = $settings;
		if ($tableName) {
			$this->tableName = $tableName;
		}

		if (empty($this->tableName)) {
			throw new Nette\InvalidStateException('Undefined tableName property in ' . $this->getReflection()->name);
		}

		$this->conventions = $context->getConventions();
		$this->primaryColumn = $this->conventions->getPrimary($this->tableName);
	}



	/**
	 * Creates entity with data.
	 * @param  array      entity data
	 * @param  Selection  parent selection
	 * @return Table\ActiveRow
	 */
	public function initEntity(array $data, Table\Selection $selection)
	{
		$class = $selection->getRowClass();
		if (!$class && isset($this->settings->tables->{$selection->getTable()})) {
			$class = $this->settings->tables->{$selection->getTable()};
		}
		if (!$class) {
			$class = '\Ndab\Entity';
		}

        $entity = new $class($data, $selection);
        return $entity;
	}

	public function getRowClass()
	{
		return $this->rowClass;
	}

	public function setRowClass($class)
	{
		$this->rowClass = $class;
	}


	/**
	 * Returns all rows filtered by $conds
	 * @param  array  $conds
	 * @return Selection
	 */
	public function getAll($conds = array())
	{
		return $this->table()->where($conds);
	}



	/**
	 * Returns row identified by $privaryValue
	 * @param  mixed  $privaryValue
	 * @return Entity
	 */
	public function get($privaryValue)
	{
		return $this->table()->get($privaryValue);
	}



	/**
	 * Inserts data into table
	 * @param  mixed $values
	 * @return Entity
	 */
	public function create($values)
	{
		$entity = $this->table()->insert($values);
		return $this->get($entity[$this->primaryColumn]);
	}



	/**
	 * Updates entry
	 * @param  mixed $values
	 * @return Entity
	 */
	public function update($values)
	{
		if (!isset($values[$this->primaryColumn]))
			throw new Nette\InvalidArgumentException('Missing primary value');

		$primaryValue = $values[$this->primaryColumn];

		$updates = $values->getMyData();

		$this->table()->where($this->primaryColumn, $primaryValue)->update($updates);
		return $this->get($primaryValue);
	}



	/**
	 * Deletes entry
	 * @param  Entity|mixed  Entity instance or primary value
	 * @return bool
	 */
	public function delete($entity)
	{
		if ($entity instanceof Entity)
			$primaryValue = $entity[$this->primaryColumn];
		else
			$primaryValue = $entity;

		return $this->table()->where($this->primaryColumn, $primaryValue)->delete() > 0;
	}



	/**
	 * Returns table selection.
	 * @return Selection
	 */
	final protected function table()
	{
		return new Selection($this->context, $this->context->getConventions(), $this->tableName, $this);
	}

}
