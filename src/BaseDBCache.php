<?php

namespace DenisBuyakov\DBCache;

abstract class BaseDBCache
{
	protected $currentData;
	protected $oldData;
	protected $primaryColumn;


	public function __construct()
	{
		$this->primaryColumn = $this->initPrimaryColumn();
		$this->currentData = $this->oldData = $this->getDataFromDB();
	}

	protected abstract function getDataFromDB(): array;

	protected abstract function initPrimaryColumn(): string;

	public function add(array $item): void
	{
		if ($this->validate($item))
			$this->currentData[$item[$this->primaryColumn]] = $item;
	}

	protected abstract function validate(array $item): bool;

	public function check(string $item): bool
	{
		if (isset($this->currentData[$item]))
			return true;
		else
			return false;
	}

	public function delete(string $item): void
	{
		unset($this->currentData[$item]);
	}

	public function saveDataToDB() : void
	{
		$this->deleteItemsFromDb($this->getDeletedItems());
		$this->updateItemsFromDb($this->getUpdatedItems());
		$this->insertItemsInDb( $this->getNewItems());

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

	protected abstract function deleteItemsFromDb(array $data): void;

	protected abstract function updateItemsFromDb(array $data): void;

	protected abstract function insertItemsInDb(array $data): void;


}