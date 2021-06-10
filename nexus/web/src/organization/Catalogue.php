<?php

require_once(dirname(__FILE__) . "/../framework/PgDb.php");
require_once(Utilities::getSrcRoot() . "/user/User.php");
require_once(Utilities::getSrcRoot() . "/organization/Organization.php");

class Catalogue {
	
	public static function getEntries($groupId, $networkId, $inputString, $filters) {
		
		$conf = array('append' => true, 'mode' => 0644, 'timeFormat' => '%X %x');	
		$logger = Log::singleton("file", Utilities::getLogRoot() ."/directory.log", "", $conf, PEAR_LOG_DEBUG);
				
		$results = array();
		$terms = trim(Utilities::strip2($inputString));
		
		//$logger->log("Raw terms: " . $terms);
					
		// TODO: Escape apostrophes instead of stripping
		// TODO: solve for 2 orgs with same name
		
		// If we have free search terms, process the first three.
		if (strlen($terms) > 0) {
	
			$searchTerms = preg_split("/[\s,]+/", $terms);
			//$logger->log("Raw terms: " . print_r($searchTerms, true));
			$counter = 0;
		
			while ($counter < 3 && isset($searchTerms[$counter])) {
							
				if (preg_match("/^[Tt][Hh][Ee]$/", $searchTerms[$counter]) ||
						preg_match("/^[Aa][Nn]$/", $searchTerms[$counter]) ||
						preg_match("/^[Aa]$/", $searchTerms[$counter]) ||
						strlen($searchTerms[$counter]) < 2
						) {
					$counter++;
					continue;
				}
						
				// TODO: Iterating a cursor dismantles a cursor, so we have to make copies 
				// http://stackoverflow.com/questions/8735192/how-to-rewind-the-pg-fetch-assocresult-null-iterator
				
				//$cursor = self::freeSearch($searchTerms[$counter], $networkId);
				//$cursor1 = $cursor2 = $cursor3 = $cursor4 = $cursor5 = $cursor6 = $cursor7 = $cursor;
				$cursor1 = self::freeSearch($searchTerms[$counter], $networkId);
				$cursor2 = self::freeSearch($searchTerms[$counter], $networkId);
				$cursor3 = self::freeSearch($searchTerms[$counter], $networkId);
				$cursor4 = self::freeSearch($searchTerms[$counter], $networkId);
				$cursor5 = self::freeSearch($searchTerms[$counter], $networkId);
				$cursor6 = self::freeSearch($searchTerms[$counter], $networkId);
				$cursor7 = self::freeSearch($searchTerms[$counter], $networkId);
				
				while ($pass1 = pg_fetch_array($cursor1)) { 
					if (!strcmp($pass1['type'], "Organization")) {
						if (array_key_exists($pass1['name'], $results)) {
							// do nothing - we already have an index for this org in the data set
						} else {
							$results[$pass1['name']] = array(
								"Programs" => array(),
								"People" =>  array(),
								"Contact" => array(),
								"Language" => array(),
								"Topic" => array(),
								"Location" => array(),
								"OrgId" => $pass1['id']
							);
						}		
					}
				}
																			
				// This is what is happening below, as pattern for the rest
				// $pass2 holds all matching data records from the original search, each typed. Here we are pulling out the "Person" record types
				// For each "Person" record type, we go get the orgs associated with this Person (user_organization table)
				// For each org, loop
				// If the organization name is already indexed in the master $results array
				// BUT
				// This particular Person is NOT already indexed therein
				// 		Set a Person associative index inside the $results record, where User Id is key, Person name is value
				// Otherwise create a new index for this organization inside $results and include this Person
			
				while ($pass2 = pg_fetch_array($cursor2)) { 
					if (!strcmp($pass2['type'], "Person")) {
						$innerCursor2 = User::getUserOrgRelationsByUserId($pass2['id']);
						while ($inner2 = pg_fetch_array($innerCursor2)) {
							if (array_key_exists($inner2['name'], $results)) {
								if (!in_array($pass2['name'], $results[$inner2['name']]["People"])) {
									$results[$inner2['name']]['People'][$pass2['id']] = $pass2['name'];
								}
							} else {					
								$results[$inner2['name']] = array(
									"Programs" => array(),
									"People" => array($pass2['id'] => $pass2['name']),
						   		"Contact" => array(),
									"Language" => array(),
									"Topic" => array(),
									"Location" => array(),
									"OrgId" => $inner2['id']
								);
							}
						}
					}
				}
		
				while ($pass5 = pg_fetch_array($cursor5)) { 
					if (!strcmp($pass5['type'], "Contact")) {
						$innerCursor5 = Organization::getOrganizationByContactId($pass5['id']);
						while ($inner5 = pg_fetch_array($innerCursor5)) {
							if (array_key_exists($inner5['name'], $results)) {
								if (!in_array($pass5['name'], $results[$inner5['name']]["Contact"])) {
									array_push($results[$inner5['name']]["Contact"], $pass5['name']);
								}
							} else {
								$results[$inner5['name']] = array(
									"Programs" => array(),
									"People" => array(),
							  	"Contact" => array($pass5['name']),
									"Language" => array(),
									"Topic" => array(),
									"Location" => array(),
									"OrgId" => $inner5['id']
								);
							}
						}
					}
				}

				while ($pass3 = pg_fetch_array($cursor3)) { 
					if (!strcmp($pass3['type'], "Language")) {
						$innerCursor3 = Organization::getOrganizationByLanguageId($pass3['id']);
						while ($inner3 = pg_fetch_array($innerCursor3)) {
							if (array_key_exists($inner3['name'], $results)) {
								if (!in_array($pass3['name'], $results[$inner3['name']]["Language"])) {
									array_push($results[$inner3['name']]["Language"], $pass3['name']);
								}
							} else {
								$results[$inner3['name']] = array(
									"Programs" => array(),
									"People" => array(),
									"Contact" => array(),
									"Language" => array($pass3['name']),
									"Topic" => array(),
							  	"Location" => array(),
									"OrgId" => $inner3['id']
								);
							}
						}
					}
				}
		
				// When this opens up, adjust directorySummary.php: indexes inside loop at line 62-ish
				while ($pass4 = pg_fetch_array($cursor4)) { 
					if (!strcmp($pass4['type'], "Program")) {
						$innerCursor4 = Organization::getOrganizationByProgramId($pass4['id']);
						while ($inner4 = pg_fetch_array($innerCursor4)) {
							if (array_key_exists($inner4['name'], $results)) {
								if (!in_array($pass4['name'], $results[$inner4['name']]["Programs"])) {
									array_push($results[$inner4['name']]["Programs"], $pass4['name']);
								}
							} else {
								$results[$inner4['name']] = array(
									"Programs" => array($pass4['name']),
									"People" => array(),
									"Contact" => array(),
									"Language" => array(),
									"Topic" => array(),
									"Location" => array(),
									"OrgId" => $inner4['id']
								);
							}
						}
					}
				}
			
				while ($pass7 = pg_fetch_array($cursor7)) { 
					if (!strcmp($pass7['type'], "Topic")) {
						$innerCursor7 = Organization::getOrganizationByTopicId($pass7['id']);
						while ($inner7 = pg_fetch_array($innerCursor7)) {
							if (array_key_exists($inner7['name'], $results)) {
								if (!in_array($pass7['name'], $results[$inner7['name']]["Topic"])) {
									array_push($results[$inner7['name']]["Topic"], $pass7['name']);
								}
							} else {
								$results[$inner7['name']] = array(
									"Programs" => array(),
									"People" => array(),
							  	"Contact" => array(),
									"Language" => array(),
									"Topic" => array($pass7['name']),
									"Location" => array(),
									"OrgId" => $inner7['id']
								);
							}
						}
					}
				}		
			
				while ($pass6 = pg_fetch_array($cursor6)) { 
					if (!strcmp($pass6['type'], "Location")) {
						$innerCursor6 = Organization::getOrganizationByLocationId($pass6['id']);
						while ($inner6 = pg_fetch_array($innerCursor6)) {
							if (array_key_exists($inner6['name'], $results)) {
								if (!in_array($pass6['name'], $results[$inner6['name']]["Location"])) {
									array_push($results[$inner6['name']]["Location"], $pass6['name']);
								}
							} else {
								$results[$inner6['name']] = array(
									"Programs" => array(),
									"People" => array(),
							  	"Contact" => array(),
									"Language" => array(),
									"Topic" => array(),
									"Location" => array($pass6['name']),
									"OrgId" => $inner6['id']
								);
							}
						}
					}
				}
				
				$counter++;
			}
			
			if (isset($filters['specialty']) && $filters['specialty'] > 0) {
				foreach ($results as $key=>$val) {
					$row = pg_fetch_row(Organization::organizationTopicExists($val['OrgId'], $filters['specialty']));
					$topicMatch = $row[0];
					if (strcmp($topicMatch, "t")) {
						unset($results[$key]);
					}
				}	
			}
	
			if (isset($filters['type']) && strcmp($filters['type'], "0")) {
				foreach ($results as $key=>$val) {
					$row = pg_fetch_row(Organization::organizationTypeExists($val['OrgId'], $filters['type']));
					$typeMatch = $row[0];
					if (strcmp($typeMatch, "t")) {
						unset($results[$key]);
					}
				}	
			}
			
			if (isset($filters['affiliation']) && $filters['affiliation'] > 0) {
				foreach ($results as $key=>$val) {
					$row = pg_fetch_row(Organization::organizationAffiliationExists($val['OrgId'], $filters['affiliation']));
					$affiliationMatch = $row[0];
					if (strcmp($affiliationMatch, "t")) {
						unset($results[$key]);
					}
				}	
			}

			
		} else if (isset($filters) && count($filters) > 0) {
		// We get here if there are no free search terms. So, run filters against the entire network
		
			$cursor = Organization::getOrganizationsByNetworkId($networkId);
			
			while ($pass = pg_fetch_array($cursor)) { 
				if (!strcmp($pass['type'], "Organization")) {
					if (array_key_exists($pass['name'], $results)) {
						// do nothing - we already have an index for this org in the data set
					} else {
						$results[$pass['name']] = array(
							"Programs" => array(),
							"People" =>  array(),
							"Contact" => array(),
							"Language" => array(),
							"Topic" => array(),
							"Location" => array(),
							"OrgId" => $pass['id']
						);
					}
				}
			}
								
			if (isset($filters['specialty']) && $filters['specialty'] > 0) {
				foreach ($results as $key=>$val) {
					$row = pg_fetch_row(Organization::organizationTopicExists($val['OrgId'], $filters['specialty']));
					$topicMatch = $row[0];
					if (strcmp($topicMatch, "t")) {
						unset($results[$key]);
					}
				}	
			}
	
			if (isset($filters['type']) && strcmp($filters['type'], "0")) {
				foreach ($results as $key=>$val) {
					$row = pg_fetch_row(Organization::organizationTypeExists($val['OrgId'], $filters['type']));
					$typeMatch = $row[0];
					if (strcmp($typeMatch, "t")) {
						unset($results[$key]);
					}
				}	
			}
			
			if (isset($filters['affiliation']) && $filters['affiliation'] > 0) {
				foreach ($results as $key=>$val) {
					$row = pg_fetch_row(Organization::organizationAffiliationExists($val['OrgId'], $filters['affiliation']));
					$affiliationMatch = $row[0];
					if (strcmp($affiliationMatch, "t")) {
						unset($results[$key]);
					}
				}	
			}
						
		} else {
			// If we get here, we had no search terms or filters
			
			$cursor = Organization::getOrganizationsByNetworkId($networkId);
			
			while ($pass = pg_fetch_array($cursor)) { 
				if (!strcmp($pass['type'], "Organization")) {
					if (array_key_exists($pass['name'], $results)) {
						// do nothing - we already have an index for this org in the data set
					} else {
						$results[$pass['name']] = array(
							"Programs" => array(),
							"People" =>  array(),
							"Contact" => array(),
							"Language" => array(),
							"Topic" => array(),
							"Location" => array(),
							"OrgId" => $pass['id']
						);
					}
				}
			}
		}
				
		// We do this to be JSON-friendly
		$indexedResults = array();
		$orgIdList = array();
		ksort($results, SORT_NATURAL | SORT_FLAG_CASE);
		$counter1 = 0;
		foreach ($results as $key1=>$val1) {
			$indexedResults[$counter1]["name"] = $key1;
			$counter2 = 0;
			foreach ($val1 as $key2=>$val2) {
				// If this is the OrgId item
				if ($key2 === "OrgId") {
					$indexedResults[$counter1]["content"][$counter2][strtolower($key2)] = $val2;
					array_push($orgIdList, $val2);
				} else {
					$counter3 = 0;
					if (count($val2) == 0) {
						$indexedResults[$counter1]["content"][$counter2][strtolower($key2)][$counter3] = "";				
					} else {
						foreach ($val2 as $key3=>$val3) {				
							$indexedResults[$counter1]["content"][$counter2][strtolower($key2)][$counter3] = $val3;
							$counter3++;
						}	
					}
					$counter3++;
				}
				$counter2++;
			}
			$counter1++;
		}
					
		/* Yes, this code keeps getting more and more awesome... */	
		$result = Organization::getGeoByOrgIds($orgIdList);

		$geoResults = array();
		
		while ($row = pg_fetch_array($result)) {
			$orgId = $row['oid'];
			$geoResults[$orgId]['lat'] = $row['lat'];
			$geoResults[$orgId]['lng'] = $row['long'];
			$geoResults[$orgId]['title'] = $row['name'];
		}

		$multiObjectResults = array();		
		$multiObjectResults['orgEntry'] = $indexedResults;
		$multiObjectResults['geoEntry'] = $geoResults;
		return $multiObjectResults;

	}
	
	private static function freeSearch($term, $networkId) {
		// TODO: use citext index in db instead of lower() function here
		// http://stackoverflow.com/questions/7005302/postgresql-how-to-make-not-case-sensitive-queries
		
		$logger = Log::singleton("file", Utilities::getLogRoot() ."/directory.log", "", $conf, PEAR_LOG_DEBUG);
		
		$query = "
			select 'Organization' as type, o.id as id, o.name as name
			from organization o, organization_organization oo
			where 
				lower(o.name) like lower('%' || $1 || '%')
			and o.id = oo.organization_to_fk
			and oo.organization_from_fk = $2
			and oo.relationship ='parent'
			and o.suspend_dttm is null
			
			union
			
			select distinct 'Person' as type, u.id as id, (u.fname || ' ' || u.mname || ' ' || u.lname || '::' || u.username) as name
			from public.user u, organization_organization oo, user_organization uo, organization o
			where (
				lower(u.fname) like lower('%' || $1 || '%')
				or lower(u.lname) like lower('%' || $1 || '%')
				or lower(u.mname) like lower('%' || $1 || '%')
				or lower(u.nickname) like lower('%' || $1 || '%')
			)
			and u.id = uo.user_fk
			and uo.organization_fk = oo.organization_to_fk
			and oo.organization_from_fk = $2
			and oo.relationship ='parent'
			and o.id = oo.organization_to_fk
            and o.suspend_dttm is null
            
			union
			
			select distinct 'Contact' as type, c.id as id, c.name as name
			from contact c, organization_organization oo, organization_contact oc, organization o
			where (
				lower(c.name) like lower('%' || $1 || '%')
				or lower(c.title) like lower('%' || $1 || '%')
			)
			and c.id = oc.contact_fk
			and oc.organization_fk = oo.organization_to_fk
			and oo.organization_from_fk = $2
			and oo.relationship ='parent'
			and o.id = oo.organization_to_fk
            and o.suspend_dttm is null
			
			union
			
			select distinct 'Program' as type, p.id as id, p.name as name
			from program p, organization_organization oo, organization_program op, organization o
			where (
				lower(p.name) like lower('%' || $1 || '%')
				or lower(p.description) like lower('%' || $1 || '%')
			)
			and p.id = op.program_fk
			and op.organization_fk = oo.organization_to_fk
			and oo.organization_from_fk = $2
			and oo.relationship ='parent'
			and o.id = oo.organization_to_fk
            and o.suspend_dttm is null
			
			union
			
			select 'Language' as type, l.id as id, l.language as name
			from language l, organization_organization oo, organization_language ol, organization o
			where 
				lower(l.language) like lower('%' || $1 || '%')		
			and l.id = ol.language_fk
			and ol.organization_fk = oo.organization_to_fk
			and oo.organization_from_fk = $2
			and oo.relationship ='parent'
			and o.id = oo.organization_to_fk
            and o.suspend_dttm is null
            
			union
			
			select 'Topic' as type, t.id as id, t.name as name
			from topic t, organization_organization oo, organization_topic ot, organization o
			where 
				lower(t.name) like lower('%' || $1 || '%')		
			and t.id = ot.topic_fk
			and ot.organization_fk = oo.organization_to_fk
			and oo.organization_from_fk = $2
			and oo.relationship ='parent'
			and o.id = oo.organization_to_fk
            and o.suspend_dttm is null
					
			union
			
			select distinct 'Location' as type, loc.id as id, loc.municipality || ', ' || loc.region2 as name
			from location loc, organization_organization oo, organization_location oloc, organization o
			where (
				lower(loc.address1) like lower('%' || $1 || '%')
				or lower(loc.address2) like lower('%' || $1 || '%')
				or lower(loc.municipality) like lower('%' || $1 || '%')
				or lower(loc.region1) like lower('%' || $1 || '%')
				or lower(loc.region2) like lower('%' || $1 || '%')
				or lower(loc.country) like lower('%' || $1 || '%')
				or lower(loc.postal_code) like lower('%' || $1 || '%')
			)		
			and loc.id = oloc.location_fk
			and oloc.organization_fk = oo.organization_to_fk
			and oo.organization_from_fk = $2
			and oo.relationship ='parent'
			and o.id = oo.organization_to_fk
            and o.suspend_dttm is null
			";
			
			//$logger->log($term);
			//$logger->log($networkId);
			//$logger->log($query);
			
			return PgDatabase::psExecute($query, array($term, $networkId));			
			
	}
	
}

?>
