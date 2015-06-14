<?php

use Illuminate\Database\Seeder;
use App\Models\Occasion;

class OccasionsTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Occasion::truncate();

		$day = 18;
		$month = 6;
		$wiki = qp('https://en.wikipedia.org/wiki/June_18');

		$events = explode("\n", trim(qp($wiki->find('#Events')->parent())->next()->text()));
		foreach ($events as $event)
		{
			if ($occasion = $this->convertWikipediaTextToOccasion($day, $month, Occasion::EVENT, $event))
			{
				$occasion->save();
			}
		}

		$events = explode("\n", trim(qp($wiki->find('#Births')->parent())->next()->text()));
		foreach ($events as $event)
		{
			if ($occasion = $this->convertWikipediaTextToOccasion($day, $month, Occasion::BIRTH, $event))
			{
				$occasion->save();
			}
		}

		$events = explode("\n", trim(qp($wiki->find('#Deaths')->parent())->next()->text()));
		foreach ($events as $event)
		{
			if ($occasion = $this->convertWikipediaTextToOccasion($day, $month, Occasion::DEATH, $event))
			{
				$occasion->save();
			}
		}

		$holidays = qp($wiki->find('#Holidays_and_observances')->parent());
		while ($holidays = $holidays->next())
		{
			$holidays->find('li a[title="Feast Day"]')->parent()->remove();
			if ($holidays->get(0)->nodeName != 'ul')
			{
				break;
			}
			$events = explode("\n", trim($holidays->text()));
			foreach ($events as $event)
			{
				if ($occasion = $this->convertWikipediaTextToOccasion($day, $month, Occasion::HOLIDAY, $event))
				{
					$occasion->save();
				}
			}
		}
	}


	private function convertWikipediaTextToOccasion($day, $month, $type, $text, $language = 'en')
	{
		if (!$text) return null;

		$occasion = new Occasion();

		$occasion->day = $day;
		$occasion->month = $month;
		$occasion->type = $type;
		$occasion->language = $language;

		$parsed = explode('–', $text);

		$year = null;
		if (count($parsed) == 2 && is_numeric($year = intval(trim($parsed[0]))))
		{
			$occasion->year = $year;
			array_shift($parsed);
		}

		$occasion->fullDescription = trim($parsed[0]);

		if ($occasion->type == Occasion::HOLIDAY)
		{
			if (preg_match('/(.+)\((.+)\)/', $occasion->fullDescription, $matches))
			{
				$occasion->subject = trim($matches[1]);
				$occasion->subjectDetail = trim($matches[2]);
			}
		}
		else if ($occasion->type == Occasion::DEATH || $occasion->type == Occasion::BIRTH)
		{
			if (preg_match('/(.+),(.+)/', $occasion->fullDescription, $matches))
			{
				$occasion->subject = trim($matches[1]);
				$occasion->subjectDetail = trim($matches[2]);
			}
			if (preg_match('/(.+)\((.+)\)/', $occasion->subjectDetail , $matches))
			{
				$occasion->subjectDetail = trim($matches[1]);
			}
		}
		
		return $occasion;
	}

}
