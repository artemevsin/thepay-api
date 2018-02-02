<?php
declare(strict_types=1);

namespace Tp\DataApi\Processors;

use Tp\Utils;

abstract class Processor
{
	public static function process(array $input) : array
	{
		$instance = new static;
		// Start with an empty path [].
		$processed = $instance->processHash($input, []);

		return $processed;
	}

	/**
	 * @param array    $value
	 * @param string[] $currentPath
	 *
	 * @return array
	 */
	protected function processHash(array $value, array $currentPath) : array
	{
		$processed = [];
		foreach ($value as $key => $item) {
			// Every level deeper appends the currenty key to the path.
			$itemPath = array_merge($currentPath, [$key]);
			$processed[$key] = is_array($item)
				? $this->processItem($item, $itemPath)
				: $item;
		}

		return $processed;
	}

	/**
	 * @param array    $value
	 * @param string[] $currentPath
	 *
	 * @return array
	 */
	protected function processItem(array $value, array $currentPath) : array
	{
		if (Utils::isList($value)) {
			return $this->processList($value, $currentPath);
		}
		else {
			return $this->processHash($value, $currentPath);
		}
	}

	/**
	 * Seznamy položek mohou obsahovat složené hodnoty určené ku zjednodušení,
	 * avšak jejich číselné klíče se nevkládají jako součást cesty.
	 *
	 * @param array    $list
	 * @param string[] $currentPath
	 *
	 * @return array
	 */
	protected function processList(array $list, array $currentPath) : array
	{
		$processed = [];
		foreach ($list as $key => $value) {
			// Numeric list keys are not appended to the path.
			$processed[$key] = is_array($value)
				? $this->processItem($value, $currentPath)
				: $value;
		}

		return $processed;
	}
}