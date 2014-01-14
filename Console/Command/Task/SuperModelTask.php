<?php

/**
 * SuperBake Shell script - superModel Task - Generates models
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @package       ELCMS.superBake.Task
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @version       0.3
 *
 * This file is based on the lib/Cake/Console/Command/Task/ModelTask.php file
 * from CakePHP.
 *
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
// SbShell from superBake
App::uses('SbShell', 'Sb.Console/Command');
// Bake from superBake
App::uses('BakeTask', 'Sb.Console/Command/Task');

// ConnectionManager (to access DB) from Cake
App::uses('ConnectionManager', 'Model');
// Model from Cake
App::uses('Model', 'Model');
// Validation utility from Cake
App::uses('Validation', 'Utility');

/**
 * Task class for creating and updating model files.
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
	public $tasks = array('DbConfig', 'Fixture', 'Test', 'Sb.Template');

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

	/**
	 * Holds the project configuration array
	 * @var array
	 */
	public $Sbc;

	/**
	 * Holds some params (as theme)
	 * @var array
	 */
	public $params = array();

	/**
	 * Current part name
	 * @var string
	 */
	public $currentPart = null;

	/**
	 * Override initialize
	 *
	 * Unmodified method.
	 *
	 * @return void
	 */
	public function initialize() {
		$this->path = current(App::path('Model'));
	}

	/**
	 * Execution method always used for tasks
	 *
	 * Note: no parent::execute() is used as arguments are handled in the shell.
	 *
	 * @return void
	 */
	public function execute() {
		// DB connection to use
		$this->connection = $this->Sbc->getConfig('general.dbConnection');

		// Model name
		$model = $this->Sbc->getConfig('plugins.' . $this->Sbc->pluginName($this->plugin) . '.parts.' . $this->currentPart . '.model.name');

		// Lists the tables
		$this->listAll($this->connection);
		// Use the table for current model
		$useTable = $this->getTable($model);
		$object = $this->_getModelObject($model, $useTable);

		// Bake the model
		if ($this->bake($object, false)) {
			// Bake fixtures and tests
			if ($this->_checkUnitTest()) {
				$this->bakeFixture($model, $useTable);
				$this->bakeTest($model);
			}
		}
	}

	/**
	 * Get a model object for a class name.
	 *
	 * Unmodified method.
	 *
	 * @todo: handle the $table argument in config file (issue #28)
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
	 * Handles Generation and user interaction for creating validation.
	 *
	 * Unmodified method.
	 *
	 * @param Model $model Model to have validations generated for.
	 * @return array $validate Array of user selected validations.
	 */
	public function doValidation($model) {
		if (!$model instanceof Model) {
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
	 * Unmodified method.
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
				$choices[$default] = $option;
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
	 * @todo Handle basic field validation in cofnig file
	 *
	 * @param string $fieldName Name of field to be validated.
	 * @param array $metaData metadata for field
	 * @param string $primaryKey
	 * @return array Array of validation for the field.
	 */
	public function fieldValidation($fieldName, $metaData, $primaryKey = 'id') {
		$defaultChoice = count($this->_validations);
		$validate = $alreadyChosen = array();

//		$anotherValidator = 'y';
//		while ($anotherValidator === 'y') {

//		$prompt = __d('cake_console', "... or enter in a valid regex validation string.\n");
		$methods = array_flip($this->_validations);
		$guess = $defaultChoice;
		if ($metaData['null'] != 1 && !in_array($fieldName, array($primaryKey, 'created', 'modified', 'updated'))) {
			if ($fieldName === 'email') {
				$guess = $methods['email'];
			} elseif ($metaData['type'] === 'string' && $metaData['length'] == 36) {
				$guess = $methods['uuid'];
			} elseif ($metaData['type'] === 'string') {
				$guess = $methods['notEmpty'];
			} elseif ($metaData['type'] === 'text') {
				$guess = $methods['notEmpty'];
			} elseif ($metaData['type'] === 'integer') {
				$guess = $methods['numeric'];
			} elseif ($metaData['type'] === 'float') {
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
//			$anotherValidator = 'n';
//		}
		return $validate;
	}

	/**
	 * Handles associations
	 *
	 * Same method as in the original file, with the "interactive" handling removed.
	 *
	 * @param Model $model
	 * @return array Associations
	 */
	public function doAssociations($model) {
		if (!$model instanceof Model) {
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

		unset($associations['hasOne']);

		return $associations;
	}

	/**
	 * Find belongsTo relations and add them to the associations list.
	 *
	 * Unmodified method.
	 *
	 * @param Model $model Model instance of model being generated.
	 * @param array $associations Array of in progress associations
	 * @return array Associations with belongsTo added in.
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
	 * Find the hasOne and hasMany relations and add them to associations list
	 *
	 * Unmodified method.
	 *
	 * @param Model $model Model instance being generated
	 * @param array $associations Array of in progress associations
	 * @return array Associations with hasOne and hasMany added in.
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
	 * Unmodified method.
	 *
	 * @param Model $model Model instance being generated
	 * @param array $associations Array of in-progress associations
	 * @return array Associations with hasAndBelongsToMany added in.
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
	 * Interact with the user and confirm associations.
	 *
	 * Unmodified method.
	 *
	 * @param array $model Temporary Model instance.
	 * @param array $associations Array of associations to be confirmed.
	 * @return array Array of confirmed associations
	 */
	public function confirmAssociations(Model $model, $associations) {
		foreach ($associations as $type => $settings) {
			if (!empty($associations[$type])) {
				foreach ($associations[$type] as $i => $assoc) {
					$prompt = "{$model->name} {$type} {$assoc['alias']}?";
					$response = $this->in($prompt, array('y', 'n'), 'y');

					if (strtolower($response) === 'n') {
						unset($associations[$type][$i]);
					} elseif ($type === 'hasMany') {
						unset($associations['hasOne'][$i]);
					}
				}
				$associations[$type] = array_merge($associations[$type]);
			}
		}
		return $associations;
	}

	/**
	 * Assembles and writes a Model file.
	 *
	 * @todo : reimplement the doActAs() method from CakePHP in order to handle
	 * the different behaviors.
	 * @param string|object $name Model name or object
	 * @param array|boolean $data if array and $name is not an object assume bake data, otherwise boolean.
	 * @return string
	 */
	public function bake($name, $data = array()) {
		/* -------------------------------------------------------------------------
		 * Preparing data for template
		 * ---------------------------------------------------------------------- */
		if ($name instanceof Model) {
			if (!$data) {
				$data = array();
				// Associations
				$data['associations'] = $this->doAssociations($name);
				// Validation array
				$data['validate'] = $this->doValidation($name);
//				$data['actsAs'] = $this->doActsAs($name);
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
//			'actsAs' => array(),
				'validate' => array(),
				'primaryKey' => 'id',
				'useTable' => null,
				'useDbConfig' => 'default',
				'displayField' => $this->Sbc->getConfig('plugins.' . $this->Sbc->pluginName($this->plugin) . '.parts.' . $this->currentPart . '.model.displayField'),
		);
		$data = array_merge($defaults, $data);

		// Getting the file's target path
		$pluginPath = '';
		if ($this->plugin) {
			$pluginPath = $this->plugin . '.';
		}

		// Options
		foreach ($this->Sbc->getConfig('plugins.' . $this->Sbc->pluginName($this->plugin) . '.parts.' . $this->currentPart . '.model.options') as $option => $value) {
			$this->Template->set($option, $value);
		}

		/* -------------------------------------------------------------------------
		 * Making the prepared data available for template
		 * ---------------------------------------------------------------------- */

		//Sbc class
		$this->Template->Sbc = $this->Sbc;
		// Prepared data
		$this->Template->set($data);
		// Additionnal data:
		$this->Template->set(array(
				// Current plugin
				'plugin' => $this->plugin,
				'pluginPath' => $pluginPath,
				// Theme to use
				'theme' => $this->params['theme'],
				// Part name
				'part' => $this->currentPart,
				// Entire model config for a quicker access in templates
				'modelConfig' => $this->Sbc->getConfig('plugins.' . $this->Sbc->pluginName($this->plugin) . '.parts.' . $this->currentPart . '.model'),
		));

		/* -------------------------------------------------------------------------
		 * Generating and saving the file
		 * ---------------------------------------------------------------------- */

		$out = $this->Template->generate('classes', 'model');

		$path = $this->getPath();
		$filename = $path . $name . '.php';
		$this->speak(__d('cake_console', 'Baking model class for %s...', $name), 'info', 0, 0);
		$this->createFile($filename, $out);
		ClassRegistry::flush();
		return $out;
	}

	/**
	 * Returns the path where model should be created.
	 * @return string
	 */
	public function getPath() {
		$path = $this->path;
		if (isset($this->plugin)) {
			$path = $this->_pluginPath($this->plugin) . 'Model' . DS;
		}
		return $path;
	}

	/**
	 * Assembles and writes a unit test file
	 *
	 * Same method as in the original file, with the "interactive" handling removed.
	 *
	 * @param string $className Model class name
	 * @return string
	 */
	public function bakeTest($className) {
		// The only difference from original method is setting the interactive mode
		// to false.
		$this->Test->interactive = false;
		$this->Test->plugin = $this->plugin;
		$this->Test->connection = $this->connection;
		return $this->Test->bake('Model', $className);
	}

	/**
	 * outputs the a list of possible models or controllers from database
	 *
	 * Same method as in the original file, with the "interactive" handling removed.
	 *
	 * @param string $useDbConfig Database configuration name
	 * @return array
	 */
	public function listAll($useDbConfig = null) {
		$this->_tables = $this->getAllTables($useDbConfig);

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
	 * Same method as in the original file, with the "interactive" handling removed.
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
	 * Unmodified method.
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
			return $this->_stop();
		}
		sort($tables);
		return $tables;
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
		// The only difference from original method is setting the interactive mode
		// to false.
		$this->Fixture->interactive = false;
		$this->Fixture->connection = $this->connection;
		$this->Fixture->plugin = $this->plugin;
		$this->Fixture->bake($className, $useTable);
	}

}
