<?php 

require_once 'EhrlichAndreas/Pdo/Statement/Iterator.php';

/**
 *
 * @author Ehrlich, Andreas <ehrlich.andreas@googlemail.com>
 */
abstract class EhrlichAndreas_Pdo_Statement implements IteratorAggregate
{

    public $queryString;

    /**
     * Executes a prepared statement
     * 
     * @link http://www.php.net/manual/en/pdostatement.execute.php
     * @param
     *            input_parameters array[optional] <p>
     *            An array of values with as many elements as there are bound
     *            parameters in the SQL statement being executed.
     *            All values are treated as PDO::PARAM_STR.
     *            </p>
     *            <p>
     *            You cannot bind multiple values to a single parameter; for
     *            example,
     *            you cannot bind two values to a single named parameter in an
     *            IN()
     *            clause.
     *            </p>
     * @return bool Returns true on success or false on failure.
     */
    public function execute (array $input_parameters = null)
    {
    }

    /**
     * Fetches the next row from a result set
     * 
     * @link http://www.php.net/manual/en/pdostatement.fetch.php
     * @param
     *            fetch_style int[optional] <p>
     *            Controls how the next row will be returned to the caller. This
     *            value
     *            must be one of the PDO::FETCH_* constants,
     *            defaulting to PDO::FETCH_BOTH.
     *            <p>
     *            PDO::FETCH_ASSOC: returns an array indexed by column
     *            name as returned in your result set
     *            </p>
     * @param
     *            cursor_orientation int[optional] <p>
     *            For a PDOStatement object representing a scrollable cursor,
     *            this
     *            value determines which row will be returned to the caller.
     *            This value
     *            must be one of the PDO::FETCH_ORI_* constants,
     *            defaulting to PDO::FETCH_ORI_NEXT. To request a
     *            scrollable cursor for your PDOStatement object, you must set
     *            the
     *            PDO::ATTR_CURSOR attribute to
     *            PDO::CURSOR_SCROLL when you prepare the SQL
     *            statement with PDO::prepare.
     *            </p>
     * @param
     *            cursor_offset int[optional]
     * @return mixed The return value of this function on success depends on the
     *         fetch type. In
     *         all cases, false is returned on failure.
     */
    public function fetch ($fetch_style = null, $cursor_orientation = null, $cursor_offset = null)
    {
    }

    /**
     * Binds a parameter to the specified variable name
     * 
     * @link http://www.php.net/manual/en/pdostatement.bindparam.php
     * @param
     *            parameter mixed <p>
     *            Parameter identifier. For a prepared statement using named
     *            placeholders, this will be a parameter name of the form
     *            :name. For a prepared statement using
     *            question mark placeholders, this will be the 1-indexed
     *            position of
     *            the parameter.
     *            </p>
     * @param
     *            variable mixed <p>
     *            Name of the PHP variable to bind to the SQL statement
     *            parameter.
     *            </p>
     * @param
     *            data_type int[optional] <p>
     *            Explicit data type for the parameter using the PDO::PARAM_*
     *            constants.
     *            To return an INOUT parameter from a stored procedure,
     *            use the bitwise OR operator to set the PDO::PARAM_INPUT_OUTPUT
     *            bits
     *            for the data_type parameter.
     *            </p>
     * @param
     *            length int[optional] <p>
     *            Length of the data type. To indicate that a parameter is an
     *            OUT
     *            parameter from a stored procedure, you must explicitly set the
     *            length.
     *            </p>
     * @param
     *            driver_options mixed[optional] <p>
     *            </p>
     * @return bool Returns true on success or false on failure.
     */
    public function bindParam ($parameter, &$variable, $data_type = null, $length = null, $driver_options = null)
    {
    }

    /**
     * Bind a column to a PHP variable
     * 
     * @link http://www.php.net/manual/en/pdostatement.bindcolumn.php
     * @param
     *            column mixed <p>
     *            Number of the column (1-indexed) or name of the column in the
     *            result set.
     *            If using the column name, be aware that the name should match
     *            the
     *            case of the column, as returned by the driver.
     *            </p>
     * @param
     *            param mixed <p>
     *            Name of the PHP variable to which the column will be bound.
     *            </p>
     * @param
     *            type int[optional] <p>
     *            Data type of the parameter, specified by the PDO::PARAM_*
     *            constants.
     *            </p>
     * @param
     *            maxlen int[optional] <p>
     *            A hint for pre-allocation.
     *            </p>
     * @param
     *            driverdata mixed[optional] <p>
     *            Optional parameter(s) for the driver.
     *            </p>
     * @return bool Returns true on success or false on failure.
     */
    public function bindColumn ($column, &$param, $type = null, $maxlen = null, $driverdata = null)
    {
    }

    /**
     * Binds a value to a parameter
     * 
     * @link http://www.php.net/manual/en/pdostatement.bindvalue.php
     * @param
     *            parameter mixed <p>
     *            Parameter identifier. For a prepared statement using named
     *            placeholders, this will be a parameter name of the form
     *            :name. For a prepared statement using
     *            question mark placeholders, this will be the 1-indexed
     *            position of
     *            the parameter.
     *            </p>
     * @param
     *            value mixed <p>
     *            The value to bind to the parameter.
     *            </p>
     * @param
     *            data_type int[optional] <p>
     *            Explicit data type for the parameter using the PDO::PARAM_*
     *            constants.
     *            </p>
     * @return bool Returns true on success or false on failure.
     */
    public function bindValue ($parameter, $value, $data_type = null)
    {
    }

    /**
     * Returns the number of rows affected by the last SQL statement
     * 
     * @link http://www.php.net/manual/en/pdostatement.rowcount.php
     * @return int the number of rows.
     */
    public function rowCount ()
    {
    }

    /**
     * Returns a single column from the next row of a result set
     * 
     * @link http://www.php.net/manual/en/pdostatement.fetchcolumn.php
     * @param
     *            column_number int[optional] <p>
     *            0-indexed number of the column you wish to retrieve from the
     *            row. If
     *            no value is supplied, PDOStatement::fetchColumn
     *            fetches the first column.
     *            </p>
     * @return string PDOStatement::fetchColumn returns a single column
     *         in the next row of a result set.
     *         </p>
     *         <p>
     *         There is no way to return another column from the same row if you
     *         use PDOStatement::fetchColumn to retrieve data.
     */
    public function fetchColumn ($column_number = null)
    {
    }

    /**
     * Returns an array containing all of the result set rows
     * 
     * @link http://www.php.net/manual/en/pdostatement.fetchall.php
     * @param
     *            fetch_style int[optional] <p>
     *            Controls the contents of the returned array as documented in
     *            PDOStatement::fetch.
     *            </p>
     *            <p>
     *            To return an array consisting of all values of a single column
     *            from
     *            the result set, specify PDO::FETCH_COLUMN. You
     *            can specify which column you want with the
     *            column-index parameter.
     *            </p>
     *            <p>
     *            To fetch only the unique values of a single column from the
     *            result set,
     *            bitwise-OR PDO::FETCH_COLUMN with
     *            PDO::FETCH_UNIQUE.
     *            </p>
     *            <p>
     *            To return an associative array grouped by the values of a
     *            specified
     *            column, bitwise-OR PDO::FETCH_COLUMN with
     *            PDO::FETCH_GROUP.
     *            </p>
     * @param
     *            column_index int[optional] <p>
     *            Returns the indicated 0-indexed column when the value of
     *            fetch_style is
     *            PDO::FETCH_COLUMN.
     *            </p>
     * @param
     *            ctor_args array[optional] <p>
     *            Arguments of custom class constructor.
     *            </p>
     * @return array PDOStatement::fetchAll returns an array containing
     *         all of the remaining rows in the result set. The array represents
     *         each
     *         row as either an array of column values or an object with
     *         properties
     *         corresponding to each column name.
     *         </p>
     *         <p>
     *         Using this method to fetch large result sets will result in a
     *         heavy
     *         demand on system and possibly network resources. Rather than
     *         retrieving
     *         all of the data and manipulating it in PHP, consider using the
     *         database
     *         server to manipulate the result sets. For example, use the WHERE
     *         and
     *         SORT BY clauses in SQL to restrict results before retrieving and
     *         processing them with PHP.
     */
    public function fetchAll ($fetch_style = null, $column_index = null, array $ctor_args = null)
    {
    }

    /**
     * Fetches the next row and returns it as an object.
     * 
     * @link http://www.php.net/manual/en/pdostatement.fetchobject.php
     * @param
     *            class_name string[optional] <p>
     *            Name of the created class.
     *            </p>
     * @param
     *            ctor_args array[optional] <p>
     *            Elements of this array are passed to the constructor.
     *            </p>
     * @return mixed an instance of the required class with property names that
     *         correspond to the column names &return.falseforfailure;.
     */
    public function fetchObject ($class_name = null, array $ctor_args = null)
    {
    }

    /**
     * Fetch the SQLSTATE associated with the last operation on the statement
     * handle
     * 
     * @link http://www.php.net/manual/en/pdostatement.errorcode.php
     * @return string Identical to PDO::errorCode, except that
     *         PDOStatement::errorCode only retrieves error codes
     *         for operations performed with PDOStatement objects.
     */
    public function errorCode ()
    {
    }

    /**
     * Fetch extended error information associated with the last operation on
     * the statement handle
     * 
     * @link http://www.php.net/manual/en/pdostatement.errorinfo.php
     * @return array PDOStatement::errorInfo returns an array of
     *         error information about the last operation performed by this
     *         statement handle. The array consists of the following fields:
     *         <tr valign="top">
     *         <td>Element</td>
     *         <td>Information</td>
     *         </tr>
     *         <tr valign="top">
     *         <td>0</td>
     *         <td>SQLSTATE error code (a five characters alphanumeric
     *         identifier defined
     *         in the ANSI SQL standard).</td>
     *         </tr>
     *         <tr valign="top">
     *         <td>1</td>
     *         <td>Driver specific error code.</td>
     *         </tr>
     *         <tr valign="top">
     *         <td>2</td>
     *         <td>Driver specific error message.</td>
     *         </tr>
     */
    public function errorInfo ()
    {
    }

    /**
     * Set a statement attribute
     * 
     * @link http://www.php.net/manual/en/pdostatement.setattribute.php
     * @param
     *            attribute int
     * @param
     *            value mixed
     * @return bool Returns true on success or false on failure.
     */
    public function setAttribute ($attribute, $value)
    {
    }

    /**
     * Retrieve a statement attribute
     * 
     * @link http://www.php.net/manual/en/pdostatement.getattribute.php
     * @param
     *            attribute int
     * @return mixed the attribute value.
     */
    public function getAttribute ($attribute)
    {
    }

    /**
     * Returns the number of columns in the result set
     * 
     * @link http://www.php.net/manual/en/pdostatement.columncount.php
     * @return int the number of columns in the result set represented by the
     *         PDOStatement object. If there is no result set,
     *         PDOStatement::columnCount returns 0.
     */
    public function columnCount ()
    {
    }

    /**
     * Returns metadata for a column in a result set
     * 
     * @link http://www.php.net/manual/en/pdostatement.getcolumnmeta.php
     * @param
     *            column int <p>
     *            The 0-indexed column in the result set.
     *            </p>
     * @return array an associative array containing the following values
     *         representing
     *         the metadata for a single column:
     *         </p>
     *         <table>
     *         Column metadata
     *         <tr valign="top">
     *         <td>Name</td>
     *         <td>Value</td>
     *         </tr>
     *         <tr valign="top">
     *         <td>native_type</td>
     *         <td>The PHP native type used to represent the column value.</td>
     *         </tr>
     *         <tr valign="top">
     *         <td>driver:decl_type</td>
     *         <td>The SQL type used to represent the column value in the
     *         database.
     *         If the column in the result set is the result of a function, this
     *         value
     *         is not returned by PDOStatement::getColumnMeta.
     *         </td>
     *         </tr>
     *         <tr valign="top">
     *         <td>flags</td>
     *         <td>Any flags set for this column.</td>
     *         </tr>
     *         <tr valign="top">
     *         <td>name</td>
     *         <td>The name of this column as returned by the database.</td>
     *         </tr>
     *         <tr valign="top">
     *         <td>table</td>
     *         <td>The name of this column's table as returned by the
     *         database.</td>
     *         </tr>
     *         <tr valign="top">
     *         <td>len</td>
     *         <td>The length of this column. Normally -1 for
     *         types other than floating point decimals.</td>
     *         </tr>
     *         <tr valign="top">
     *         <td>precision</td>
     *         <td>The numeric precision of this column. Normally
     *         0 for types other than floating point
     *         decimals.</td>
     *         </tr>
     *         <tr valign="top">
     *         <td>pdo_type</td>
     *         <td>The type of this column as represented by the
     *         PDO::PARAM_* constants.</td>
     *         </tr>
     *         </table>
     *         <p>
     *         Returns false if the requested column does not exist in the
     *         result set,
     *         or if no result set exists.
     */
    public function getColumnMeta ($column)
    {
    }

    /**
     * Set the default fetch mode for this statement
     * 
     * @link http://www.php.net/manual/en/pdostatement.setfetchmode.php
     * @param
     *            mode int <p>
     *            The fetch mode must be one of the PDO::FETCH_* constants.
     *            </p>
     * @return bool 1 on success&return.falseforfailure;.
     */
    public function setFetchMode ($mode)
    {
    }

    /**
     * Advances to the next rowset in a multi-rowset statement handle
     * 
     * @link http://www.php.net/manual/en/pdostatement.nextrowset.php
     * @return bool Returns true on success or false on failure.
     */
    public function nextRowset ()
    {
    }

    /**
     * Closes the cursor, enabling the statement to be executed again.
     * 
     * @link http://www.php.net/manual/en/pdostatement.closecursor.php
     * @return bool Returns true on success or false on failure.
     */
    public function closeCursor ()
    {
    }

    /**
     * Dump a SQL prepared command
     * 
     * @link http://www.php.net/manual/en/pdostatement.debugdumpparams.php
     * @return bool
     */
    public function debugDumpParams ()
    {
    }

    public function getIterator ()
    {
        return new EhrlichAndreas_Pdo_Statement_Iterator($this);
    }
    
    // final public function __wakeup() {}
    
    // final public function __sleep() {}
}

?>