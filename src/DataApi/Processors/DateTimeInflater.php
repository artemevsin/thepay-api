<?php
declare(strict_types=1);

namespace Tp\DataApi\Processors;

use DateTime;
use Tp\InvalidParameterException;

class DateTimeInflater extends ProcessorWithPaths
{

	/**
	 * @param mixed    $value
	 * @param string[] $currentPath
	 *
	 * @return mixed
	 * @throws InvalidParameterException
	 */
	protected function processItem($value, array $currentPath) : array
	{
		$isNull = is_null($value);
		if ($isNull) {
			$processed = parent::processItem($value, $currentPath);
		}
		else {
			$onPath = $this->onPath($currentPath);
			if ($onPath) {
				// Pozor, neprojde, pokud časové razítko obsahuje desetinnou část
				// vteřin. Viz https://bugs.php.net/bug.php?id=51950.
				$processed = DateTime::createFromFormat(DateTime::ISO8601, $value);
				if ($processed === FALSE) {
					$errorPathArray = $currentPath;
					array_unshift($errorPathArray, '');
					$errorPathString = implode('/', $errorPathArray);
					throw new InvalidParameterException($errorPathString);
				}
			}
			else {
				$processed = parent::processItem($value, $currentPath);
			}
		}

		return $processed;
	}

}
