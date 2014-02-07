<?php

require_once 'EhrlichAndreas/Pdo/Exception.php';

require_once 'EhrlichAndreas/Pdo/Abstract.php';

require_once 'EhrlichAndreas/Pdo/Adapter/Abstract.php';

require_once 'EhrlichAndreas/Pdo/Adapter/Sqlite/Statement.php';

/**
 *
 * @author Ehrlich, Andreas <ehrlich.andreas@googlemail.com>
 */
class EhrlichAndreas_Pdo_Adapter_Sqlite extends EhrlichAndreas_Pdo_Adapter_Abstract
{
    private $autocommit;

    public function __construct(&$dsn, &$username, &$password, &$driver_options)
    {
        if(!extension_loaded('sqlite'))
        {
            throw new EhrlichAndreas_Pdo_Exception('could not find extension');
        }

        parent::__construct($dsn, $username, $password, $driver_options);
        
        $this->driver_quote_type = 1;
    }

    public function beginTransaction()
    {
        parent::beginTransaction();

        if(!sqlite_exec($this->link, 'BEGIN'))
        {
            $this->set_driver_error(null, EhrlichAndreas_Pdo_Abstract::ERRMODE_EXCEPTION, 'beginTransaction');
        }

        return true;
    }

    public function commit()
    {
        parent::commit();

        if(!sqlite_exec($this->link, 'COMMIT'))
        {
            $this->set_driver_error(null, EhrlichAndreas_Pdo_Abstract::ERRMODE_EXCEPTION, 'commit');
        }

        $this->setAttribute(EhrlichAndreas_Pdo_Abstract::ATTR_AUTOCOMMIT, 1);
        
        return true;
    }

    public function exec(&$statement)
    {
        if(@sqlite_exec($this->link, $statement))
        {
            return sqlite_changes($this->link);
        }

        return false;
    }

    public function getAttribute($attribute, &$source = null, $func = 'PDO::getAttribute', &$last_error = null)
    {
        switch($attribute)
        {
            case EhrlichAndreas_Pdo_Abstract::ATTR_AUTOCOMMIT:
                
                return $this->autocommit;
                
                break;

            case EhrlichAndreas_Pdo_Abstract::ATTR_CLIENT_VERSION:
                
                return sqlite_libversion();
                
                break;

            case EhrlichAndreas_Pdo_Abstract::ATTR_SERVER_VERSION:
                
                return sqlite_libversion();
                
                break;

            default:
                
                return parent::getAttribute($attribute, $source, $func, $last_error);
                
                break;
        }
    }

    public function lastInsertId($name = '')
    {
        return sqlite_last_insert_rowid($this->link);
    }

    public function quote(&$param, $parameter_type = -1)
    {
        switch($parameter_type)
        {
            case EhrlichAndreas_Pdo_Abstract::PARAM_BOOL:
                
                return $param ? 1 : 0;
                
                break;

            case EhrlichAndreas_Pdo_Abstract::PARAM_NULL:
                
                return 'NULL';
                
                break;

            case EhrlichAndreas_Pdo_Abstract::PARAM_INT:
                
                return is_null($param) ? 'NULL' : (is_int($param) ? $param : (float)$param);
                
                break;

            default:
                
                return '\'' . sqlite_escape_string($param) . '\'';
                
                break;
        }
    }

    public function rollBack()
    {
        parent::rollback();

        if(!sqlite_exec($this->link, 'ROLLBACK'))
        {
            $this->set_driver_error(null, EhrlichAndreas_Pdo_Abstract::ERRMODE_EXCEPTION, 'rollBack');
        }

        $this->setAttribute(EhrlichAndreas_Pdo_Abstract::ATTR_AUTOCOMMIT, $this->driver_options[EhrlichAndreas_Pdo_Abstract::ATTR_AUTOCOMMIT]);
        
        return true;
    }

    public function setAttribute($attribute, $value, &$source = null, $func = 'PDO::setAttribute', &$last_error = null)
    {
        if($source == null)
        {
            $source =& $this->driver_options;
        }

        switch($attribute)
        {
            case EhrlichAndreas_Pdo_Abstract::ATTR_AUTOCOMMIT:
                
                $this->autocommit = $value ? 1 : 0;
                
                return true;
                
                break;

            default:
                
                return parent::setAttribute($attribute, $value, $source, $func, $last_error);
                
                break;
        }

        return false;
    }

    public function set_driver_error($state = null, $mode = EhrlichAndreas_Pdo_Abstract::ERRMODE_SILENT, $func = '')
    {
        $errno = sqlite_last_error($this->link);
        
        if($state === null)
        {
            $state = 'HY000';
        }

        $this->set_error($errno, sqlite_error_string($errno), $state, $mode, $func);
    }

    protected function connect(&$username, &$password, &$driver_options)
    {
        $database = key($this->dsn);
        
        $error = '';

        if(isset($driver_options[EhrlichAndreas_Pdo_Abstract::ATTR_PERSISTENT]) && $driver_options[EhrlichAndreas_Pdo_Abstract::ATTR_PERSISTENT])
        {
            $this->link = @sqlite_popen($database, 0666, $error);
        }
        else
        {
            $this->link = @sqlite_open($database, 0666, $error);
        }

        if(!$this->link)
        {
            $this->set_error(0, $error, 'HY000', EhrlichAndreas_Pdo_Abstract::ERRMODE_EXCEPTION, '__construct');
        }
    }

    protected function disconnect()
    {
        sqlite_close($this->link);
    }
}

?>