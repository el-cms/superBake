<?php

/**
 * SuperBake Shell script - superModel Task - Generates models
 * 
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       EL-CMS/superBake
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 *
 * This file is based on the lib/Cake/Console/Command/Task/ModelTask.php file 
 * from CakePHP.
 * 
 * ----
 * 
 *  This file is part of EL-CMS.
 *
 *  EL-CMS is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  EL-CMS is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 * 
 *
 *  You should have received a copy of the GNU General Public License
 *  along with EL-CMS. If not, see <http://www.gnu.org/licenses/> 
 */
App::uses('AppShell', 'Console/Command');
App::uses('BakeTask', 'Console/Command/Task');
App::uses('ConnectionManager', 'Model');
App::uses('Model', 'Model');
App::uses('Validation', 'Utility');

/**
 * Task class for creating and updating model files.
 *
 * @package	   Cake.Console.Command.Task
 */
class SuperModelTask extends BakeTask {

	/**
	 * path to Model directory
	 *
	 * @var string
	 */
	public $path = null;

	/**
	 * tasks
	 *
	 * @var array
	 */
	public $tasks = array('DbConfig', 'Fixture', 'Test', 'Template');

	/**
	 * Tables to skip when running all()
	 *
	 * @var array
	 */
	public $skipTables = array('i18n');

	/**
	 * Holds tables found on connection.
	 *
	 * @var array
	 */
	protected $_tables = array();

	/**
	 * Holds the model names
	 *
	 * @var array
	 */
	protected $_modelNames = array();

	/**
	 * Holds validation method map.
	 *
	 * @var array
	 */
	protected $_validations = array();
	/* public $executeOptions = array(
	  'plugin' => null,
	  'model' => null,
	  'theme' => null,
	  'path' => null,
	  ); */
	public $currentPlugin = null;
	public $currentModel = null;
	public $projectConfig = array();
	public $currentModelConfig = array();

	/**
	 * Name of template used for generation
	 * @var string
	 */
	public $template = null;

	/**
	 * Override initialize
	 *
	 * @return void
	 */
	public function initialize() {
		$this->path = current(App::path('Model'));
	}

	/**
	 * Execution method always used for tasks
	 *
	 * @return void
	 */
	public function execute() {
		parent::execute();
		$this->plugin = $this->currentPlugin;
		$model = $this->currentModel;
		if (!isset($this->connection)) {
			$this->connection = 'default';
		}
		$model = $this->_modelName($model);
		$this->listAll($this->connection);
		$useTable = $this->getTable($model);
		$object = $this->_getModelObject($model, $useTable);
		if ($this->bake($object, false)) {
			if ($this->_checkUnitTest()) {
				$this->bakeFixture($model, $useTable);
				$this->bakeTest($model);
			}
		}
	}

	/**
	 * Get a model object for a class name.
	 *
	 * @param string $className Name of class you want model to be.
	 * @param string $table Table name
	 * @return Model Model instance
	 */
	protected function _getModelObject($className, $table = null) {
		if (!$table) {
			$table = Inflector::tableize($className);
		}
		$object = new Model(array('name' => $className, 'table' => $table, 'ds' => $this->connection));
		$fields = $object->schema(true);
		foreach ($fields as $name => $field) {
			if (isset($field['key']) && $field['key'] === 'primary') {
				$object->primaryKey = $name;
				break;
			}
		}
		return $object;
	}

	/**
	 * Generate a key value list of options and a prompt.
	 *
	 * @param array $options Array of options to use for the selections. indexes must start at 0
	 * @param string $prompt Prompt to use for options list.
	 * @param integer $default The default option for the given prompt.
	 * @return integer result of user choice.
	 */
	public function inOptions($options, $prompt = null, $default = null) {
		$valid = false;
		$max = count($options);
		while (!$valid) {
			$len = strlen(count($options) + 1);
			foreach ($options as $i => $option) {
				$this->out(sprintf("%${len}d. %s", $i + 1, $option));
			}
			if (empty($prompt)) {
				$prompt = __d('cake_console', 'Make a selection from the choices above');
			}
			$choice = $this->in($prompt, null, $default);
			if (intval($choice) > 0 && intval($choice) <= $max) {
				$valid = true;
			}
		}
		return $choice - 1;
	}

	/**
	 * Handles Generation and user interaction for creating validation.
	 *
	 * @param Model $model Model to have validations generated for.
	 * @return array $validate Array of user selected validations.
	 */
	public function doValidation($model) {
		if (!is_object($model)) {
			return false;
		}
		$fields = $model->schema();

		if (empty($fields)) {
			return false;
		}
		$validate = array();
		$this->initValidations();
		foreach ($fields as $fieldName => $field) {
			$validation = $this->fieldValidation($fieldName, $field, $model->primaryKey);
			if (!empty($validation)) {
				$validate[$fieldName] = $validation;
			}
		}
		return $validate;
	}

	/**
	 * Populate the _validations array
	 *
	 * @return void
	 */
	public function initValidations() {
		$options = $choices = array();
		if (class_exists('Validation')) {
			$options = get_class_methods('Validation');
		}
		sort($options);
		$default = 1;
		foreach ($options as $option) {
			if ($option{0} !== '_') {
				$choices[$default] = strtolower($option);
				$default++;
			}
		}
		$choices[$default] = 'none'; // Needed since index starts at 1
		$this->_validations = $choices;
		return $choices;
	}

	/**
	 * Does individual field validation handling.
	 *
	 * @param string $fieldName Name of field to be validated.
	 * @param array $metaData metadata for field
	 * @param string $primaryKey
	 * @return array Array of validation for the field.
	 */
	public function fieldValidation($fieldName, $metaData, $primaryKey = 'id') {
		$defaultChoice = count($this->_validations);
		$validate = $alreadyChosen = array();

		$anotherValidator = 'y';
		while ($anotherValidator === 'y') {
			$prompt = __d('cake_console', "... or enter in a valid regex validation string.\n");
			$methods = array_flip($this->_validations);
			$guess = $defaultChoice;
			if ($metaData['null'] != 1 && !in_array($fieldName, array($primaryKey, 'created', 'modified', 'updated'))) {
				if ($fieldName === 'email') {
					$guess = $methods['email'];
				} elseif ($metaData['type'] === 'string' && $metaData['length'] == 36) {
					$guess = $methods['uuid'];
				} elseif ($metaData['type'] === 'string') {
					$guess = $methods['notempty'];
				} elseif ($metaData['type'] === 'text') {
					$guess = $methods['notempty'];
				} elseif ($metaData['type'] === 'integer') {
					$guess = $methods['numeric'];
				} elseif ($metaData['type'] === 'boolean') {
					$guess = $methods['boolean'];
				} elseif ($metaData['type'] === 'date') {
					$guess = $methods['date'];
				} elseif ($metaData['type'] === 'time') {
					$guess = $methods['time'];
				} elseif ($metaData['type'] === 'datetime') {
					$guess = $methods['datetime'];
				} elseif ($metaData['type'] === 'inet') {
					$guess = $methods['ip'];
				}
			}

			$choice = $guess;
			if (isset($this->_validations[$choice])) {
				$validatorName = $this->_validations[$choice];
			} else {
				$validatorName = Inflector::slug($choice);
			}

			if ($choice != $defaultChoice) {
				$validate[$validatorName] = $choice;
				if (is_numeric($choice) && isset($this->_validations[$choice])) {
					$validate[$validatorName] = $this->_validations[$choice];
				}
			}
			$anotherValidator = 'n';
		}
		return $validate;
	}

	/**
	 * Handles associations
	 *
	 * @param Model $model
	 * @return array $associations
	 */
	public function doAssociations($model) {
		if (!is_object($model)) {
			return false;
		}

		$fields = $model->schema(true);
		if (empty($fields)) {
			return array();
		}

		if (empty($this->_tables)) {
			$this->_tables = (array) $this->getAllTables();
		}

		$associations = array(
			'belongsTo' => array(),
			'hasMany' => array(),
			'hasOne' => array(),
			'hasAndBelongsToMany' => array()
		);

		$associations = $this->findBelongsTo($model, $associations);
		$associations = $this->findHasOneAndMany($model, $associations);
		$associations = $this->findHasAndBelongsToMany($model, $associations);

		return $associations;
	}

	/**
	 * Find belongsTo relations and add them to the associations list.
	 *
	 * @param Model $model Model instance of model being generated.
	 * @param array $associations Array of in progress associations
	 * @return array $associations with belongsTo added in.
	 */
	public function findBelongsTo(Model $model, $associations) {
		$fieldNames = array_keys($model->schema(true));
		foreach ($fieldNames as $fieldName) {
			$offset = strpos($fieldName, '_id');
			if ($fieldName != $model->primaryKey && $fieldName !== 'parent_id' && $offset !== false) {
				$tmpModelName = $this->_modelNameFromKey($fieldName);
				$associations['belongsTo'][] = array(
					'alias' => $tmpModelName,
					'className' => $tmpModelName,
					'foreignKey' => $fieldName,
				);
			} elseif ($fieldName === 'parent_id') {
				$associations['belongsTo'][] = array(
					'alias' => 'Parent' . $model->name,
					'className' => $model->name,
					'foreignKey' => $fieldName,
				);
			}
		}
		return $associations;
	}

	/**
	 * Find the hasOne and HasMany relations and add them to associations list
	 *
	 * @param Model $model Model instance being generated
	 * @param array $associations Array of in progress associations
	 * @return array $associations with hasOne and hasMany added in.
	 */
	public function findHasOneAndMany(Model $model, $associations) {
		$foreignKey = $this->_modelKey($model->name);
		foreach ($this->_tables as $otherTable) {
			$tempOtherModel = $this->_getModelObject($this->_modelName($otherTable), $otherTable);
			$tempFieldNames = array_keys($tempOtherModel->schema(true));

			$pattern = '/_' . preg_quote($model->table, '/') . '|' . preg_quote($model->table, '/') . '_/';
			$possibleJoinTable = preg_match($pattern, $otherTable);
			if ($possibleJoinTable) {
				continue;
			}
			foreach ($tempFieldNames as $fieldName) {
				$assoc = false;
				if ($fieldName != $model->primaryKey && $fieldName == $foreignKey) {
					$assoc = array(
						'alias' => $tempOtherModel->name,
						'className' => $tempOtherModel->name,
						'foreignKey' => $fieldName
					);
				} elseif ($otherTable == $model->table && $fieldName === 'parent_id') {
					$assoc = array(
						'alias' => 'Child' . $model->name,
						'className' => $model->name,
						'foreignKey' => $fieldName
					);
				}
				if ($assoc) {
					$associations['hasOne'][] = $assoc;
					$associations['hasMany'][] = $assoc;
				}
			}
		}
		return $associations;
	}

	/**
	 * Find the hasAndBelongsToMany relations and add them to associations list
	 *
	 * @param Model $model Model instance being generated
	 * @param array $associations Array of in-progress associations
	 * @return array $associations with hasAndBelongsToMany added in.
	 */
	public function findHasAndBelongsToMany(Model $model, $associations) {
		$foreignKey = $this->_modelKey($model->name);
		foreach ($this->_tables as $otherTable) {
			$tableName = null;
			$offset = strpos($otherTable, $model->table . '_');
			$otherOffset = strpos($otherTable, '_' . $model->table);

			if ($offset !== false) {
				$tableName = substr($otherTable, strlen($model->table . '_'));
			} elseif ($otherOffset !== false) {
				$tableName = substr($otherTable, 0, $otherOffset);
			}
			if ($tableName && in_array($tableName, $this->_tables)) {
				$habtmName = $this->_modelName($tableName);
				$associations['hasAndBelongsToMany'][] = array(
					'alias' => $habtmName,
					'className' => $habtmName,
					'foreignKey' => $foreignKey,
					'associationForeignKey' => $this->_modelKey($habtmName),
					'joinTable' => $otherTable
				);
			}
		}
		return $associations;
	}

	/**
	 * Interact with the user and generate additional non-conventional associations
	 *
	 * @param Model $model Temporary model instance
	 * @param array $associations Array of associations.
	 * @return array Array of associations.
	 */
	public function doMoreAssociations(Model $model, $associations) {
		$prompt = __d('cake_console', 'Would you like to define some additional model associations?');
		$wannaDoMoreAssoc = $this->in($prompt, array('y', 'n'), 'n');
		$possibleKeys = $this->_generatePossibleKeys();
		while (strtolower($wannaDoMoreAssoc) === 'y') {
			$assocs = array('belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany');
			$this->out(__d('cake_console', 'What is the association type?'));
			$assocType = intval($this->inOptions($assocs, __d('cake_console', 'Enter a number')));

			$this->out(__d('cake_console', "For the following options be very careful to match your setup exactly.\n" .
							"Any spelling mistakes will cause errors."));
			$this->hr();

			$alias = $this->in(__d('cake_console', 'What is the alias for this association?'));
			$className = $this->in(__d('cake_console', 'What className will %s use?', $alias), null, $alias);

			if ($assocType === 0) {
				if (!empty($possibleKeys[$model->table])) {
					$showKeys = $possibleKeys[$model->table];
				} else {
					$showKeys = null;
				}
				$suggestedForeignKey = $this->_modelKey($alias);
			} else {
				$otherTable = Inflector::tableize($className);
				if (in_array($otherTable, $this->_tables)) {
					if ($assocType < 3) {
						if (!empty($possibleKeys[$otherTable])) {
							$showKeys = $possibleKeys[$otherTable];
						} else {
							$showKeys = null;
						}
					} else {
						$showKeys = null;
					}
				} else {
					$otherTable = $this->in(__d('cake_console', 'What is the table for this model?'));
					$showKeys = $possibleKeys[$otherTable];
				}
				$suggestedForeignKey = $this->_modelKey($model->name);
			}
			if (!empty($showKeys)) {
				$this->out(__d('cake_console', 'A helpful List of possible keys'));
				$foreignKey = $this->inOptions($showKeys, __d('cake_console', 'What is the foreignKey?'));
				$foreignKey = $showKeys[intval($foreignKey)];
			}
			if (!isset($foreignKey)) {
				$foreignKey = $this->in(__d('cake_console', 'What is the foreignKey? Specify your own.'), null, $suggestedForeignKey);
			}
			if ($assocType === 3) {
				$associationForeignKey = $this->in(__d('cake_console', 'What is the associationForeignKey?'), null, $this->_modelKey($model->name));
				$joinTable = $this->in(__d('cake_console', 'What is the joinTable?'));
			}
			$associations[$assocs[$assocType]] = array_values((array) $associations[$assocs[$assocType]]);
			$count = count($associations[$assocs[$assocType]]);
			$i = ($count > 0) ? $count : 0;
			$associations[$assocs[$assocType]][$i]['alias'] = $alias;
			$associations[$assocs[$assocType]][$i]['className'] = $className;
			$associations[$assocs[$assocType]][$i]['foreignKey'] = $foreignKey;
			if ($assocType === 3) {
				$associations[$assocs[$assocType]][$i]['associationForeignKey'] = $associationForeignKey;
				$associations[$assocs[$assocType]][$i]['joinTable'] = $joinTable;
			}
			$wannaDoMoreAssoc = $this->in(__d('cake_console', 'Define another association?'), array('y', 'n'), 'y');
		}
		return $associations;
	}

	/**
	 * Finds all possible keys to use on custom associations.
	 *
	 * @return array array of tables and possible keys
	 */
	protected function _generatePossibleKeys() {
		$possible = array();
		foreach ($this->_tables as $otherTable) {
			$tempOtherModel = new Model(array('table' => $otherTable, 'ds' => $this->connection));
			$modelFieldsTemp = $tempOtherModel->schema(true);
			foreach ($modelFieldsTemp as $fieldName => $field) {
				if ($field['type'] === 'integer' || $field['type'] === 'string') {
					$possible[$otherTable][] = $fieldName;
				}
			}
		}
		return $possible;
	}

	/**
	 * Assembles and writes a Model file.
	 *
	 * @param string|object $name Model name or object
	 * @param array|boolean $data if array and $name is not an object assume bake data, otherwise boolean.
	 * @return string
	 */
	public function bake($name, $data = array()) {
		if (is_object($name)) {
			if (!$data) {
				$data = array();
				$data['associations'] = $this->doAssociations($name);
				$data['validate'] = $this->doValidation($name);
			}
			$data['primaryKey'] = $name->primaryKey;
			$data['useTable'] = $name->table;
			$data['useDbConfig'] = $name->useDbConfig;
			$data['name'] = $name = $name->name;
		} else {
			$data['name'] = $name;
		}
		$defaults = array(
			'associations' => array(),
			'validate' => array(),
			'primaryKey' => 'id',
			'useTable' => null,
			'useDbConfig' => 'default',
			'displayField' => null
		);
		$data = array_merge($defaults, $data);

		$pluginPath = '';
		if ($this->plugin) {
			$pluginPath = $this->plugin . '.';
		}

		$this->Template->set($data);
		$this->Template->set(array(
			'plugin' => $this->plugin,
			'pluginPath' => $pluginPath,
			'theme' => $this->params['theme'],
			'projectConfig' => $this->projectConfig,
			'currentModelConfig' => $this->currentModelConfig,
		));
		//echo (var_export($this->currentModelConfig));
		$out = $this->Template->generate('classes', 'model');
		$path = $this->getModelPath();
		$filename = $path . $name . '.php';
		$this->hr();
		$this->out($filename, 1, Shell::QUIET);
		$this->hr();
		$this->out("\n" . __d('cake_console', 'Baking model class for %s...', $name), 1, Shell::QUIET);
		$this->createFile($filename, $out);
		ClassRegistry::flush();
		return $out;
	}

	/**
	 * Assembles and writes a unit test file
	 *
	 * @param string $className Model class name
	 * @return string
	 */
	public function bakeTest($className) {
		$this->Test->interactive = $this->interactive;
		$this->Test->plugin = $this->plugin;
		$this->Test->connection = $this->connection;
		return $this->Test->bake('Model', $className);
	}

	/**
	 * outputs the a list of possible models or controllers from database
	 *
	 * @param string $useDbConfig Database configuration name
	 * @return array
	 */
	public function listAll($useDbConfig = null) {
		$this->_tables = (array) $this->getAllTables($useDbConfig);

		$this->_modelNames = array();
		$count = count($this->_tables);
		for ($i = 0; $i < $count; $i++) {
			$this->_modelNames[] = $this->_modelName($this->_tables[$i]);
		}
		return $this->_tables;
	}

	/**
	 * Interact with the user to determine the table name of a particular model
	 *
	 * @param string $modelName Name of the model you want a table for.
	 * @param string $useDbConfig Name of the database config you want to get tables from.
	 * @return string Table name
	 */
	public function getTable($modelName, $useDbConfig = null) {
		$useTable = Inflector::tableize($modelName);
		if (in_array($modelName, $this->_modelNames)) {
			$modelNames = array_flip($this->_modelNames);
			$useTable = $this->_tables[$modelNames[$modelName]];
		}
		return $useTable;
	}

	/**
	 * Get an Array of all the tables in the supplied connection
	 * will halt the script if no tables are found.
	 *
	 * @param string $useDbConfig Connection name to scan.
	 * @return array Array of tables in the database.
	 */
	public function getAllTables($useDbConfig = null) {
		if (!isset($useDbConfig)) {
			$useDbConfig = $this->connection;
		}

		$tables = array();
		$db = ConnectionManager::getDataSource($useDbConfig);
		$db->cacheSources = false;
		$usePrefix = empty($db->config['prefix']) ? '' : $db->config['prefix'];
		if ($usePrefix) {
			foreach ($db->listSources() as $table) {
				if (!strncmp($table, $usePrefix, strlen($usePrefix))) {
					$tables[] = substr($table, strlen($usePrefix));
				}
			}
		} else {
			$tables = $db->listSources();
		}
		if (empty($tables)) {
			$this->err(__d('cake_console', 'Your database does not have any tables.'));
			$this->_stop();
		}
		return $tables;
	}

	/**
	 * get the option parser.
	 *
	 * @return void
	 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		return $parser->description(
						__d('cake_console', 'Bake models for plugins.')
		);
	}

	/**
	 * Interact with FixtureTask to automatically bake fixtures when baking models.
	 *
	 * @param string $className Name of class to bake fixture for
	 * @param string $useTable Optional table name for fixture to use.
	 * @return void
	 * @see FixtureTask::bake
	 */
	public function bakeFixture($className, $useTable = null) {
		$this->Fixture->interactive = $this->interactive;
		$this->Fixture->connection = $this->connection;
		$this->Fixture->plugin = $this->plugin;
		$this->Fixture->bake($className, $useTable);
	}

	/**
	 * Gets the path for models. Checks the plugin property
	 * and returns the correct path.
	 *
	 * Orig. function from BakeTask.php (getPath())
	 * @return string Path to output.
	 */
	public function getModelPath() {
		$path = $this->path;
		if (isset($this->plugin)) {
			//$path = $this->_pluginPath($this->plugin) . $this->name . DS;
			$path = $this->_pluginPath($this->plugin) . 'Model' . DS;
		}
		return $path;
	}

}
