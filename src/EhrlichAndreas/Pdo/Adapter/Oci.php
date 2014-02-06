<?php

require_once 'EhrlichAndreas/Pdo/Exception.php';

require_once 'EhrlichAndreas/Pdo/Abstract.php';

require_once 'EhrlichAndreas/Pdo/Adapter/Abstract.php';

require_once 'EhrlichAndreas/Pdo/Adapter/Oci/Statement.php';

require_once 'EhrlichAndreas/Pdo/Adapter/Oci/defaults.php';

/**
 *
 * @author Ehrlich, Andreas <ehrlich.andreas@googlemail.com>
 */
class EhrlichAndreas_Pdo_Adapter_Oci extends EhrlichAndreas_Pdo_Adapter_Abstract
{
    public $autocommit;
    
    private $temp_result = null;

    public function __construct ($dsn, &$username, &$password, &$driver_options)
    {
        if (! extension_loaded('oci8'))
        {
            throw new EhrlichAndreas_Pdo_Exception('could not find extension');
        }

        $this->driver_param_type = 1;
        
        $this->driver_quote_type = 1;

        if(!isset($driver_options[PDO::ATTR_PREFETCH]))
        {
            $driver_options[PDO::ATTR_PREFETCH] = @ini_get('oci8.default_prefetch');
        }

        parent::__construct($dsn, $username, $password, $driver_options);
    }

    public function commit ()
    {
        parent::commit();

        if(!oci_commit($this->link))
        {
            $this->set_driver_error(null, EhrlichAndreas_Pdo_Abstract::ERRMODE_EXCEPTION, 'commit');
        }

        $this->setAttribute(EhrlichAndreas_Pdo_Abstract::ATTR_AUTOCOMMIT, 1);
        
        return true;
    }

    public function exec (&$statement)
    {
        
        $result = $this->temp_result;
        
        if(($result = oci_parse($this->link, $statement)) && @oci_execute($result, ($this->autocommit ? OCI_COMMIT_ON_SUCCESS : OCI_DEFAULT)))
        {
            if('SELECT' == oci_statement_type($result))
            {
                oci_free_statement($result);
                
                $result = null;

                return 0;
            }

            $rows = oci_num_rows($result);
            
            $result = null;
            
            $this->temp_result = null;

            return $rows;
        }

        return false;
    }

    public function getAttribute ($attribute, &$source = null, $func = 'PDO::getAttribute', &$last_error = null)
    {
        switch($attribute)
        {
            case EhrlichAndreas_Pdo_Abstract::ATTR_AUTOCOMMIT:
                
                return $this->autocommit;
                
                break;

            case EhrlichAndreas_Pdo_Abstract::ATTR_PREFETCH:

                break;

            case EhrlichAndreas_Pdo_Abstract::ATTR_CLIENT_VERSION:
                
                return oci_server_version($this->link);
                
                break;

            case EhrlichAndreas_Pdo_Abstract::ATTR_SERVER_VERSION:
                
                $ver = oci_server_version($this->link);
                
                if(preg_match('/([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/', $ver, $match))
                {
                    return $match[1];
                }

                return $ver;
                
                break;

            case EhrlichAndreas_Pdo_Abstract::ATTR_SERVER_INFO:
                
                return oci_server_version($this->link);
                
                break;

            default:
                
                return parent::getAttribute($attribute, $source, $func, $last_error);
                
                break;
        }
    }

    public function lastInsertId ($name = '')
    {
        if(!$name)
        {
            return false;
        }

        if(($result = oci_parse($this->link, 'SELECT '.$name.'.CURRVAL FROM dual')) && @oci_execute($result, ($this->autocommit ? OCI_COMMIT_ON_SUCCESS : OCI_DEFAULT)))
        {
            $row = oci_fetch_row($result);
            
            return intval($row[0]);
        }

        return false;
    }

    public function prepare (&$statement, &$options)
    {
        if (! ($st = parent::prepare($statement, $options)))
        {
            return false;
        }
        
        $result = oci_parse($this->link, $this->prepared);
        
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
        switch($parameter_type)
        {
            case EhrlichAndreas_Pdo_Abstract::PARAM_BOOL:
                
                return $param ? '1' : '0';
                
                break;

            case EhrlichAndreas_Pdo_Abstract::PARAM_NULL:
                
                return 'NULL';
                
                break;

            case EhrlichAndreas_Pdo_Abstract::PARAM_INT:
                
                return is_null($param) ? 'NULL' : (is_int($param) ? $param : (float)$param);
                
                break;

            default:
                
                return '\'' . str_replace('\'', '\'\'', $param) . '\'';
                
                break;
        }
    }

    public function rollBack ()
    {
        parent::rollback();
        
        if (! oci_rollback($this->link))
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

        switch($attribute)
        {
            case EhrlichAndreas_Pdo_Abstract::ATTR_AUTOCOMMIT:
                
                if(!$value && $this->in_transaction)
                {
                    $this->commit();
                }

                $this->autocommit = $value ? true : false;
                
                return true;
                
                break;


            default:
                
                return parent::setAttribute($attribute, $value, $source, $func, $last_error);
                
                break;
        }

        return false;
    }

    public function set_driver_error ($state = null, $mode = EhrlichAndreas_Pdo_Abstract::ERRMODE_SILENT, $func = '')
    {
        if($this->temp_result)
        {
            $error = oci_error($this->temp_result);
            
            $this->temp_result = null;
        }
        else
        {
            $error = $this->link ? oci_error($this->link) : oci_error();
        }

        if($state === null)
        {
            $state = 'HY000';
        }
        
        $this->set_error($error['code'], $error['message'], $state, $mode, $func);
    }

    protected function connect (&$username, &$password, &$driver_options)
    {
        $dbname     = isset($this->dsn['dbname'])   ? $this->dsn['dbname']  : '';
        
        $charset    = isset($this->dsn['charset'])  ? $this->dsn['charset'] : (isset($_ENV['NLS_LANG']) ? $_ENV['NLS_LANG'] : 'WE8ISO8859P1');

        ob_start();

        if(isset($driver_options[EhrlichAndreas_Pdo_Abstract::ATTR_PERSISTENT]) && $driver_options[EhrlichAndreas_Pdo_Abstract::ATTR_PERSISTENT])
        {
            $this->link = oci_pconnect($username, $password, $dbname, $charset);
        }
        else
        {
            $this->link = oci_new_connect($username, $password, $dbname, $charset);
        }

        $error = ob_get_contents();
        
        ob_end_clean();

        if(!$this->link)
        {
            $this->set_driver_error(null, EhrlichAndreas_Pdo_Abstract::ERRMODE_EXCEPTION, '__construct');
        }
        else if($error)
        {
            $this->set_error(0, $this->clear_warning($error), 'HY000', EhrlichAndreas_Pdo_Abstract::ERRMODE_EXCEPTION, '__construct');
        }
    }

    protected function disconnect ()
    {
        oci_close($this->link);
    }

    private function clear_warning($msg)
    {
        $pos = strpos($msg, '): ');
        
        $pos2 = strrpos($msg, ' in ');
        
        if($pos !== false && $pos2 !== false)
        {
            $pos += 3;
            
            return substr($msg, $pos, ($pos2 - $pos));
        }

        return $msg;
    }
}

?>