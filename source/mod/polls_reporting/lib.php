<?php
	/**
	 * This is the superclass for all the domain objects
	 * @package PollsReporting
	 * @subpackage Model
	 * @author <elgg@surevine.com>
	 */
	abstract class PollsReporting_DomainObject
	{
		/**
		 * 
		 * @var PollReporting_DomainObjectMapper
		 */
		private $mapper;
		
		/**
		 * Returns the mapper used for this object
		 * @return PollsReporting_DomainObjectMapper
		 */
		public function getMapper()
		{
			return $this->mapper;
		}
		
		/**
		 * Sets the mapper used for this domain object
		 * @param PollsReporting_DomainObjectMapper $mapper
		 * @return void
		 */
		public function setMapper(PollsReporting_DomainObjectMapper $mapper)
		{
			$this->mapper = $mapper;
		}
		
		public function save()
		{
			if(is_null($this->getMapper()))
				throw new Exception('Cannot save - no mapper!');
				
			return $this->getMapper()->save($this);
		}
	}

	/**
	 * This is the superclass for all the domain objects mappers which are responsible
	 * for persisting domain objects
	 * @package PollsReporting
	 * @subpackage DAO
	 * @author <elgg@surevine.com>
	 */
	abstract class PollsReporting_DomainObjectMapper
	{
		/**
		 * Saves the domain object
		 * @param PollsReporting_DomainObject $object
		 * @return boolean True if the save was successful
		 */
		public abstract function save(PollsReporting_DomainObject $object);
		
		/**
		 * Overridden in a subclass to create a new domain object
		 * @return PollsReporting_DomainObject
		 */
		protected abstract function create();
		
		/**
		 * Creates a new PollsReporting_DomainObject
		 * @return PollsReporting_DomainObject
		 */
		public function createNew()
		{
			$object = $this->create();
			$object->setMapper($this);
			return $object;
		}
	}
	
	class PollsReporting_ReportParameterDefinition
	{
		private $name;
		private $required;
		private $title;
		
		/**
		 * Return the parameter name
		 * @return string
		 */
		public function getName()
		{
			return $this->name;
		}
		
		/**
		 * Sets the parameter name
		 * @param string $value
		 * @return void
		 */
		public function setName($value)
		{
			$this->name = $value;
		}
		
		/**
		 * Return the parameter title
		 * @return string
		 */
		public function getTitle()
		{
			return $this->title;
		}
		
		/**
		 * Sets the parameter title
		 * @param string $value
		 * @return void
		 */
		public function setTitle($value)
		{
			$this->title = $value;
		}
		
		/**
		 * Return whether this parameter is required
		 * @return boolean
		 */
		public function getRequired()
		{
			return $this->required;
		}
		
		/**
		 * Sets whether the parameter is required
		 * @param boolean $value
		 * @return void
		 */
		public function setRequired($value)
		{
			$this->required = $value;
		}
	}
	
	/**
	 * This class represents a report type that is available to view, e.g. "Current Standings report"
	 * @package PollsReporting
	 * @subpackage Model
	 * @author <elgg@surevine.com>
	 *
	 */
	class PollsReporting_ReportDefinition extends PollsReporting_DomainObject
	{
		private $id;
		
		/**
		 * Array of PollsReporting_ReportParameterDefinition denoting which extra
		 * parameters are available/required for this report type.
		 * @var array
		 */
		private $param_defs = array();
		
		/**
		 * Default acces level for this report type
		 * @todo Make this user configurable
		 */
		private $default_access_id = ACCESS_LOGGED_IN;

		/**
		 * Returns the report id
		 * 
		 * @return int
		 */
		public function getId()
		{
			return $this->id;
		}
		
		/**
		 * Sets the report id
		 * 
		 * @param int $value
		 * @return void
		 */
		public function setId($value)
		{
			$this->id = $value;
		}
		
		/**
		 * Returns the default access level for the report
		 * 
		 * @return int
		 */
		public function getDefaultAccessId()
		{
			return (int) $this->default_access_id;
		}
		
		/**
		 * Sets the default access level for the report
		 * 
		 * @param int $value
		 * @return void
		 */
		public function setDefaultAccessId($value)
		{
			$this->default_access_id = $value;
		}
		
		/**
		 * Returns the title for the report
		 * 
		 * @return string
		 */
		public function getTitle()
		{
			return elgg_echo('polls_reporting:report:title:' . $this->getId());
		}
		
		/**
		 * Sets the title for the report
		 * 
		 * @param string $value
		 * @return void
		 */
		public function setTitle($value)
		{
			// Not currently implemented
		}
		
		/**
		 * Returns the description for the report
		 * 
		 * @return string
		 */
		public function getDescription()
		{
			return elgg_echo('polls_reporting:report:description:' . $this->getId());
		}
		
		/**
		 * Sets the description for the report
		 * 
		 * @param string $value
		 * @return void
		 */
		public function setDescription($value)
		{
			// Not currently implemented
		}
		
		/**
		 * 
		 * @param int $poll_guid Poll Guid
		 * @return PollsReporting_Report
		 */
		public function getReportForPollGuid($poll_guid)
		{
			$report_mapper = PollsReporting_ReportMapper::getInstance();
	
			return $report_mapper->findByIdAndPollGuid($this->getId(), $poll_guid);
		}
		
		/**
		 * Get the parameter definitions
		 * @return array<PollsReporting_ReportParameterDefinition>
		 */
		public function getReportParameterDefinitions()
		{
			return $this->param_defs;
		}
		
		/**
		 * Get the parameter definitions
		 * @param array<PollsReporting_ReportParameterDefinition> $definition
		 * @return void
		 */
		public function setReportParameterDefinitions($definitions)
		{
			$this->param_defs = $definitions;
		}
	}

	/**
	 * This class represents an actual report for a poll
	 * e.g. "Current Standings report for Who's the best looking pop star"
	 * @package PollsReporting
	 * @subpackage Model
	 * @author <elgg@surevine.com>
	 *
	 */
	class PollsReporting_Report extends PollsReporting_DomainObject
	{
		private $id;
		private $poll_guid;
		private $access_id;
		private $params = array();
		
		/**
		 * Returns the report id
		 * 
		 * @return int
		 */
		public function getId()
		{
			return $this->id;
		}
		
		/**
		 * Sets the report id
		 * 
		 * @param int $value
		 * @return void
		 */
		public function setId($value)
		{
			$this->id = $value;
		}

		/**
		 * Returns the associated poll guid
		 * 
		 * @return int
		 */
		public function getPollGuid()
		{
			return $this->poll_guid;
		}
		
		/**
		 * Sets the associated poll guid
		 * 
		 * @param int $value
		 * @return void
		 */
		public function setPollGuid($value)
		{
			$this->poll_guid = $value;
		}
		
		/**
		 * Returns the access level for the report
		 * 
		 * @return int
		 */
		public function getAccessId()
		{
			if(is_null($this->access_id))
			{
				$definition = $this->getReportDefinition();
				
				if($definition)
					return $definition->getDefaultAccessId();
			}
			
			return $this->access_id;
		}
		
		/**
		 * Sets the access level for the report
		 * 
		 * @param int $value
		 * @return void
		 */
		public function setAccessId($value)
		{
			$this->access_id = (int) $value;
		}
		
		/**
		 * Returns to URL to access this report
		 * 
		 * @return string
		 */
		public function getUrl($viewtype = 'default')
		{
			global $CONFIG; // Yuck!
			
			$query = $this->getQueryString();
			
			if($viewtype == 'csv')
			{
				if(elgg_view_exists('polls_reporting/reports/' . $this->getId(), $viewtype))
					return "{$CONFIG->wwwroot}pg/polls/reports/{$this->getPollGuid()}/{$this->getId()}." . $viewtype . $query;
				
				return null;
			}
			
			return "{$CONFIG->wwwroot}pg/polls/reports/{$this->getPollGuid()}/{$this->getId()}{$query}";
		}
		
		/**
		 * 
		 * @return ElggObject
		 */
		public function getPoll()
		{
			$poll = get_entity($this->getPollGuid());
			
			if(!$poll || ($poll->getSubType() != 'poll'))
				return null;
				
			return $poll;
		}
		
		/**
		 * Determine if a user can edit the settings for this report
		 * 
		 * @param int|null $user_guid defaults to the logged in user
		 * @return boolean true if the user can edit this report
		 */
		public function canEdit($user_guid = null)
		{
			$poll = $this->getPoll();
			
			if(!$poll)
				return false;	// Something must've gone wrong somewhere!
			
			// If we can edit the underlying poll, then we can edit this!
			// return $poll->canEdit($user_guid);
			
			if(is_null($user_guid))
				$user_guid = get_loggedin_userid();
				
			return ($poll->getOwner() == $user_guid);
		}
		
		/**
		 * Determine if a user can view this report
		 * 
		 * @param int|null $user_guid defaults to the logged in user
		 * @return boolean true if the user can edit this report
		 */
		public function canView($user_guid = null)
		{
			// If we can edit it then we can view it
			if($this->canEdit($user_guid))
				return true;
			
			$access_array = get_access_array($user_guid);
			
			if(in_array($this->getAccessId(), $access_array))
				return true;
			
			// @todo Make this handle friends
			return false;
		}
		
		public function getReportDefinition()
		{
			$definition_mapper = PollsReporting_ReportDefinitionMapper::getInstance();
			
			return $definition_mapper->findById($this->getId());
		}
		
		public function getTitle()
		{
			return $this->getReportDefinition()->getTitle();
		}
		
		public function setParameter($name, $value)
		{
			$this->params[$name] = $value;
		}
		
		public function getParameter($name)
		{
			if(isset($this->params[$name]))
				return $this->params[$name];
				
			return null;
		}
		
		private function getQueryString()
		{
			$query = PollsReporting_StringUtils::buildHttpQuery($this->params);
			
			if($query != '')
				$query = '?' . $query;
				
			return $query;
		}
	}

	/**
	 * This class is for persistence of PollsReporting_ReportDefinition instances
	 * @package PollsReporting
	 * @subpackage DAO
	 * @author <elgg@surevine.com>
	 * @see PollsReporting_ReportDefinition
	 *
	 */
	class PollsReporting_ReportDefinitionMapper extends PollsReporting_DomainObjectMapper
	{
		/**
		 * This is just temporary until there is a better way of defining which reports
		 * are available
		 * @var array<array>
		 */
		private static $reports_available = array(
			'whohasvoted' => array('id' => 'whohasvoted'),
			'standings' => array('id' => 'standings'),
			'candidate_votes' => array(
				'id' => 'candidate_votes',
				'params' => array(
					array('name' => 'candidate_guid', 'required' => true, 'title' => 'polls:candidate:title')
				),
			),
			'candidate_trend' => array(
				'id' => 'candidate_trend',
				'params' => array(
					array('name' => 'candidate_guid', 'required' => true, 'title' => 'polls:candidate:title')
				),
			),
//			'top10' => array('id' => 'top10'),
			);
		
		private static $instance;
		
		/**
		 * @return PollsReporting_ReportDefinitionMapper
		 */
		public static function getInstance()
		{
			if(is_null(self::$instance))
				self::$instance = new PollsReporting_ReportDefinitionMapper();
				
			return self::$instance;
		}
		
		/**
		 * Returns all the available report definitions
		 * @return array
		 */
		public function findAll()
		{
			$result = array();
			
			foreach(self::$reports_available as $report_array)
			{
				$result[] = $this->findById($report_array['id']);
			}
			
			return $result;
		}
		
		/**
		 * Returns a report definition by id
		 * @param int $id
		 * @return PollsReporting_ReportDefinition
		 */
		public function findById($id)
		{
			if(!isset(self::$reports_available[$id]))
				return null;
			
			$report_def = $this->populate(self::$reports_available[$id]);
			
			return $report_def;
		}
		
		/**
		 * 
		 * @param array $data
		 * @param PollsReporting_Report $object
		 * @return PollsReporting_Report
		 */
		protected function populate($data, $object = null)
		{
			if(!$object)
				$object = $this->createNew();
				
			if(isset($data['id'])) $object->setId($data['id']);
			
			if(isset($data['params']) && is_array($data['params']))
			{
				$params = array();
				
				foreach($data['params'] as $param)
				{
					$param_def = new PollsReporting_ReportParameterDefinition();
					
					if(isset($param['name']))
						$param_def->setName($param['name']);
					
					if(isset($param['required']))
						$param_def->setRequired($param['required']);
						
					$params[] = $param_def;
				}
				
				$object->setReportParameterDefinitions($params);
			}
			
			return $object;
		}
		
		/**
		 * Creates a new report definition
		 * @return PollsReporting_ReportDefinition
		 */
		public function create()
		{
			return new PollsReporting_ReportDefinition();
		}

		/**
		 * Saves the report definition
		 * @see httpdocs/mod/polls_reporting/PollsReporting_DomainObjectMapper#save()
		 */
		public function save(PollsReporting_DomainObject $reportdef)
		{
			// We don't actually persist anything at the moment
			return true;
		}
	}
	
	/**
	 * This class is for persistence of PollsReporting_Report instances
	 * @package PollsReporting
	 * @subpackage DAO
	 * @author <elgg@surevine.com>
	 * @see PollsReporting_Report
	 *
	 */
	class PollsReporting_ReportMapper extends PollsReporting_DomainObjectMapper
	{
		private static $instance;
		
		/**
		 * @return PollsReporting_ReportMapper
		 */
		public static function getInstance()
		{
			if(is_null(self::$instance))
				self::$instance = new PollsReporting_ReportMapper();
				
			return self::$instance;
		}
		
		/**
		 * Internal function to load the access id into the report
		 * @param PollsReporting_Report $report
		 * @return void
		 */
		private function updateReportAccessId(PollsReporting_Report $report)
		{
			$poll = $report->getPoll();
			
			if(!$poll)
				return;
			
			$metaname = 'polls_reporting_' . $report->getId() . '_access_id';
				
			$report->setAccessId($poll->$metaname);
		}
		
		/**
		 * Finds a PollsReporting_Report by its id and poll guid
		 * @todo Make it check that the id is a real report id
		 * @param int $id Report Id
		 * @param int $poll_guid The poll guid for the report
		 * @return PollsReporting_Report
		 */
		public function findByIdAndPollGuid($id, $poll_guid)
		{
			$report = $this->createNew();
			
			$report->setId($id);
			$report->setPollGuid($poll_guid);
			
			$this->updateReportAccessId($report);

			return $report;
		}
		
		/**
		 * Finds all the PollsReporting_Reports for a certain poll viewable by a certain user
		 * @param $poll_guid
		 * @param $user_guid
		 * @return array
		 */
		public function findAllByPollGuidForUserGuid($poll_guid, $user_guid)
		{
			// Get all the different types of report
			$reportdefinition_mapper = PollsReporting_ReportDefinitionMapper::getInstance();
			$definitions = $reportdefinition_mapper->findAll();
			
			$results = array();
			
			foreach($definitions as $definition)
			{
				$report = $this->createNew();
				
				$report->setId($definition->getId());
				$report->setPollGuid($poll_guid);
				
				$this->updateReportAccessId($report);
				
				if($report->canView($user_guid))
					$results[] = $report;
			}
			
			return $results;
		}
		
		/**
		 * Creates a new report
		 * @return PollsReporting_Report
		 */
		public function create()
		{
			return new PollsReporting_Report();
		}
		
		/**
		 * Save the report
		 * @see httpdocs/mod/polls_reporting/PollsReporting_DomainObjectMapper#save()
		 */
		public function save(PollsReporting_DomainObject $report)
		{
			$poll = $report->getPoll();
			
			if(!$poll)
				throw new Exception('No poll to save settings to');
					
			$metaname = 'polls_reporting_' . $report->getId() . '_access_id';
			
			$poll->$metaname = (int) $report->getAccessId();
			
			return true;
		}
		
		/**
		 * 
		 * @param array $data
		 * @param PollsReporting_Report $object
		 * @return PollsReporting_Report
		 */
		protected function populate($data, $object = null)
		{
			if(!$object)
				$object = $this->createNew();
				
			if(isset($data['id'])) $object->setId($data['id']);
			if(isset($data['access_id'])) $object->setId($data['access_id']);
			
			return $object;
		}
	}
	
	
	
	/**
	 * String based utility functions
	 * @package PollsReporting
	 * @subpackage Util
	 * @author <elgg@surevine.com>
	 * @see PollsReporting_Report
	 *
	 */
	class PollsReporting_StringUtils
	{
		/**
		 * Quickie function to generate a line of a CSV file
		 * @param array $data
		 * @param string $delimiter
		 * @return unknown_type
		 */
		public static function arrayToCsvLine($data, $delimiter = ',')
		{
			$line = '';
			
			$column = 0;
			
			foreach($data as $value)
			{
				$escape = false;
				
				// Apparently Excel gets confused if the first value starts 'ID' in capitals! See http://support.microsoft.com/kb/323626
				if(($column == 0) && ($value == 'ID'))
					$escape = true;
				
				if(!$escape && preg_match('/[' . preg_quote($delimiter, '/') . '\\n\\r]/', $value))
					$escape = true;
					
				if($escape)
				{
					$value = str_replace('"', '""', $value);
					$value = '"' . $value . '"';
				}
				
				if($column > 0)
					$line .= ',';
					
				$line .= $value;
				
				++$column;
			}
			
			return $line . "\r\n";
		}
		
		public static function buildHttpQuery($data)
		{
			$str = '';
			
			foreach($data as $name => $value)
			{
				if($str != '')
					$str .= '&';
					
				$str .= rawurlencode($name) . '=' . rawurlencode($value); 
			}
			
			return $str;
		}
	}
	
	/**
	 * Returns a list of entities based on the given search criteria.
	 *
	 * @param array $meta_array Array of 'name' => 'value' pairs
	 * @param string $search String to search for in the title
	 * @param string $entity_type The type of entity to look for, eg 'site' or 'object'
	 * @param string $entity_subtype The subtype of the entity.
	 * @param int $owner_guid
	 * @param int $limit 
	 * @param int $offset
	 * @param true|false $reverse Reverse sort?
	 * @param int $site_guid The site to get entities for. Leave as 0 (default) for the current site; -1 for all sites.
	 * @param true|false $count If set to true, returns the total number of entities rather than a list. (Default: false)
	 * @return int|array List of ElggEntities, or the total number if count is set to false
	 */
	function polls_reporting_get_entities_from_metadata_multi_and_title_search_order_by_title(
				$meta_array, $search = '', $entity_type = "", $entity_subtype = "", $owner_guid = 0,
				$limit = 10, $offset = 0, $reverse = false, $site_guid = 0, $count = false)
	{
		global $CONFIG;
		
		if (!is_array($meta_array) || sizeof($meta_array) == 0)
		{
			return false;
		}
		
		$where = array();
		
		$mindex = 1;
		$join = "";

		foreach($meta_array as $meta_name => $meta_value)
		{
			$meta_n = get_metastring_id($meta_name);
			
			if(!is_array($meta_value))
				$meta_value = array($meta_value);
				
			foreach($meta_value as $value)
			{
				$meta_v = get_metastring_id($value);
	
				$join .= " JOIN {$CONFIG->dbprefix}metadata m{$mindex} on e.guid = m{$mindex}.entity_guid "; 
	
				if ($meta_name!="")
					$where[] = "m{$mindex}.name_id='$meta_n'";
	
				if ($meta_value!="")
					$where[] = "m{$mindex}.value_id='$meta_v'";
	
				$mindex++;
			}
		}
			
		$entity_type = sanitise_string($entity_type);
		$entity_subtype = get_subtype_id($entity_type, $entity_subtype);

		$limit = (int)$limit;
		$offset = (int)$offset;

		$owner_guid = (int) $owner_guid;
		
		$site_guid = (int) $site_guid;
		if ($site_guid == 0)
			$site_guid = $CONFIG->site_guid;
			
		if ($entity_type!="")
			$where[] = "e.type = '{$entity_type}'";

		if ($entity_subtype)
			$where[] = "e.subtype = {$entity_subtype}";

		if ($site_guid > 0)
			$where[] = "e.site_guid = {$site_guid}";

		if ($owner_guid > 0)
			$where[] = "e.container_guid = {$owner_guid}";
			
		if ($search != '')
			$where[] = "oe.title LIKE \"%" . sanitise_string($search) . "%\"";

		if ($count)
		{
			$query = "SELECT count(distinct e.guid) as total ";
		}
		else
		{
			$query = "SELECT distinct e.* ";
		}


		if (!$count)
			$query .= ", oe.title ";

		$join .= "LEFT JOIN `{$CONFIG->dbprefix}objects_entity` as oe on e.guid = oe.guid ";

		$query .= " from {$CONFIG->dbprefix}entities e {$join} where";

		foreach ($where as $w)
			$query .= " $w and ";

		$query .= get_access_sql_suffix("e"); // Add access controls
		$query .= ' and ' . get_access_sql_suffix("e"); // Add access controls
		
		if (!$count)
		{
			// Add order by

			if ($reverse)
				$query .= " order by oe.title desc, e.time_updated desc ";
			else
				$query .= " order by oe.title, e.time_updated ";

			// add limit
			$query .= " limit $offset, $limit";
			
			return get_data($query, "entity_row_to_elggstar");
		}
		else
		{
			if ($count = get_data_row($query)) {
				return $count->total;
			}
		}
		return false;
	}
	
?>