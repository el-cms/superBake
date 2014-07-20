<?php

App::uses('SbShell', 'Sb.Console/Command');

/**
 * This class contains methods for graphical elements creation or data manipulation
 * during the baking process.
 *
 * You can add/modify mothods to fit your needs.
 *
 * It extends the TemplateTask Task, and is loaded instead of the TemplateTask
 *
 * Methods beginning with :
 * 		v_ are for views manipulations (mainly HTML widgets)
 * 		c_ are for controllers-related manipulation
 * 		s_ are for schemas-related manipulation
 *
 * The Sbc object is available in the class through $this->Sbc
 *
 * @copyright     Copyright 2012, Manuel Tancoigne (http://experimentslabs.com)
 * @author        Manuel Tancoigne <m.tancoigne@gmail.com>
 * @link          http://experimentslabs.com Experiments Labs
 * @license       GPL v3 (http://www.gnu.org/licenses/gpl.html)
 * @package       ELCMS.superBake.Templates.Default
 * @version       0.3
 * @author kure
 */
class Theme extends SbShell {
	/* ---------------------------------------------------------------------------
	 *
	 * Vars
	 *
	 * ------------------------------------------------------------------------- */

	/**
	 * variables to add to template scope
	 *
	 * @var array
	 */
	public $templateVars = array();

	/**
	 * Path to the Template directory.
	 *
	 * @var string
	 */
	public $templatePath = null;
	// Other models
	public $otherModels = array();

	/* ---------------------------------------------------------------------------
	 *
	 * Methods
	 *
	 * ------------------------------------------------------------------------- */

	/**
	 * Returns some infos on the current theme
	 *
	 * @return string Some theme info. Use it where you want.
	 */
	public function t_getThemeInfos() {
		return('Experiments Labs theme.');
	}

	/**
	 * Magic method for non-existing methods. Will return the first argument as-is ?
	 *
	 * @param string $name
	 * @param array $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments) {
		$this->speak(__d('sb', "The Theme::$name method does not exists. First argument will be returned as-is"), 'warning', 0);
		return $arguments[0];
	}

	/* ---------------------------------------------------------------------------
	 *
	 * Methods to work on schemas
	 *
	 * ------------------------------------------------------------------------- */

	/**
	 * Loads a model to access its properties during generation
	 *
	 * @param string $model Model name
	 * @param string $plugin Plugin name. If null, plugin will be determinated by the model.
	 */
	public function s_loadModel($model, $plugin = null) {
		if (!in_array($model, $this->otherModels)) {
			if (is_null($plugin)) {
				$plugin = $this->Sbc->getPluginName($this->Sbc->getModelPlugin($model));
			}
			App::uses($model, $plugin . '.Model');
			// Checks if Model has been loaded correctly
			if (!class_exists($model)) {
				$this->speak(__d('superBake', "Generate {$plugin}$model model first"), 'error', 0);
				$this->_stop();
			}
			$this->otherModels[$model] = ClassRegistry::init($model);
		}
	}

	/**
	 * Remove a given field from template vars fields list if it shoud be hidden.
	 *
	 * @param string $field Field name
	 * @param int $i Field key
	 */
	public function s_unsetHiddenField($field, $i) {
		if (is_array($this->templateVars['hiddenFields']) && in_array($field, $this->templateVars['hiddenFields'])) {
			// Removing from fields list
			$this->speak("Removing $field from field list.");
			unset($this->templateVars['fields'][$i]);
			return true;
		}
		return false;
	}

	/**
	 * Updates the fields list considering the fields to be hidden and the different
	 * language fields.
	 *
	 * Don't forget to update the "$schema" and "$fields" vars in your template
	 * after this method call :
	 *
	 * 	$schema=$this->templateVars['schema'];
	 * 	$fields=$this->templateVars['fields'];
	 *
	 * @param array $schema List of schema fields and config
	 * @return true
	 */
	public function s_prepareSchemaFields() {

		$i = 0;
		// Language fields (for summary)
		$languageFields = array();
		// File fields (for summary)
		$fileFields = array();
		foreach ($this->templateVars['schema'] as $field => $config) {

			//
			// Removing field or continue
			if (!$this->s_unsetHiddenField($field, $i)) {

				//
				// Checking for language field: field_[lng]
				if ($this->s_isLanguageField($field)) {

					// Preparing field
					$languageOptions = $this->s_getLanguageFieldProperties($field);

					$fieldName = $languageOptions['fieldName'];
					$lang = $languageOptions['lang'];

					// Replacing field in list
					if (!in_array($fieldName, $this->templateVars['fields'])) {
						$this->templateVars['fields'][$i] = $fieldName;
						// Copying field description:
						$this->templateVars['schema'][$fieldName] = $this->templateVars['schema'][$field];
					} else {
						unset($this->templateVars['fields'][$i]);
					}

					// Field subtype:
					$this->templateVars['schema'][$fieldName]['subType'] = 'language';
					// Fields options: languages
					$this->templateVars['schema'][$fieldName]['language']['langs'][] = $lang;

					// Removing original field from Schema:
					unset($this->templateVars['schema'][$field]);

					// Language fields configuration (for summary):
					$languageFields[$fieldName][] = $lang;
				}

				//
				// File field
				elseif ($this->s_isFileField($field)) {
					// Field subtype:
					$this->templateVars['schema'][$field]['subType'] = 'file';
					// Field name, for summary
					$fileFields[] = $field;
				}
				//
				// Other fields
				else {

				}
			}
			$i++;
		}

		//
		// Small summary:
		//
		if (!empty($languageFields)) {
			$this->speak("This model have " . count($languageFields) . " localised fields:", 'comment');
			foreach ($languageFields as $k => $v) {
				$this->speak(" - \"$k\" with languages " . implode(', ', $v), 'comment');
			}
		}
		if (!empty($fileFields)) {
			$this->speak("This model have " . count($fileFields) . " file fields:", 'comment');
			foreach ($fileFields as $k) {
				$this->speak(" - \"$k\"", 'comment');
			}
		}

		return true;
	}

	/**
	 * Returns the different string/fields to display for the linked fields of a given schema.
	 *
	 * @param array $model Main model name
	 * @param array $relation Relationship configuration
	 * @param boolean $hasOne If true, will output a correct field name for hasOne associations
	 *
	 * @return array List of shema related dependencies.
	 */
	public function s_prepareSchemaRelatedFields($model, $relation, $hasOne = false) {
		$i = 0;
		// Language fields (for summary)
		$languageFields = array();

		$i = 0;
		foreach ($relation['fields'] as $field) {

			//
			// Hidden field
			//
			if (isset($this->templateVars['assoc_hiddenModelFields'][Inflector::pluralize($model)])) {
				$hiddenFields = $this->templateVars['assoc_hiddenModelFields'][Inflector::pluralize($model)];
			} else {
				$hiddenFields = array();
			}
			if ($this->Sbc->getConfig('theme.removeSelfIdInAssociations') && $field == $relation['foreignKey']) {
				$hiddenFields[] = $relation['foreignKey'];
			}
			if (in_array($field, $hiddenFields)) {
				unset($relation['fields'][$i]);
			} else {
				// Field name
				if ($hasOne) {
					$fieldName = "\${$this->templateVars['singularVar']}['$model']['$field']";
				} else {
					$fieldName = "\$" . Inflector::variable($model) . "['$field']";
				}

				// String to display on views
				$displayString = "echo $fieldName;";

				// Language fields ?
				if ($this->s_isLanguageField($field)) {
					$languageOptions = $this->s_getLanguageFieldProperties($field);

					$fieldName = $languageOptions['fieldName'];
					$lang = $languageOptions['lang'];

					// Replacing in field list:
					if (!in_array($fieldName, $relation['fields'])) {
						$relation['fields'][$i] = $fieldName;
						$relation['fieldsOptions'][$fieldName]['subType'] = 'language';
					} else {
						unset($relation['fields'][$i]);
					}
					$relation['fieldsOptions'][$fieldName]['langs'][] = $lang;
				}
			}
			$i++;
		}
		return $relation;
	}

	/**
	 * Gets the relations for a given model and loads its related models.
	 *
	 * @param string $model Model name
	 * @return array List of belongsTo relations.
	 */
	public function s_getBelongsToAndLoadModels($model) {
		$this->s_loadModel($model, $this->Sbc->getModelPlugin($model));
		$assocs = $this->otherModels[$model]->belongsTo;
		$out = array();
		foreach ($assocs as $k => $v) {
			$this->s_loadModel($k, $this->Sbc->getModelPlugin($k));
			$out[$k] = $v;
		}
		return $out;
	}

	/**
	 * Determines if the current schema have a SFW field
	 *
	 * @param string $schema Schema name. Will use current schema name if null
	 *
	 * @return boolean
	 */
	public function s_haveSFW($schema = null) {
		if ($this->Sbc->getConfig('theme.sfw.useSFW')) {

			//Schema given: field list
			if (!is_null($schema)) {
				return in_array($this->Sbc->getConfig('theme.sfw.field'), $schema);
			} else {
				return array_key_exists($this->Sbc->getConfig('theme.sfw.field'), $this->templateVars['schema']);
			}
		}
	}

	/**
	 * Determines if the current schema uses Anon field.
	 *
	 * @param string $schema Schema name. Will use current schema name if null
	 *
	 * @return boolean
	 */
	public function s_haveAnon($schema = null) {
		if ($this->Sbc->getConfig('theme.anon.useAnon')) {
			// Schema given : field list
			if (!is_null($schema)) {
				return in_array($this->Sbc->getConfig('theme.anon.field'), $schema);
			} else {
				return array_key_exists($this->Sbc->getConfig('theme.anon.field'), $this->templateVars['schema']);
			}
		}
	}

	/**
	 * Determines is a field is a language field or not
	 *
	 * @param string $field Field name
	 *
	 * @return boolean
	 */
	public function s_isLanguageField($field) {
		if ($this->Sbc->getConfig('theme.language.useLanguages') == true) {
			// Splitting field name
			$exploded = explode('_', $field);
			$nbExploded = count($exploded);
			if ($nbExploded > 1) {
				if (in_array($exploded[($nbExploded - 1)], $this->Sbc->getConfig('theme.language.available'))) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Returns the language and name of a language field (field_lng)
	 *
	 * @param string $field Field name
	 *
	 * @return array Array(fieldName, lang)
	 */
	public function s_getLanguageFieldProperties($field) {
		$exploded = explode('_', $field);
		$nbExploded = count($exploded);
		if ($nbExploded > 1) {
			if (in_array($exploded[($nbExploded - 1)], $this->Sbc->getConfig('theme.language.available'))) {

				// Language:
				$lang = $exploded[$nbExploded - 1];

				// Final field name:
				unset($exploded[$nbExploded - 1]);
				$fieldName = implode('_', $exploded);

				return array('lang' => $lang, 'fieldName' => $fieldName);
			}
		}
	}

	/* ---------------------------------------------------------------------------
	 *
	 * Methods for views
	 *
	 * Methods begining with an "e" are for HTML elements
	 *
	 */

	/**
	 * Returns true or false if the current schema contain a file field.
	 *
	 * @param array $schema Schema array, that should have been updated by s_prepareSchemaFields()
	 * @return boolean
	 */
	public function s_haveFileField($schema) {
		foreach ($schema as $field => $options) {
			if ($this->s_isFileField($field)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Checks if the given field should be a file field.
	 *
	 * @param string $field
	 * @return boolean
	 */
	public function s_isFileField($field) {
		$uploadFields = $this->Sbc->getConfig('theme.upload.fields');
		if (is_null($uploadFields)) {
			return false;
		}
		if (in_array($field, $uploadFields)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Prepares an input field.
	 *
	 * @param string $field Field name
	 * @param array $config Configuration from v_prepareInputFields
	 * @param array $options An array of other options
	 *
	 * @return string HTML string for the input field.
	 */
	public function v_prepareInputField($field, $config, $options = array()) {

		// Field name
		$fieldString = "\${$this->templateVars['singularVar']}['{$this->templateVars['modelClass']}']['{$field}']";
		$displayString = $this->v_eFormInput($field) . "\n";

		// Set it to true when you have successfully found the type of field to prevent
		// accidental modifications
		$done = false;

		//
		// Field type
		//
		switch ($config['type']) {

			//
			// Numbers
			case 'integer':
				$displayString = $this->v_eFormInput($field, array('type' => 'number')) . "\n";
				break;

			//
			// Bools
			case 'boolean':
				$displayString = $this->v_eFormInput_CheckBox($field) . "\n";
				break;

			//
			// Strings and texts
			case in_array($config['type'], array('string', 'text')):
				//
				// Language field ?
				if (!empty($config['subType']) && $config['subType'] == 'language') {
					// Reset string
					$displayString = "";
					$options = ($config['type'] == 'text') ? array('class' => 'ckeditor form-control') : array();
					$strings = $this->v_displayInput_Language($field, $config, $options);
					foreach ($strings as $string) {
						$displayString.= $string;
					}
					$done = true;
				}

				//
				// File field ?
				if (!empty($config['subType']) && $config['subType'] == 'file') {
					$displayString = $this->v_eFormInput($field, array('type' => 'file'), array('fileField' => true)) . "\n";
				}
				//
				// TEXT
				elseif ($config['type'] == 'text' && !$done) {
					$displayString = $this->v_eFormInput($field, array('class' => 'ckeditor form-control')) . "\n";
				}

				break;

			// Datetimes
			case 'datetime':
				$displayString = $this->v_eFormInput_DateTimePicker($field) . "\n";
				break;
			// Default
			default:
				$displayString = $this->v_eFormInput($field) . "\n";
				break;
		}

		// Primary key ?
		if ($field === $this->templateVars['primaryKey']) {
			$displayString = $this->v_eFormInput($field, array('type' => 'hidden'));
		}

		// Adding new config to original one
		$config['displayString'] = $displayString;

		return $config;
	}

	/**
	 * Returns a input HTML element for language fields
	 *
	 * @param string $field Field name
	 * @param array $config Config from s_prepareSchemaFields() for this field
	 * @param array $options An array of options to pass to v_eFormInput()
	 * @return string
	 */
	public function v_displayInput_Language($field, $config, $options = array()) {
		foreach ($config['language']['langs'] as $lang) {
			$out[] = $this->v_eFormInput("{$field}_$lang", $options, array('addBefore' => "<?php echo \$this->Html->image('flags/$lang.gif');?>"));
		}
		return $out;
	}

	/**
	 * Prepares strings to display for a given field
	 *
	 * @param string $field Field name
	 * @param array $config Field configuration
	 * @param array $options An array of options
	 * @return array Configuration for the field
	 */
	public function v_prepareDisplayField($field, $config, $options = array()) {

		// Field options for views
		$tdClass = null; // Class for table rows containing this element
		// Field name
		$fieldString = "\${$this->templateVars['singularVar']}['{$this->templateVars['modelClass']}']['{$field}']";
		// Field to display on views
		$displayString = "echo $fieldString;";
		// Field to display, with no checks on it:
		$displayStringNoCheck = "echo $fieldString;";

		//
		// Field type
		//
		switch ($config['type']) {

			//
			// Numbers
			case 'integer':
				$displayString = "<?php $displayString ?>";
				break;

			//
			// Bools
			case 'boolean':
				$tdClass = 'text-center';
				// An icon should be displayed instead of the value
				$displayString = "<?php $displayString ?>";
				break;

			//
			// Strings and texts
			case in_array($config['type'], array('string', 'text')):
				//
				// Language field ?
				if (!empty($config['subType']) && $config['subType'] == 'language') {
					$displayString = $this->v_displayString_Language($field, $config);
					$displayStringNoCheck = $displayString;
				}

				//
				// Link ?
				if (isset($options['url']) && !is_null($options['url'])) {
					$displayString = "<?php echo \$this->Html->link($fieldString, {$options['url']})?>";
				} else {
					$displayString = "<?php\n$displayString\n?>";
					$displayStringNoCheck = "<?php\n$displayStringNoCheck\n?>";
				}
				break;

			// Datetimes
			case 'datetime':
				$tdClass = 'date-field';
				if ($this->Sbc->getConfig('theme.languages.uselanguages')) {
					$displayString = "<?php "
									. "echo date(\$langDateFormats[\$lang], strtotime($fieldString));"
									. " ?>";
				} else {
					$displayString = "<?php echo $fieldString; ?>";
				}
				break;

			// Default
			default:
				$displayString = "<?php $displayString ?>";
				break;
		}

		// Adding new config to original one
		$config['tdClass'] = (!is_null($tdClass)) ? " class=\"$tdClass\"" : '';
		$config['displayString'] = $displayString;
		$config['displayString-noCheck'] = $displayStringNoCheck;

		return $config;
	}

	/**
	 * Prepares string to display for a given field in associated models.
	 *
	 * @param string $field Field name
	 * @param array $config Association configuration
	 * @param boolean $hasOne If set to true, field names will be make for hasOne associations.
	 *
	 * @return array Configuration for the field.
	 */
	public function v_prepareDisplayRelatedField($field, $config, $originalFieldsList, $hasOne = false) {

		//Current Model:
		$model = Inflector::classify($config['controller']);

		$assocs = $this->s_getBelongsToAndLoadModels($model);
		// Class for table cells
		$tdClass = null;
		// Field name in views
		if ($hasOne) {
			$fieldString = "\${$this->templateVars['singularVar']}['$model']['$field']";
			$relationType = 'hasOne';
		} else {
			$fieldString = "\$" . Inflector::variable(Inflector::singularize($config['controller'])) . "['$field']";
			$relationType = 'hasMany';
		}

		// String to display data
		$displayString = "echo $fieldString;";

		// Foreign key field ?
		foreach ($assocs as $assoc => $assocConfig) {
			if ($field == $assocConfig['foreignKey']) {
				// Creating the link
				$fieldContent = "\$" . Inflector::variable($model) . "['$assoc']['" . ((is_null($this->otherModels[$assoc]->displayField)) ? $this->otherModels[$assoc]->primaryKey : $this->otherModels[$assoc]->displayField) . "']";
				$fieldId = "\$" . Inflector::variable($model) . "['$assoc']['" . $this->otherModels[$assoc]->primaryKey . "']";
				$controller = inflector::tableize($assoc);
				if ($this->canDo('view', null, $controller)) {
					$displayString = "echo \$this->Html->link($fieldContent, " . $this->url('view', $controller, null, "$fieldId") . ");";
				} else {
					$displayString = "echo $fieldContent";
				}
				$this->speak(__d('Sb', "$field is a FK for '$assoc'"), 'comment');
			}
		}
		// Configuration data is poor, so we can't check data type.
		// Language field ?
		if (!empty($config['fieldsOptions'][$field]) && $config['fieldsOptions'][$field]['subType'] == 'language') {
			$displayString = $this->v_displayString_Language($field, $config, $relationType, $model);
		}


		$config['displayString'] = "<?php $displayString ?>";
		$config['tdClass'] = (!is_null($tdClass)) ? " class=\"$tdClass\"" : '';

		return $config;
	}

	/**
	 * Prepares a string to use in views top display a foreign key. If the current
	 * prefix is allowed to view the action, a link will be made.
	 *
	 * @param type $field
	 * @param string $key
	 * @param array $config Field configuration array
	 * @return string String to use in views
	 */
	public function v_prepareDisplayFieldForeignKey($field, $key, $config) {

		// Class for table rows (or whatever you want) containing this element
		$tdClass = null;
		// Field name
		$fieldString = "\${$this->templateVars['singularVar']}['{$key['alias']}']['{$key['field']}']";
		// Field to display on views
		$displayString = "echo $fieldString;";

		// Link
		if ($this->canDo('view', null, $key['details']['controller'])) {
			$displayString = "echo \$this->Html->link($fieldString," . $this->url('view', $key['details']['controller'], null, "\${$this->templateVars['singularVar']}['{$key['alias']}']['{$key['details']['primaryKey']}']") . ");";
		} else {
			$displayString = "echo $fieldString;";
		}

		$displayString = "<?php\n$displayString\n?>";

		$config['tdClass'] = $tdClass;
		$config['displayString'] = $displayString;
		return $config;
	}

	/* ---------------------------------------------------------------------------
	 * Methods related to forms
	 */

	/**
	 * Displays a checkbox
	 *
	 * @param string $field Field name
	 * @return string The checkbox control
	 */
	public function v_eFormInput_CheckBox($field) {
		// Boolean entry

		$niceName = $this->v_getNiceFieldName($field);
		return "<?php echo \$this->Form->label('$field', \$this->Form->input('$field', array('div' => false, 'label' => false)) . ' ' . " . $this->iString($niceName) . ", array('class' => 'checkbox-inline')); ?>";
	}

	/**
	 * Creates a string for form input in views.
	 *
	 * @param string $field Field name
	 * @param array $options list of options to be passed to Html::input();
	 * @param array $config Configuration options for the style
	 *  Options for config:
	 *   - addBefore: adds an addon before input
	 *   - addAfter: adds an addon after input.
	 *   - fileField: bool, default false.
	 *
	 * Input style for "files" found here:
	 * http://www.surrealcms.com/blog/whipping-file-inputs-into-shape-with-bootstrap-3
	 *
	 * @return string input string.
	 */
	public function v_eFormInput($field, $options = array(), $config = array()) {

		$niceName = $this->iString($this->v_getNiceFieldName($field));

		$optionsString = "";
		$hiddenInput = false;

		// default options:
		if (!isset($options['div'])) {
			$optionsString.="'div' => " . ((!isset($options['div'])) ? "false" : (($options['div']) ? 'true' : 'false')) . ",";
			unset($options['div']);
		}
		if (!isset($options['label'])) {
			$optionsString.="'label' => " . ((!isset($options['label'])) ? "false" : (($options['label']) ? 'true' : 'false')) . ",";
			unset($options['label']);
		}
		if (!isset($options['class'])) {
			$optionsString.="'class' => " . ((!isset($options['class'])) ? "'form-control'" : "${options['class']}") . ",";
			unset($options['class']);
		}
		if (!isset($options['placeholder'])) {
			$optionsString.="'placeholder' => " . ((!isset($options['placeholder'])) ? $niceName : "${options['placeholder']}");
			unset($options['placeholder']);
		}
		if (isset($options['type']) && $options['type'] === 'hidden') {
//			$hiddenInput = true;
//			unset($options['type']);
			return "<?php echo \$this->Form->hidden('$field');?>\n";
		}

		//Other options:
		foreach ($options as $k => $v) {
			$optionsString.=", '$k' => '$v'";
		}

		// Config for style
		$hasAddon = (!empty($config['addBefore']) || !empty($config['addAfter'])) ? true : false;
		$fileField = (isset($config['fileField'])) ? $config['fileField'] : false;
		$out = "<div class=\"form-group\">\n";
		$out.="\t<?php echo \$this->Form->label('$field', $niceName, array('class' => 'col-lg-2 control-label')) ?>\n";
		$out.="\t<div class=\"col-lg-10" . ((!is_null($field)) ? '' : ' col-lg-offset-2') . "\">\n";
//		if (!$hiddenInput) {
		if ($hasAddon) {
			$out.="\t\t<div class=\"input-group\">\n";
			if (!empty($config['addBefore'])) {
				$out.="\t\t\t<span class=\"input-group-addon\">${config['addBefore']}</span>\n\t";
			}
		}
		if ($fileField) {
			$out.= "\t\t\t<span class=\"btn btn-primary btn-file\">\n\t\t\t<?php echo " . $this->iString('Browse') . "?>\n\t\t";
		}
		$out.="\t\t<?php echo \$this->Form->input('$field', array($optionsString));?>\n";
		if ($fileField) {
			$out.="\t\t</span><span id=\"${field}_selected\"></span>";
		}
		if ($hasAddon) {
			if (!empty($config['addAfter'])) {
				$out.="\t\t\t<span class=\"input-group-addon\">${config['addAfter']}</span>\n\t";
			}
			$out.="\t\t</div>\n";
		}
		$out.="\t</div>\n</div>\n";

		if ($fileField) {
			$out.="<script language=\"javascript\">
			    $(document).on('change', '.btn-file :file', function() {
						var input = $(this),
								numFiles = input.get(0).files ? input.get(0).files.length : 1,
								label = input.val().replace(/\\\/g, '/').replace(/.*\//, '');
						input.trigger('fileselect', [numFiles, label]);
					});

					$(document).ready( function() {
						$('.btn-file :file').on('fileselect', function(event, numFiles, label) {
							$('#${field}_selected').text(label)
						});
					});
</script>";
		}

		return $out;
	}

	public function v_eFormInput_DateTimePicker($field, $options=array()){
		return "<?php echo \$this->Form->input('$field', array('type'=>'datetime'));?>";
	}

	/**
	 * Creates an alert div with given class and content.
	 *
	 * Options:
	 *  - haveCloseButton true/false*, If true, alert will have a close button.
	 *
	 * @param string $content Div content
	 * @param string $class CSS class ('danger' for 'alert-danger)
	 * @param array $options List of options
	 * @return string HTML div with content
	 */
	public function v_eAlert($content, $class, $options = array()) {
		return "<div class=\"$class\">\n$content\n</div>";
	}

	/**
	 * Creates a new dropdown buttons group
	 *
	 * @param string $title Group name
	 * @param array $content Array of toolbar elements
	 *
	 * @return string String to add in the HTML
	 */
//	public function v_newDropdownButton($title, $content, $btnSize, $style = 'default') {
//		$dropdown = '';
//		$dropdown .="\t<div class=\"btn-group\">\n";
//		$dropdown .="\t\t<a class=\"btn $btnSize dropdown-toggle btn-" . $style . "\" data-toggle=\"dropdown\" href=\"#\"><?php echo " . $title . "; ? > <span class=\"caret\"></span></a>\n";
//		$dropdown .="\t\t<ul class=\"dropdown-menu\">\n";
//		foreach ($content as $item) {
//			$dropdown.= "\t\t\t<li>\n\t\t\t\t" . $item . "\t\t\t</li>\n";
//		}
//		$dropdown .= "\t\t</ul>\n";
//		$dropdown .= "\t</div>\n";
//		return $dropdown;
//	}

	/**
	 * Creates a new buttons group
	 *
	 * @param string $title Group name
	 * @param array $content Array of toolbar elements
	 *
	 * @return string String to add in the HTML
	 */
	public function v_newButtonGroup($content) {
		$btnGroup = "\t<ul>\n";
		foreach ($content as $item) {
			$btnGroup.= "\t\t<li>" . $item . "</li>\n";
		}
		$btnGroup .= "\t</ul>\n";
		return $btnGroup;
	}

	/**
	 * Creates a new row HTML element with $content in it.
	 *
	 * @param string $content Content to put in the row
	 * @param array $options
	 *
	 * @return string Row with content in it
	 */
	public function v_row($content, $options = array()) {
		// Opens the row
		$return = $this->v_newRow('open');
		// Put content
		$return.=$content;
		// Closes the row
		$return.=$this->v_newRow('close');

		return $return;
	}

//	public function v_dateField($name, $data_format = 'dd MM yyyy - hh:ii') {
//		$out = "\t<?php echo \$this->Form->input('$name'); ? >\n";
//		return $out;
//	}

	/**
	 * Opens a form group: contains a label in a col-lg-2 and opens a col-lg-10 for input.
	 *
	 * @param string $field
	 * @return string
	 */
	public function v_formOpenGroup($field = null, $humanFieldName = null) {
		$out = "<div class=\"form-group\">\n";
		if (!is_null($field)) {
			$out.="\t<?php echo \$this->Form->label('$field', $humanFieldName, array('class' => 'col-lg-2 control-label')) ?>\n";
		}
		$out.="\t<div class=\"col-lg-10" . ((!is_null($field)) ? '' : ' col-lg-offset-2') . "\">\n";
		return $out;
	}

	/**
	 * Closes a form group.
	 * @return string
	 */
	public function v_formCloseGroup() {
		return "\t</div>\n</div>\n";
	}

	/**
	 * Checks if a field is a foreign key. If true, returns an
	 *
	 * array(
	 * 		'alias'=>modelName,
	 * 		'field'=>displayField|primaryKey,
	 * 		'details'=> array(Association details)
	 * )
	 *
	 * else, returns false.
	 *
	 * @param type $field Field to check
	 * @param type $associactions Associations array.
	 * @return mixed Array or false
	 */
	public function v_isFieldForeignKey($field, $associations) {
		if (!empty($associations['belongsTo'])) {
			foreach ($associations['belongsTo'] as $alias => $details) {
				if ($field === $details['foreignKey']) {
//					echo "$field is in assocs array\n Here are the details for this assoc:\n";
//					var_dump($details);
//					die;
					return array(
							'alias' => $alias,
							'field' => ((isset($details['displayField']) ? $details['displayField'] : $details['primaryKey'])),
							'details' => $details
					);
				}
			}
		}
		return false;
	}

	/**
	 * Returns a field name with pagination link of any
	 *
	 * @param string $field Field name
	 * @param array $unsortableFields Array of unsortable fields from config file
	 */
	public function v_paginatorField($field, $unsortableFields = array()) {
		$out = null;

		//
		// Foreign keys
		//
		$key = $this->v_isFieldForeignKey($field, $this->templateVars['associations']);
		if (is_array($key)) {
			// Display name for foreign keys:
			$dField = $this->v_getNiceFieldName($key['alias']);
		} else {
			// Display name for "normal" fields:
			$dField = $this->v_getNiceFieldName($field);
		}

		//
		// Is the field sortable ?
		//
		if (!in_array($field, $unsortableFields)) {
			$out .= "\t\t\t\t\t<?php if (\$this->Paginator->sortDir() == 'desc' && \$this->Paginator->sortKey() == '$field'): ?>\n";
			$out .= "\t\t\t\t\t\t<i class=\"fa fa-sort-alpha-desc\"></i>\n";
			$out .= "\t\t\t\t\t<?php elseif(\$this->Paginator->sortDir() == 'asc' && \$this->Paginator->sortKey() == '$field') : ?>\n";
			$out .= "\t\t\t\t\t\t\t<i class=\"fa fa-sort-alpha-asc\"></i>\n";
			$out .= "\t\t\t\t\t<?php endif; ?>\n";
			$out .= "\t\t\t\t\t<?php echo \$this->Paginator->sort('{$field}', " . $this->iString($dField) . "); ?>\n";
		} else {
			$out .= "<?php echo " . $this->iString($dField) . "; ?>";
		}
//		$this->speak("$field: $out\n");
		return $out;
	}

	/**
	 * Returns an human readable field name.
	 *
	 * ex:
	 * 	"user_id" becomes "user id"
	 *
	 * @param string $field Field name
	 * @return string
	 */
	public function v_getNiceFieldName($field) {
		if (array_key_exists($field, $this->templateVars['fieldNames'])) {
			return $this->templateVars['fieldNames'][$field];
		} else {
			return ucfirst(strtolower(Inflector::humanize($field)));
		}
	}

	/*	 * ************************************************************************
	 *
	 * Methods for controllers
	 *
	 * *********************************************************************** */

	/**
	 * Returns the 'contain' conditions for find() method.
	 *
	 * @param string $model Current model name.
	 * @param array $options Find condition for related data
	 * @return array
	 */
	public function c_findContains($model, $options = array()) {
		//Treating options:
		if (!isset($options['hiddenAssociations'])) {
			$options['hiddenAssociations'] = array();
		}
		if (!isset($options['conditions'])) {
			$options['conditions'] = array();
		}

		$relations = array();
		// Getting model relations
		foreach ($model->hasOne as $assoc => $config) {
			if (!in_array($assoc, $options['hiddenAssociations'])) {
				$relations[$assoc] = array();
			}
		}
		foreach ($model->hasMany as $assoc => $config) {
			if (!in_array($assoc, $options['hiddenAssociations'])) {
				$relations[$assoc] = array();
				$this->speak("$assoc:");
				// Loads the associated model
				$this->s_loadModel($assoc);
				foreach ($this->otherModels[$assoc]->belongsTo as $fAssoc => $fAssocData) {
					$fModelConfig = $this->Sbc->getModelConfig($fAssoc);
					// PK
					$relations[$assoc][$fAssoc][] = $this->otherModels[$assoc]->primaryKey;
					// Display field
					if (!empty($fModelConfig['displayField'])) {
						$relations[$assoc][$fAssoc][] = $fModelConfig['displayField'];
					}
				}
			}
		}
		foreach ($model->belongsTo as $assoc => $config) {
			if (!in_array($assoc, $options['hiddenAssociations'])) {
				$relations[$assoc] = array();
			}
		}
		foreach ($model->hasAndBelongsToMany as $assoc => $config) {
			if (!in_array($assoc, $options['hiddenAssociations'])) {
				$relations[$assoc] = array();
			}
		}

		foreach ($relations as $rel => $config) {
			$currentConditions = array();
			if (isset($options['conditions'][$rel])) {
				$currentConditions = $this->c_getContainConditions($options['conditions'][$rel]);
			}
			if (count($currentConditions) > 0) {
				$relations[$rel]['conditions'] = $currentConditions;
			}
		}

		return $relations;
	}

	/**
	 * Adds conditions for the containable behavior.
	 *
	 * @param type $conditions
	 * @return string
	 */
	public function c_getContainConditions($conditions = array()) {
		$return = array();
		foreach ($conditions as $condition => $value) {
			switch ($condition) {
				// Hide the "anon" field
				case '%noAnon%':
					if ($this->Sbc->getConfig('theme.anon.useAnon')) {
						$return[$this->Sbc->getConfig('theme.anon.field')] = 0;
					}
					break;
				default:
					$return[$condition] = $value;
					break;
			}
		}
		return $return;
	}

	/**
	 * Outputs a condition for a find/paginate call by replacing vars by actual code.
	 *
	 * @param string $condition The condition string
	 *
	 * @return string replacement string or $condition if nothing is found.
	 */
	public function c_setFindConditions($condition) {
		switch ($condition) {
			case '%now%':
				return 'date("Y-m-d H:i:s")';
			case '%self%':
				return "\$this->Session->read('Auth." . $this->Sbc->getConfig('theme.components.Auth.userModel') . '.' . $this->Sbc->getConfig('theme.components.Auth.userModelPK') . "')";
			default:
				return "'$condition'";
				break;
		}
	}

	/**
	 * Parses a given byte count.
	 *
	 * Function from Drupal, found here : https://api.drupal.org/api/drupal/includes!common.inc/function/parse_size/6
	 *
	 * @param string A size expressed as a number of bytes with optional SI or IEC binary unit prefix (e.g. 2, 3K, 5MB, 10G, 6GiB, 8 bytes, 9mbytes).
	 *
	 * @return integer Representation of the size, in bytes
	 */
	public function c_parseSize($size) {
		$suffixes = array(
				'' => 1,
				'k' => 1024,
				'm' => 1048576, // 1024 * 1024
				'g' => 1073741824, // 1024 * 1024 * 1024
		);
		if (preg_match('/([0-9]+)\s*(k|m|g)?(b?(ytes?)?)/i', $size, $match)) {
			return $match[1] * $suffixes[strtolower($match[2])];
		}
	}

	/**
	 * Determine the maximum file upload size by querying the PHP settings.
	 *
	 * Function from Drupal, found here : https://api.drupal.org/api/drupal/includes!file.inc/function/file_upload_max_size/6
	 *
	 * @staticvar integer $max_size
	 * @return integer A file size limit in bytes based on the PHP upload_max_filesize and post_max_size
	 */
	public function c_getFileUploadMaxSize() {
		$max_size = -1;

		if ($max_size < 0) {
			$upload_max = parseSize(ini_get('upload_max_filesize'));
			$post_max = parseSize(ini_get('post_max_size'));
			$max_size = ($upload_max < $post_max) ? $upload_max : $post_max;
		}
		return $max_size;
	}

	/* ---------------------------------------------------------------------------
	 *
	 *
	 * Other methods
	 *
	 *
	 * ------------------------------------------------------------------------ */
}
