<?php

namespace CSVMapper;

use CSVMapper\Configuration\SettingManager;
use CSVMapper\Configuration\Yaml\YamlSettingManager;
use CSVMapper\Configuration\MappingManager;
use CSVMapper\Configuration\Yaml\YamlMappingManager;
use CSVMapper\Configuration\ErrorManager;
use CSVMapper\Exception\WrongColumnsNumberException;
use CSVMapper\Exception\ConfigurationMissingExcepion;

class CsvTest extends \PHPUnit_Framework_TestCase
{
	private $expected_table;

	/*
	* set up the expected resultset
	*/
	protected function setUp()
	{
		$this->expected_table = array
		(
			array('month' => '01', 'year' => '2013', 'temperature' => 0.2,  'fixed_field' => 'default_value'),
			array('month' => '02', 'year' => '2013', 'temperature' => -1.5, 'fixed_field' => 'default_value'),
			array('month' => '03', 'year' => '2013', 'temperature' => 2.4,  'fixed_field' => 'default_value'),
			array('month' => '04', 'year' => '2013', 'temperature' => 8.7,  'fixed_field' => 'default_value'),
			array('month' => '05', 'year' => '2013', 'temperature' => 15.6, 'fixed_field' => 'default_value'),
			array('month' => '06', 'year' => '2013', 'temperature' => 20.4, 'fixed_field' => 'default_value'),
			array('month' => '07', 'year' => '2013', 'temperature' => 25.3, 'fixed_field' => 'default_value'),
			array('month' => '08', 'year' => '2013', 'temperature' => 26.0, 'fixed_field' => 'default_value'),
			array('month' => '09', 'year' => '2013', 'temperature' => 22.2, 'fixed_field' => 'default_value'),
			array('month' => '10', 'year' => '2013', 'temperature' => 15.2, 'fixed_field' => 'default_value'),
			array('month' => '11', 'year' => '2013', 'temperature' => 8.2,  'fixed_field' => 'default_value'),
			array('month' => '12', 'year' => '2013', 'temperature' => 0.2,  'fixed_field' => 'default_value')
		);
	}

	/*
	* test a correct mapping with columns number limit and explicit delimiter
	*/
	public function testCorrectMapping()
	{
		$csv = new Csv();
		$config = new SettingManager();
		$mapping = new MappingManager();
		$error = new ErrorManager();

		$config->set_setting('folder','./tests');
		$config->set_setting('filename','temperatures.csv');
		$config->set_setting('separator',';');
		$config->set_setting('columns_allowed',3);

		$mapping->set_mapping("month",			array('key'=>0, 'fn'=>create_function('$input','return strlen($input) == 1?"0".$input:$input;'),'test'=>create_function('$input','return is_numeric($input);')));
		$mapping->set_mapping("year",			array('key'=>1, 'fn'=>FALSE,'test'=>FALSE));
		$mapping->set_mapping("temperature",	array('key'=>2, 'fn'=>create_function('$input','return floatval($input);'),'test'=>FALSE));
        $mapping->set_mapping("fixed_field",	array('key'=>NULL, 'value'=>'default_value', 'fn'=>FALSE, 'test'=>FALSE));
		
		$csv->set_mapping_manager($mapping);
		$csv->set_setting_manager($config);
		$csv->set_error_manager($error);

		$result = $csv->looper();

		$this->assertEquals($result,$this->expected_table);
	}

	/*
	* test a correct mapping with columns number limit and default delimiter
	*/
	public function testCorrectMappingDefaultSeparator()
	{
		$csv = new Csv();
		$config = new SettingManager();
		$mapping = new MappingManager();
		$error = new ErrorManager();

		$config->set_setting('folder','./tests');
		$config->set_setting('filename','temperatures.csv');
		$config->set_setting('columns_allowed',3);

		$mapping->set_mapping("month",			array('key'=>0, 'fn'=>create_function('$input','return strlen($input) == 1?"0".$input:$input;'),'test'=>create_function('$input','return is_numeric($input);')));
		$mapping->set_mapping("year",			array('key'=>1, 'fn'=>FALSE,'test'=>FALSE));
		$mapping->set_mapping("temperature",	array('key'=>2, 'fn'=>create_function('$input','return floatval($input);'),'test'=>FALSE));
        $mapping->set_mapping("fixed_field",	array('key'=>NULL, 'value'=>'default_value', 'fn'=>FALSE, 'test'=>FALSE));
		
		$csv->set_mapping_manager($mapping);
		$csv->set_setting_manager($config);
		$csv->set_error_manager($error);

		$result = $csv->looper();

		$this->assertEquals($result,$this->expected_table);
	}

	/*
	* test a correct mapping without columns number limit and with default delimiter
	*/
	public function testCorrectMappingNoColumnsNumberBound()
	{
		$csv = new Csv();
		$config = new SettingManager();
		$mapping = new MappingManager();
		$error = new ErrorManager();

		$config->set_setting('folder','./tests');
		$config->set_setting('filename','temperatures.csv');

		$mapping->set_mapping("month",			array('key'=>0, 'fn'=>create_function('$input','return strlen($input) == 1?"0".$input:$input;'),'test'=>create_function('$input','return is_numeric($input);')));
		$mapping->set_mapping("year",			array('key'=>1, 'fn'=>FALSE,'test'=>FALSE));
		$mapping->set_mapping("temperature",	array('key'=>2, 'fn'=>create_function('$input','return floatval($input);'),'test'=>FALSE));
        $mapping->set_mapping("fixed_field",	array('key'=>NULL, 'value'=>'default_value', 'fn'=>FALSE, 'test'=>FALSE));
		
		$csv->set_mapping_manager($mapping);
		$csv->set_setting_manager($config);
		$csv->set_error_manager($error);

		$result = $csv->looper();

		$this->assertEquals($result,$this->expected_table);
	}

	/**
    * @expectedException CSVMapper\Exception\WrongColumnsNumberException
    */
	public function testNumColsException()
	{
		$csv = new Csv();
		$config = new SettingManager();
		$error = new ErrorManager();

		$config->set_setting('folder','./tests');
		$config->set_setting('filename','temperatures.csv');
		$config->set_setting('separator',';');
		$config->set_setting('columns_allowed',4);

		$csv->set_setting_manager($config);

		$csv->looper();
	}

	/**
    * @expectedException CSVMapper\Exception\ConfigurationMissingExcepion
    */
	public function testConfigException()
	{
		$csv = new Csv();
		$config = new SettingManager();

		$config->set_setting('filename','temperatures.csv');

		$csv->set_setting_manager($config);

		$csv->looper();
	}

	/**
    * @expectedException CSVMapper\Exception\ConfigurationMissingExcepion
    */
	public function testConfigException2()
	{
		$csv = new Csv();
		$config = new SettingManager();

		$config->set_setting('folder','./tests');

		$csv->set_setting_manager($config);

		$csv->looper();
	}
        
    /**
     * 
     */
    public function testYamlSetting()
    {
		$csv = new Csv();
		$config = new YamlSettingManager('./tests/tempMappings.yml');
        $mapping = new MappingManager();
        $error = new ErrorManager();
	            
		$mapping->set_mapping("month",			array('key'=>0, 'fn'=>create_function('$input','return strlen($input) == 1?"0".$input:$input;'),'test'=>create_function('$input','return is_numeric($input);')));
		$mapping->set_mapping("year",			array('key'=>1, 'fn'=>FALSE,'test'=>FALSE));
		$mapping->set_mapping("temperature",	array('key'=>2, 'fn'=>create_function('$input','return floatval($input);'),'test'=>FALSE));
	    $mapping->set_mapping("fixed_field",	array('key'=>NULL, 'value'=>'default_value', 'fn'=>FALSE, 'test'=>FALSE));
	            
		$csv->set_mapping_manager($mapping);
		$csv->set_setting_manager($config);
		$csv->set_error_manager($error);

		$result = $csv->looper();

		$this->assertEquals($result,$this->expected_table);
    }

    /**
     * 
     */
    public function testYamlMapping()
    {
		$csv = new Csv();
		$config = new SettingManager();
		$mapping = new YamlMappingManager('./tests/tempMappings.yml');
		$error = new ErrorManager();
	            
		$config->set_setting('folder','./tests');
		$config->set_setting('filename','temperatures.csv');
		$config->set_setting('separator',';');
		$config->set_setting('columns_allowed',3);

		$csv->set_mapping_manager($mapping);
		$csv->set_setting_manager($config);
		$csv->set_error_manager($error);

		$result = $csv->looper();

		$this->assertEquals($result,$this->expected_table);
    }

    
    public function testRemoveQuotes()
    {
        $csv = new Csv();
        
        $this->assertEquals($csv->remove_quotes('"2"'), 2);
    }
}