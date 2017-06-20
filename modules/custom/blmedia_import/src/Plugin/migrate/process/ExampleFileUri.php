<?php
/**
 * @file
 * Contains \Drupal\example_migrate\Plugin\migrate\process\ExampleFileUri.
 */
namespace Drupal\example_migrate\Plugin\migrate\process;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;


/**
 * Process the file url into a D8 compatible URL.
 *
 * @MigrateProcessPlugin(
 *   id = "example_file_uri"
 * )
 */
class ExampleFileUri extends ProcessPluginBase {
	/**
	 * {@inheritdoc} 
	 */
	public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
		list($filepath, $filename) = $value;
		$destination_base_uri = 'public://2017-06/';
		return $destination_base_uri . $value;
	}
}