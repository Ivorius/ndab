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
use Nette\Database\Context;
use Nette\Database\IConventions;



/**
 * Ndab Grouped selection
 *
 * @author  Jan Skrasek
 */
class GroupedSelection extends Table\GroupedSelection
{
	/** @var string */
	protected $table;

	/** @var string */
	protected $rowClass;

	/** @var Manager */
	protected $manager;



	/**
	 * Creates filtered and grouped table representation.
	 * @param  Context
	 * @param  IConventions
	 * @param  string  database table name
	 * @param  string  joining column
	 * @param  Selection
	 * @param  Nette\Caching\IStorage|NULL
	 * @param  Manager
	 */
	public function __construct(Context $context, IConventions $conventions, $table, $column,  Table\Selection $refTable,  Manager $manager, Nette\Caching\IStorage $cacheStorage = NULL)
	{
		parent::__construct($context, $conventions, $table, $column,  $refTable,  $cacheStorage);
		$this->manager = $manager;
		$this->table = $table;
		$this->conventions = $conventions;
		$this->context = $context;
	}


	public function getManager()
	{
		return $this->manager;
	}


	public function getTable()
	{
		return $this->table;
	}



	public function setRowClass($class)
	{
		$this->rowClass = $class;
		return $this;
	}



	public function getRowClass()
	{
		return $this->rowClass;
	}



	protected function createRow(array $row)
	{
		return $this->refTable->getManager()->initEntity($row, $this);
	}



	public function createSelectionInstance($table = NULL)
	{
		return new Selection($this->context, $this->conventions, $table ?: $this->table, $this->refTable->getManager());
	}



	protected function createGroupedSelectionInstance($table, $column)
	{
		return new GroupedSelection($this->context, $this->conventions, $table, $column, $this, $this->manager);
	}

}
