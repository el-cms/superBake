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
	public function themeInfos() {
		return('Experiments Labs theme.');
	}

	/* ---------------------------------------------------------------------------
	 *
	 * Methods to work on schemas
	 *
	 * ------------------------------------------------------------------------- */

	public function s_loadModel($model, $plugin = null) {
		if (!in_array($model, $this->otherModels)) {
			if (!is_null($plugin)) {
				$plugin = $this->Sbc->getPluginName($this->Sbc->getModelPlugin($model));
			}
			App::uses($model, $plugin . '.Model');
			// Checks if Model has been loaded correctly
			if (!class_exists($model)) {
				$this->speak(__d('superBake', "Generate {$plugin}$model model first", 'error', 0));
				$this->_stop();
			}
			$this->otherModels[$model] = ClassRegistry::init($model);
		}
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
		foreach ($this->templateVars['schema'] as $field => $config) {
			//
			// Hidden fields : removing field if it should not be seen.
			//
			if (is_array($this->templateVars['hiddenFields']) && in_array($field, $this->templateVars['hiddenFields'])) {
				// Removing from fields list
				unset($this->templateVars['fields'][$i]);
			}
			$i++;
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
			}
			$i++;
		}
		return $relation;
	}

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
	 */

	/**
	 * Prepares strings to display for a given field
	 *
	 * @param string $field Field name
	 * @param array $config Field configuration
	 * @return array Configuration for the field
	 */
	public function v_prepareField($field, $config) {
		// Class for table rows (or whatever you want) containing this element
		$tdClass = null;
		// Field name
		$fieldString = "\${$this->templateVars['singularVar']}['{$this->templateVars['modelClass']}']['{$field}']";
		// Field to display on views
		$displayString = "echo $fieldString;";

		// Input HTML element for forms
		$displayForm = $this->v_formInput($field, array('class' => 'text-muted'));

		//
		// Field type
		//
		switch ($config['type']) {
			// Numbers
			case 'integer':
				$displayString = "<?php $displayString ?>";
				break;

			//Bools
			case 'boolean':
				$tdClass = 'text-center';
				// An icon should be displayed instead of the value
				$displayString = "<?php $displayString ?>";
				break;

			// Strings and texts
			case in_array($config['type'], array('string', 'text')):
				$displayString = "<?php $displayString ?>";
				break;

			// Datetimes
			case 'datetime':
				$tdClass = 'date-field';
				$displayString = "<?php $displayString; ?>";
				break;

			// Default
			default:
				$displayString = "<?php $displayString ?>";
				break;
		}

		// Adding new config to original one
		$config['tdClass'] = (!is_null($tdClass)) ? " class=\"$tdClass\"" : '';
		$config['displayString'] = $displayString;
		$config['displayForm'] = $displayForm;

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
	public function v_prepareRelatedField($field, $config, $originalFieldsList, $hasOne = false) {

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

		// String to display form element
		$displayForm = $this->v_formInput($field, array('class' => 'text-muted'));

		// Foreign key field ?
		foreach ($assocs as $assoc => $assocConfig) {
			if ($field == $assocConfig['foreignKey']) {
				$displayString = "echo \$" . Inflector::variable($model) . "['$assoc']['" . ((is_null($this->otherModels[$assoc]->displayField)) ? $this->otherModels[$assoc]->primaryKey : $this->otherModels[$assoc]->displayField) . "'];";
				$this->speak("$field is a FK for '$assoc' !");
			}
		}

		// Configuration data is poor, so we can't check data type.  But you can still
		// check with some of your config, because you should know your DB :)
		$config['displayString'] = "<?php $displayString ?>";
		$config['tdClass'] = (!is_null($tdClass)) ? " class=\"$tdClass\"" : '';
		$config['displayForm'] = $displayForm;

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
	public function v_prepareFieldForeignKey($field, $key, $config) {

		// Class for table rows (or whatever you want) containing this element
		$tdClass = null;
		// Field name
		$fieldString = "\${$this->templateVars['singularVar']}['{$key['alias']}']['{$key['field']}']";
		// Field to display on views
		$displayString = "echo $fieldString;";

		// Input HTML element for forms
		$displayForm = $this->v_formInput($field, array('class' => 'text-muted'));

		// Link
		if ($this->canDo('view', null, $key['details']['controller'])) {
			$displayString = "echo \$this->Html->link($fieldString," . $this->url('view', $key['details']['controller'], null, "\${$this->templateVars['singularVar']}['{$key['alias']}']['{$key['details']['primaryKey']}']") . ");";
		} else {
			$displayString = "echo $fieldString;";
		}

		$displayString = "<?php\n$displayString\n?>";

		$config['tdClass'] = $tdClass;
		$config['displayString'] = $displayString;
		$config['displayForm'] = $displayForm;
		return $config;
	}

	/* ---------------------------------------------------------------------------
	 * Methods related to forms
	 */

	/**
	 * Creates a string for form input in views.
	 *
	 * @param string $field Field name
	 * @param array $options list of options to be passed to Html::input();
	 * @return string input string.
	 */
	public function v_formInput($field, $options = array()) {

		if (!empty($options)) {
			$options = $this->displayArray($options);
		}

		return "<?php echo \$this->Form->input('$field'" . ((!is_null($options) ? ", $options" : "" ) ) . ");?>";
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
	public function v_alert($content, $class, $options = array()) {
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
	public function v_newDropdownButton($title, $content, $btnSize, $style = 'default') {
		$toolbar = '';
		$toolbar .="\t<div class=\"btn-group\">\n";
		$toolbar .="\t\t<a class=\"btn $btnSize dropdown-toggle btn-" . $style . "\" data-toggle=\"dropdown\" href=\"#\"><?php echo " . $title . "; ?> <span class=\"caret\"></span></a>\n";
		$toolbar .="\t\t<ul class=\"dropdown-menu\">\n";
		foreach ($content as $item) {
			$toolbar.= "\t\t\t<li>\n\t\t\t\t" . $item . "\t\t\t</li>\n";
		}
		$toolbar .= "\t\t</ul>\n";
		$toolbar .= "\t</div>\n";
		return $toolbar;
	}

	/**
	 * Creates a new buttons group
	 *
	 * @param string $title Group name
	 * @param array $content Array of toolbar elements
	 *
	 * @return string String to add in the HTML
	 */
	public function v_newButtonGroup($content) {
		$toolbar = "\t<ul>\n";
		foreach ($content as $item) {
			$toolbar.= "\t\t<li>" . $item . "</li>\n";
		}
		$toolbar .= "\t</ul>\n";
		return $toolbar;
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

	public function v_dateField($name, $data_format = 'dd MM yyyy - hh:ii') {
		$out = "\t<?php echo \$this->Form->input('$name'); ?>\n";
		return $out;
	}

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
	public function v_isFieldKey($field, $associations) {
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
		$key = $this->v_isFieldKey($field, $this->templateVars['associations']);
		if (is_array($key)) {
			// Display name for foreign keys:
			$dField = $key['alias'];
		} else {
			// Display name for "normal" fields:
			$dField = $this->v_fieldName($field);
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
	public function v_fieldName($field) {
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
	 * Outputs a condition for a find/paginate call by replacing vars by actual code.
	 *
	 * @param string $condition The condition string
	 *
	 * @return string replacement string or $condition if nothing is found.
	 */
	public function c_indexConditions($condition) {
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
