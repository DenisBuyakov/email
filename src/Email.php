<?php

namespace DenisBuyakov\DBCache;

use \Illuminate\Support\Facades\DB;

class Email extends BaseDBCache
{


	public function add($item): void
	{
		$array = [$this->primaryColumn => $item];
		parent::add($array);
	}

	protected function getDataFromDB(): array
	{
		$request = DB::table('emails')->get();
		$array= $request->toArray();
		return array_column($array, null, $this->primaryColumn);
	}

	protected function validate($item): bool
	{
		if (filter_var($item['address'], FILTER_VALIDATE_EMAIL))
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
		DB::table('emails')->whereIn('id', $ids_to_delete)->delete();
	}

	protected function updateItemsFromDb($data): void
	{
//		foreach ($data as $item) {
//			DB::table('email')->where('address', $item['address'])->update($item);
//		}
	}

	protected function insertItemsInDb($data): void
	{
		DB::table('emails')->insert($data);
	}
}