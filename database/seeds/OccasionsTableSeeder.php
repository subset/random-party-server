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

		for ($month = 1; $month <= 12; $month++)
		{
			for ($day = 1; $day <= cal_days_in_month(CAL_GREGORIAN, $month, date('Y')); $day++)
			{
				echo  PHP_EOL . 'Processing ' . $day  . ' ' . date('F', mktime(0, 0, 0, $month, 1)) . PHP_EOL;
				$this->importFromWikipedia($day, $month);
			}
		}
	}


	private function importFromWikipedia($day, $month, $language = 'en')
	{
		$wiki = qp('https://' . $language . '.wikipedia.org/wiki/' . date('F', mktime(0, 0, 0, $month, 1)) . '_' . $day);

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

		$holidays = qp($wiki->find('#Holidays_and_observances, #Holidays_and_observations')->parent());
		while ($holidays = $holidays->next())
		{
			$holidays->find('li a[title="Feast Day"]')->parent()->remove();
			$holidays->find('li a[title="Feast day"]')->parent()->remove();
			$holidays->find('li a[title="Calendar of saints"]')->parent()->remove();
			$holidays->find('li a[title="Calendar of Saints"]')->parent()->remove();
			$holidays->find('li a[title="Calendar Of Saints"]')->parent()->remove();
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
		echo '#';
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
			if (preg_match('/(.+)\((.+)\)/', $occasion->subject , $matches))
			{
				$occasion->subject = trim($matches[1]);
			}
			if (preg_match('/(.+)\((.+)\)/', $occasion->subjectDetail , $matches))
			{
				$occasion->subjectDetail = trim($matches[1]);
			}
		}
		
		return $occasion;
	}

}
