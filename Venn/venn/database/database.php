<?php

namespace Venn\database;

use Venn\core\Kernel;

/**
 * Root class for all database interacting objects.
 */
class Database {

    protected $instance;

    public function __construct($database = null) {
        $this->instance = Kernel::db();
        if (!empty($database)) {
            mysqli_select_db($this->instance, $database);
        }
    }

    public function __destruct() {
        if (!empty($this->instance)) {
            mysqli_close($this->instance);
        }
    }

    /**
     *  This creates an entry in a database table
     *  @param String $table This is the table where the data is to be inserted into.
     *  @param Array $data This is an associative array containing the data being inserted to the table @see $table.
     */
    protected function insert($table, $data) {
        
    }

    protected function query() {
        
    }

    protected function update() {
        
    }

    protected function delete() {
        
    }

    protected function stmt(&$stmt, $expected = null, &...$args) {
        $types = '';
        for ($i = 0; $i < count($args); $i++) {
            if (is_int($args[$i]) || is_float($args[$i])) {
                $types.='i';
            } elseif (is_string($args[$i]) || is_numeric($args[$i])) {
                $types.='s';
            } elseif (is_file($args[$i])) {
                $types.='b';
            }
        }
        if (!is_null($expected) && !empty($expected) && $expected) {
            if (strcasecmp($types, $expected) !== 0) {

                return false;
            }
        }
        array_unshift($args, $stmt, $types);
        return call_user_func_array('mysqli_stmt_bind_param', $args);
    }

}
