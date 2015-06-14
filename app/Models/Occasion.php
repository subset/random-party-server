<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Occasion extends Model
{
	const EVENT = 'event';
	const BIRTH = 'birth';
	const DEATH = 'death';
	const HOLIDAY = 'holiday';

	public $timestamps = false;

	protected $appends = ['prominence'];

	public function getProminenceAttribute()
	{
		if ($this->type == Occasion::HOLIDAY)
		{
			return 1000000;
		}

		$currentYear = date('Y');
		$isCentury = ($currentYear - $this->year) > 0 && ($currentYear - $this->year) % 100 === 0;
		$isDecade = false;
		if (!$isCentury)
		{
			$isDecade = ($currentYear - $this->year) > 0 && ($currentYear - $this->year) % 10 === 0;
		}
		$prominence = intval($isCentury) * 1000000 + intval($isDecade) * 100000 + ($currentYear - $this->year) * 10;
		switch ($this->type)
		{
			case Occasion::DEATH:
				$prominence += 1;
				break;
			case Occasion::BIRTH:
				$prominence += 2;
				break;
			case Occasion::EVENT:
				$prominence += 3;
				break;
		}
		return $prominence;
	}

}
