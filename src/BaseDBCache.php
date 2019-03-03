<?php

namespace DenisBuyakov\DBCache;

abstract class BaseDBCache
{
	protected $currentData;
	private $oldData;
	protected $primaryColumn;


	public function __construct()
	{
		$this->primaryColumn = $this->initPrimaryColumn();
		$this->currentData = $this->oldData = $this->getDataFromDB();
	}

	protected abstract function getDataFromDB(): array;

	protected abstract function initPrimaryColumn(): string;

	public function add($item): void
	{
		if ($this->validate($item) and !$this->check($item[]))
			$this->currentData[$item[$this->primaryColumn]] = $item;
	}

	protected abstract function validate($item): bool;

	function check($item): bool
	{
		if (isset($this->curentData[$item]))
			return true;
		else
			return false;
	}

	public function delete($item): void
	{
		unset($this->currentData[$item]);
	}

	public function saveDataToDB()
	{
		$deletedItems = $this->getDeletedItems();
		$newItems = $this->getNewItems();
		$updatedItems = $this->getUpdatedItems();
	}

	private function getDeletedItems(): array
	{
		return array_diff_key($this->oldData, $this->currentData);
	}

	private function getNewItems(): array
	{
		return array_diff_key($this->currentData, $this->oldData);
	}

	private function getUpdatedItems(): array
	{
		$updatedItems = [];
		foreach (array_intersect_key($this->currentData, $this->oldData) as $item)
			if ($this->currentData[$item[$this->primaryColumn]] != $this->oldData[$item[$this->primaryColumn]])
				$updatedItems[] = $item;
		return $updatedItems;
	}

	protected abstract function deleteItemsFromDb($data): void;

	protected abstract function updateItemsFromDb($data): void;

	protected abstract function insertItemsInDb($data): void;


}