<?php

namespace DBCache\email;

class email extends BaseDBCache
{


	protected function getDataFromDB(): array
	{
		return array_column(DB::select('select * from emails'),null,$this->primaryColumn);
	}


	protected function validate($item): bool
	{
		if (filter_var($item, FILTER_VALIDATE_EMAIL))
			return true;
		else
			return false;
	}

	protected function initPrimaryColumn(): string
	{
		return 'address';
	}

	protected function deleteItemsFromDb($data): void
	{
		$ids_to_delete = array_column($data, 'id');
		DB::table('email')->whereIn('id', $ids_to_delete)->delete();
	}

	protected function updateItemsFromDb($data): void
	{
//		foreach ($data as $item) {
//			DB::table('email')->where('address', $item['address'])->update($item);
//		}
	}

	protected function insertItemsInDb($data): void
	{
		DB::table('email')->insert($data);
	}
}