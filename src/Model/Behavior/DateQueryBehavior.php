<?php
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Query;
use Cake\Utility\Inflector;
use Cake\I18n\Time;

/**
 * DateQuery Behavior
 * 
 * Configure this behavior to create a set of custom finder methods that operate 
 * on a single 'date' field in a table. Two configuration values are required:
 * <pre>
 * $config['field'] contains the name of the table column
 * $config['model'] contains the name of the table class
 * </pre>
 * 
 * There are two additional values that may be provided. These are the names 
 * of the form inputs that will carry the WHERE arguemnents.
 * <pre>
 * $config['primary_input'] provides the name of the imput with a value for 
 *		find_Before(), find_Is(), and find_After()
 * $config['secondary_input'] names the input with the second value 
 *		for find_Between()
 * </pre>
 * 
 * All the custom finder method names will be modified to include an 
 * inflected version of the field name. For example the behavior configured  
 * with 'field' => 'start_date' will provide the finder methods 
 * `findStartDateBefore()`, `findStartDateIs()`, `findStartDateAfter()`, and 
 * `findStartDateBetween()`. This allows multiple versions of the behavior 
 * in a single Table, each copy operating on a different 'date' column.
 * 
 * When configuring multiple copies of this behavior you must load each 
 * with a different name:
 * <code>
 *		$this->addBehavior('StartDateQuery', [
 *			'className' => 'DateQuery',
 *			'field' => 'start_date', 
 *			'model' => $this->alias()]);
 *		$this->addBehavior('EndDateQuery', [
 *			'className' => 'DateQuery',
 *			'field' => 'end_date', 
 *			'model' => $this->alias(),
 *			'primary_input' => 'end_date',]);
 * </code>
 * 
 * BE CAREFUL in tables with more than one 'date' column. Each column's 
 * associated behavior config will need its own inputs if you want to 
 * combine finders from different instantiations. Sharing names means sharing 
 * values and that's probably not the what you want!
 * 
 * And, as with all behaviors, you can reconfigure on the fly:
 * <code>
 *		// in something like a controller
 *		$this->Users->behaviors()->get('RegDateQuery')
 *			->config('primary_input', 'first_day');
 *		return $query->find('RegDateAfter', $options);
 *		// the value at $options['first_day'] would be used
 * </code>
 * 
 * @author dondrake
 */
class DateQueryBehavior extends Behavior {
	
	protected $_defaultConfig = [
		'implementedMethods' => [],
		'implementedFinders' => [],
		'primary_input' => 'start_date',
		'secondary_imput' => 'end_date'];
	
	public function __construct($table, array $config = []) {
		$methods = preg_grep('/find_/', get_class_methods($this));
		$calls = preg_replace('/find_/', 'find'. Inflector::classify($config['field']), $methods);
		$this->_defaultConfig['implementedFinders'] = array_combine($calls, $methods);
		$config += $this->_defaultConfig;
		parent::__construct($table, $config);
	}
	
	/**
	 * Standardize and sanitize user date input
	 * 
	 * Date data will often come directly form a user input form. Turning 
	 * this input into a Time object lets us absorb a wide variety of date 
	 * input and should nuetralize any malicious or damaging input.
	 * 
	 * @todo Make a better Exception result. But proper 
	 *		form validation should prevent most bad input. 
	 * @param string $date 
	 * @return Time
	 */
	protected function _setDateParameter($date) {
		try {
			return (new Time($date))->i18nFormat('yyyy-MM-dd');
		} catch (Exception $ex) {
			throw new \BadMethodCallException('Could not create Time object for the date field query');
		}
		
	}

	protected function _columnIdententifier() {
		return "{$this->config('model')}.{$this->config('field')}";
	}
	
	public function find_Is(Query $query, $options) {
		$date = $this->_setDateParameter($options[$this->config('primary_input')]);
		return $query->where([$this->_columnIdententifier() => $date]);
	}

	public function find_Before(Query $query, $options) {
		$date = $this->_setDateParameter($options[$this->config('primary_input')]);
		return $query->where(["{$this->_columnIdententifier()} <" => $date]);
	}

	public function find_After(Query $query, $options) {
		$date = $this->_setDateParameter($options[$this->config('primary_input')]);
		return $query->where(["{$this->_columnIdententifier()} >" => $date]);
	}

	/**
	 * Find dates between two dates (inclusive)
	 * 
	 * @todo _table->_setUserId() must not be here. It will have to be 
	 *		removed and performed by beforeFind event handler
	 * @param Query $query
	 * @param type $options
	 * @return type
	 */
	public function find_Between(Query $query, $options) {
		$range_start = $this->_setDateParameter($options[$this->config('primary_input')]);
		$range_end = $this->_setDateParameter($options[$this->config('secondary_input')]);
		$column = $this->_columnIdententifier();
		return $this->_table->_setUserId($query)->where(function ($exp, Query $q) 
				use ($range_start, $range_end, $column) {
					return $exp->between($column, 
						$range_start, $range_end);
				});
	}

}
