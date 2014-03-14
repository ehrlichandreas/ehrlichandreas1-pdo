<?php

//require_once 'EhrlichAndreas/Pdo/Exception.php';

//require_once 'EhrlichAndreas/Pdo/Abstract.php';

//require_once 'EhrlichAndreas/Pdo/Adapter/Abstract.php';

//require_once 'EhrlichAndreas/Pdo/Adapter/Mysqli/Statement.php';

/**
 *
 * @author Ehrlich, Andreas <ehrlich.andreas@googlemail.com>
 */
class EhrlichAndreas_Pdo_Adapter_Mysqli extends EhrlichAndreas_Pdo_Adapter_Abstract
{

    public function __construct ($dsn, &$username, &$password, &$driver_options)
    {
        if (! extension_loaded('mysqli'))
        {
            throw new EhrlichAndreas_Pdo_Exception('could not find extension');
        }
        
        // set default values
        $this->driver_options[EhrlichAndreas_Pdo_Adapter_Abstract::MYSQL_ATTR_USE_BUFFERED_QUERY] = 1;
        
        $this->driver_options[EhrlichAndreas_Pdo_Adapter_Abstract::MYSQL_ATTR_LOCAL_INFILE] = false;
        
        $this->driver_options[EhrlichAndreas_Pdo_Adapter_Abstract::MYSQL_ATTR_INIT_COMMAND] = '';
        
        $this->driver_options[EhrlichAndreas_Pdo_Adapter_Abstract::MYSQL_ATTR_READ_DEFAULT_FILE] = false;
        
        $this->driver_options[EhrlichAndreas_Pdo_Adapter_Abstract::MYSQL_ATTR_READ_DEFAULT_GROUP] = false;
        
        $this->driver_options[EhrlichAndreas_Pdo_Adapter_Abstract::MYSQL_ATTR_MAX_BUFFER_SIZE] = 1048576;
        
        $this->driver_options[EhrlichAndreas_Pdo_Adapter_Abstract::MYSQL_ATTR_DIRECT_QUERY] = 1;
        
        parent::__construct($dsn, $username, $password, $driver_options);
        
        $this->driver_param_type = 0;
    }

    public function commit ()
    {
        parent::commit();
        
        if (! mysqli_commit($this->link))
        {
            $this->set_driver_error(null, EhrlichAndreas_Pdo_Abstract::ERRMODE_EXCEPTION, 'commit');
        }
        
        $this->setAttribute(EhrlichAndreas_Pdo_Abstract::ATTR_AUTOCOMMIT, 1);
        
        return true;
    }

    public function exec (&$statement)
    {
        $result = mysqli_query($this->link, $statement, MYSQLI_USE_RESULT);
        
        if ($result)
        {
            if (is_object($result))
            {
                mysqli_free_result($result);
                
                return 0;
            }
            
            return mysqli_affected_rows($this->link);
        }
        
        return false;
    }

    public function getAttribute ($attribute, &$source = null, $func = 'PDO::getAttribute', &$last_error = null)
    {
        if ($source == null)
        {
            $source = & $this->driver_options;
        }
        
        switch ($attribute)
        {
            case EhrlichAndreas_Pdo_Abstract::ATTR_AUTOCOMMIT:
                
                $result = mysqli_query($this->link, 'SELECT @@AUTOCOMMIT', MYSQLI_USE_RESULT);
                
                if (! $result)
                {
                    $this->set_driver_error(null, EhrlichAndreas_Pdo_Abstract::ERRMODE_EXCEPTION, $func);
                }
                
                $row = mysqli_fetch_row($result);
                
                mysqli_free_result($result);
                
                return intval($row[0]);
                
                break;
            
            case EhrlichAndreas_Pdo_Abstract::ATTR_CLIENT_VERSION:
                
                return mysqli_get_client_info();
                
                break;
            
            case EhrlichAndreas_Pdo_Abstract::ATTR_CONNECTION_STATUS:
                
                return mysqli_get_host_info($this->link);
                
                break;
            
            case EhrlichAndreas_Pdo_Abstract::ATTR_SERVER_INFO:
                
                return mysqli_stat($this->link);
                
                break;
            
            case EhrlichAndreas_Pdo_Abstract::ATTR_SERVER_VERSION:
                
                return mysqli_get_server_info($this->link);
                
                break;
            
            default:
                
                return parent::getAttribute($attribute, $source, $func, $last_error);
                
                break;
        }
    }

    public function lastInsertId ($name = '')
    {
        return mysqli_insert_id($this->link);
    }

    public function prepare (&$statement, &$options)
    {
        $st = parent::prepare($statement, $options);
        
        if (! $st)
        {
            return false;
        }
        
        $result = mysqli_prepare($this->link, $this->prepared);
        
        if (! $result)
        {
            $this->set_driver_error(null, EhrlichAndreas_Pdo_Abstract::ERRMODE_SILENT, 'prepare');
            return false;
        }
        
        $st->_set_result($result);
        
        return $st;
    }

    public function quote (&$param, $parameter_type = -1)
    {
        switch ($parameter_type)
        {
            case EhrlichAndreas_Pdo_Abstract::PARAM_BOOL:
                
                return $param ? 1 : 0;
                
                break;
            
            case EhrlichAndreas_Pdo_Abstract::PARAM_NULL:
                
                return 'NULL';
                
                break;
            
            case EhrlichAndreas_Pdo_Abstract::PARAM_INT:
                
                return is_null($param) ? 'NULL' : (is_int($param) ? $param : (float) $param);
                
                break;
            
            default:
                
                return '\'' . mysqli_real_escape_string($this->link, $param) . '\'';
                
                break;
        }
    }

    public function rollBack ()
    {
        parent::rollback();
        
        if (! mysqli_rollback($this->link))
        {
            $this->set_driver_error(null, EhrlichAndreas_Pdo_Abstract::ERRMODE_EXCEPTION, 'rollBack');
        }
        
        $this->setAttribute(EhrlichAndreas_Pdo_Abstract::ATTR_AUTOCOMMIT, $this->driver_options[EhrlichAndreas_Pdo_Abstract::ATTR_AUTOCOMMIT]);
        
        return true;
    }

    public function setAttribute ($attribute, $value, &$source = null, $func = 'PDO::setAttribute', &$last_error = null)
    {
        if ($source == null)
        {
            $source = & $this->driver_options;
        }
        
        switch ($attribute)
        {
            case EhrlichAndreas_Pdo_Abstract::ATTR_AUTOCOMMIT:
                
                $value = $value ? 1 : 0;
                
                if (! mysqli_autocommit($this->link, $value))
                {
                    $this->set_driver_error(null, EhrlichAndreas_Pdo_Abstract::ERRMODE_EXCEPTION, $func);
                }
                
                return true;
                
                break;
            
            case EhrlichAndreas_Pdo_Abstract::ATTR_TIMEOUT:
                
                $value = intval($value);
                
                if ($value > 1 && mysqli_options($this->link, MYSQLI_OPT_CONNECT_TIMEOUT, $value))
                {
                    $source[EhrlichAndreas_Pdo_Abstract::ATTR_TIMEOUT] = $value;
                    
                    return true;
                }
                
                break;
            
            case EhrlichAndreas_Pdo_Adapter_Abstract::MYSQL_ATTR_LOCAL_INFILE:
                
                $value = $value ? true : false;
                
                if (mysqli_options($this->link, MYSQLI_OPT_LOCAL_INFILE, $value))
                {
                    $source[EhrlichAndreas_Pdo_Adapter_Abstract::MYSQL_ATTR_LOCAL_INFILE] = $value;
                    
                    return true;
                }
                
                break;
            
            case EhrlichAndreas_Pdo_Adapter_Abstract::MYSQL_ATTR_INIT_COMMAND:
                
                if ($value && mysqli_options($this->link, MYSQLI_INIT_COMMAND, $value))
                {
                    $source[EhrlichAndreas_Pdo_Adapter_Abstract::MYSQL_ATTR_INIT_COMMAND] = $value;
                    
                    return true;
                }
                
                break;
            
            case EhrlichAndreas_Pdo_Adapter_Abstract::MYSQL_ATTR_READ_DEFAULT_FILE:
                
                $value = $value ? true : false;
                
                if (mysqli_options($this->link, MYSQLI_READ_DEFAULT_FILE, $value))
                {
                    $source[EhrlichAndreas_Pdo_Adapter_Abstract::MYSQL_ATTR_READ_DEFAULT_FILE] = $value;
                    
                    return true;
                }
                
                break;
            
            case EhrlichAndreas_Pdo_Adapter_Abstract::MYSQL_ATTR_READ_DEFAULT_GROUP:
                
                $value = $value ? true : false;
                
                if (mysqli_options($this->link, MYSQLI_READ_DEFAULT_GROUP, $value))
                {
                    $source[EhrlichAndreas_Pdo_Adapter_Abstract::MYSQL_ATTR_READ_DEFAULT_GROUP] = $value;
                    
                    return true;
                }
                break;
            
            /*
             * case EhrlichAndreas_Pdo_Adapter_Abstract::MYSQL_ATTR_MAX_BUFFER_SIZE:
             * break; case
             * EhrlichAndreas_Pdo_Adapter_Abstract::MYSQL_ATTR_DIRECT_QUERY: break;
             */
            default:
                
                return parent::setAttribute($attribute, $value, $source, $func, $last_error);
                
                break;
        }
        
        return false;
    }

    public function set_driver_error ($state = null, $mode = EhrlichAndreas_Pdo_Abstract::ERRMODE_SILENT, $func = '')
    {
        if ($state === null)
        {
            $state = mysqli_sqlstate($this->link);
        }
        
        $this->set_error(mysqli_errno($this->link), mysqli_error($this->link), $state, $mode, $func);
    }

    protected function connect (&$username, &$password, &$driver_options)
    {
        $this->link = mysqli_init();
        
        $attributes = array
        (
            EhrlichAndreas_Pdo_Abstract::ATTR_TIMEOUT,
            EhrlichAndreas_Pdo_Adapter_Abstract::MYSQL_ATTR_LOCAL_INFILE,
            EhrlichAndreas_Pdo_Adapter_Abstract::MYSQL_ATTR_INIT_COMMAND,
            EhrlichAndreas_Pdo_Adapter_Abstract::MYSQL_ATTR_READ_DEFAULT_FILE,
            EhrlichAndreas_Pdo_Adapter_Abstract::MYSQL_ATTR_READ_DEFAULT_GROUP,
        );
        
        $this->set_attributes($attributes, $driver_options);
        
        $host = isset($this->dsn['host']) ? $this->dsn['host'] : 'localhost';
        
        $dbname = isset($this->dsn['dbname']) ? $this->dsn['dbname'] : '';
        
        $port = isset($this->dsn['port']) ? intval($this->dsn['port']) : 0;
        
        $socket = isset($this->dsn['unix_socket']) ? $this->dsn['unix_socket'] : '';
        
        if (strtolower($host) == 'localhost' && empty($socket))
        {
            $host = '127.0.0.1';
        }
        
        if (! @mysqli_real_connect($this->link, $host, $username, $password, $dbname, $port, $socket))
        {
            $this->set_error(mysqli_connect_errno(), mysqli_connect_error(), 'HY000', EhrlichAndreas_Pdo_Abstract::ERRMODE_EXCEPTION, '__construct');
        }
        
        if (isset($this->dsn['charset']))
        {
            if (! mysqli_set_charset($this->link, $this->dsn['charset']))
            {
                $this->set_driver_error(null, EhrlichAndreas_Pdo_Abstract::ERRMODE_EXCEPTION, '__construct');
            }
        }
    }

    protected function disconnect ()
    {
        mysqli_close($this->link);
    }
}

