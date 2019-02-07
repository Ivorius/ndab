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
 * Ndab selection
 *
 * @author  Jan Skrasek
 */
class Selection extends Table\Selection
{

	/** @var Context */
	protected $context;

	/** @var IConventions */
	protected $conventions;

	/** @var Nette\Caching\Cache */
	protected $cache;

	/** @var Manager */
	protected $manager;

	/** @var string */
	protected $table;




	/**
	 * Selection constructor.
	 * @param  Context
	 * @param  IConventions
	 * @param  string  table name
	 * @param  Nette\Caching\IStorage|NULL
	 */
	public function __construct(Context $context, IConventions $conventions, $table, Manager $manager, Nette\Caching\IStorage $cacheStorage = NULL)
	{
		parent::__construct($context, $conventions, $table, $cacheStorage);

		$this->table = $table;
		$this->manager = $manager;
	}



	/**
	 * @return Manager
	 */
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
		$this->manager->setRowClass($class);
		return $this;
	}



	public function getRowClass()
	{
		return $this->manager->getRowClass();
	}



	protected function createRow(array $row)
	{
		return $this->manager->initEntity($row, $this);
	}



	public function createSelectionInstance($table = NULL)
	{
		return new Selection($this->context, $this->conventions, $table ?: $this->table, $this->manager);
	}



	protected function createGroupedSelectionInstance($table, $column)
	{
		return new GroupedSelection($this->context, $this->conventions,  $table, $column, $this, $this->manager);
	}

}
