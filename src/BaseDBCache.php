<?php

namespace DenisBuyakov\DBCache;

/**
 * абстрактный класс, задающий логику кеширования и обновления любых данных.
 *
 * Class BaseDBCache
 * @package DenisBuyakov\DBCache
 */
abstract class BaseDBCache
{
	/**
	 * Текущие данные
	 *
	 * @var array
	 */
	protected $currentData;

	/**
	 * Первоначальные данные
	 *
	 * @var
	 */
	protected $oldData;

	/**
	 * Стобец с уникальными индексными значениями
	 *
	 * @var string
	 *
	 */
	protected $primaryColumn;

	/**
	 * Конструктор задает уникальный столбец и получет данные.
	 *
	 * BaseDBCache constructor.
	 */
	public function __construct()
	{
		$this->primaryColumn = $this->initPrimaryColumn();
		$this->currentData = $this->oldData = $this->getDataFromDB();
	}

	/**
	 * Абстрактный метод задающий униклаьный столбец
	 *
	 * @return string
	 */
	protected abstract function initPrimaryColumn(): string;

	/**
	 * Абстрактный метод получающий данные
	 *
	 * @return array
	 */
	protected abstract function getDataFromDB(): array;

	/**
	 * Метод добавлящий новые данные в кеш.
	 *
	 * @param $item
	 */
	public function add($item): void
	{
		if ($this->validate($item))
			$this->currentData[$item[$this->primaryColumn]] = $item;
	}

	/**
	 * абстрактный метод проверяющий корректность данных
	 *
	 * @param array $item
	 * @return bool
	 */
	protected abstract function validate(array $item): bool;

	/**
	 * метод проверяет наличие уникального поля в кеше.
	 *
	 * @param string $item
	 * @return bool
	 */
	public function check(string $item): bool
	{
		if (isset($this->currentData[$item]))
			return true;
		else
			return false;
	}

	/**
	 * метод удаляет данные из кеша
	 *
	 * @param string $item
	 */
	public function delete(string $item): void
	{
		unset($this->currentData[$item]);
	}

	/**
	 * метод сохранят данные а бд
	 */
	public function saveDataToDB(): void
	{
		$this->deleteItemsFromDb($this->getDeletedItems());
		$this->updateItemsFromDb($this->getUpdatedItems());
		$this->insertItemsInDb($this->getNewItems());

	}

	/**
	 * абстрактный метод удаляющий данные из бд
	 *
	 * @param array $data
	 */
	protected abstract function deleteItemsFromDb(array $data): void;

	/**
	 * метод получаеющий список записией которые были удаленые за время существования класса
	 *
	 * @return array
	 */
	private function getDeletedItems(): array
	{
		return array_diff_key($this->oldData, $this->currentData);
	}

	/**
	 * абстрактный метод обновляющий данные в бд
	 *
	 * @param array $data
	 */
	protected abstract function updateItemsFromDb(array $data): void;

	/**
	 * метод полуючающий обновленные записи в кеше за время существования класса
	 *
	 * @return array
	 */
	private function getUpdatedItems(): array
	{
		$updatedItems = [];
		foreach (array_intersect_key($this->currentData, $this->oldData) as $item)
			if ($this->currentData[$item[$this->primaryColumn]] != $this->oldData[$item[$this->primaryColumn]])
				$updatedItems[] = $item;
		return $updatedItems;
	}

	/**
	 * абстрактный метод отвечающи за добовление новых данныех в бд
	 *
	 * @param array $data
	 */
	protected abstract function insertItemsInDb(array $data): void;

	/**
	 * формирует список новых елменентов в кеше появившихся за время существования класса
	 *
	 * @return array
	 */
	private function getNewItems(): array
	{
		return array_diff_key($this->currentData, $this->oldData);
	}


}