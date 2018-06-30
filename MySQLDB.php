<?php

// $pathtothebills = '../../../TheBills/';
require_once('Constants.php');

// shouldn't need these - in Constants.php above 
//
// define( 'DB_USER', 'YOUR DATABASE USER' );
// define( 'DB_PASS', 'YOUR DATABASE PASS' );
// define( 'DB_NAME', 'YOUR DATABASE NAME' );
// define( 'DB_HOST', 'localhost' );
// define( 'DB_ENCODING', '' );

if ( !class_exists( 'DB' ) ) {
	class DB {
		public function __construct($db_name, $db_user, $db_pass, $db_host = 'localhost') {
			$this->db = new mysqli($db_host, $db_user, $db_pass, $db_name);
		}
		public function query($query) {
			$result = $this->db->query($query);
			
			$results = [];  // results could be empty so initialialize an empty array
			// while ( $row = $result->fetch_object() ) {
			while ( $row = $result->fetch_array() ) {
				$results[] = $row;
			}

			return $results;
		}
		public function get_results($query, $args = array()) {
			if (empty($args)) {
				return $this->query($query);
			}
			
            if (!$stmt = $this->db->prepare($query)) {
            	return false;	
            }
            
            foreach ($args as $value => $type) {
                $types .= $type;
                $values[] = &$value;
            }

            $types = (array) $types; 
            $params = array_merge($types, $values);

            call_user_func_array(array($stmt, 'bind_param'), $params);
            
            $stmt->execute();
            $result = $stmt->get_result();

            // while ($row = $result->fetch_object()) {
            while ($row = $result->fetch_array()) {
            	$results[] = $row;
            }

			if (!empty($results)) {
            	return $results;
			}
			
			return false;
        }
		public function get_row($table, $id) {
			$stmt = $this->db->prepare("SELECT * FROM {$table} WHERE ID = ?");
			$stmt->bind_param('i', $id);
			$stmt->execute();
			
			$result = $stmt->get_result();
			// $object = $result->fetch_object();
			// if (!is_null($object)) {
			// 	return $object;
			// }

			$rRow = $result->fetch_array();
			if (!is_null($rRow)) {
				return $rRow;
			}
			
			return false;
		}
		public function insert($table, $data, $format) {
			// Check for $table or $data not set
			if ( empty( $table ) || empty( $data ) ) {
				return false;
			}

			// Cast $data and $format to arrays
			$data = (array) $data;
			$format = (array) $format;
			
			// Build format string
			$format = implode('', $format); 
			
			list( $fields, $placeholders, $values ) = $this->prep_query($data);

			// Prepend $format onto $values
			array_unshift($values, $format); 

			// Prepare our query for binding
			$stmt = $this->db->prepare("INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})");

			// Dynamically bind values
			call_user_func_array( array( $stmt, 'bind_param'), $this->ref_values($values));
			
			// Execute the query
			$stmt->execute();
			
			// Check for successful insertion
			if ( $stmt->affected_rows ) {
				return true;
			}
			
			return false;
		}
		public function update($table, $data, $format, $where, $where_format) {
			// Check for $table or $data not set
			if ( empty( $table ) || empty( $data ) ) {
				return false;
			}
			
			// Cast $data and $format to arrays
			$data = (array) $data;
			$format = (array) $format;
			
			// Build format array
			$format = implode('', $format); 
			$where_format = implode('', $where_format); 
			$format .= $where_format;
			
			list( $fields, $placeholders, $values ) = $this->prep_query($data, 'update');
			
			// Format where clause
			$where_clause = '';
			$where_values = '';
			$count = 0;
			
			foreach ( $where as $field => $value ) {
				if ( $count > 0 ) {
					$where_clause .= ' AND ';
				}
				
				$where_clause .= $field . '=?';
				$where_values[] = $value;
				
				$count++;
			}

			// Prepend $format onto $values
			array_unshift($values, $format);
			$values = array_merge($values, $where_values);

			// Prepary our query for binding
			$stmt = $this->db->prepare("UPDATE {$table} SET {$placeholders} WHERE {$where_clause}");
			
			// Dynamically bind values
			call_user_func_array( array( $stmt, 'bind_param'), $this->ref_values($values));
			
			// Execute the query
			$stmt->execute();
			
			// Check for successful insertion
			if ( $stmt->affected_rows ) {
				return true;
			}
			
			return false;
		}
		public function delete($table, $id) {
			// Prepary our query for binding
			$stmt = $this->db->prepare("DELETE FROM {$table} WHERE ID = ?");
			
			// Dynamically bind values
			$stmt->bind_param('i', $id);
			
			// Execute the query
			$stmt->execute();
			
			// Check for successful insertion
			if ( $stmt->affected_rows ) {
				return true;
			}
			
			return false;
		}
		private function prep_query($data, $type='insert') {
			// Instantiate $fields and $placeholders for looping
			$fields = '';
			$placeholders = '';
			$values = array();
			
			// Loop through $data and build $fields, $placeholders, and $values			
			foreach ( $data as $field => $value ) {
				$fields .= "{$field},";
				$values[] = $value;
				
				if ( $type == 'update') {
					$placeholders .= $field . '=?,';
				} else {
					$placeholders .= '?,';
				}
				
			}
			
			// Normalize $fields and $placeholders for inserting
			$fields = substr($fields, 0, -1);
			$placeholders = substr($placeholders, 0, -1);
			
			return array( $fields, $placeholders, $values );
		}
		private function ref_values($array) {
			$refs = array();

			foreach ($array as $key => $value) {
				$refs[$key] = &$array[$key]; 
			}

			return $refs; 
		}
	}
}

// $db = new DB(DB_NAME, DB_USER, DB_PASS, DB_HOST);
// $db = new DB(DB_NAME, DB_USER, DB_PASSWORD, DB_HOST);

//print_r($db->query("SELECT * FROM objects"));
//print_r($db->insert('objects', array('post_title'=>'Abstraction Test', 'post_content' => 'Abstraction test content'), array('s', 's')));
//print_r($db->update('objects', array('post_title'=>'Abstraction Test Update', 'post_content' => 'Abstraction test update content'), array('s', 's'), array('ID'=>3), array('d')));
// print_r($db->get_results("SELECT * FROM objects"));
//print_r($db->get_row('objects', 2));
//print_r($db->delete('objects', 1));