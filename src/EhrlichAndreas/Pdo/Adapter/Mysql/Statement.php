<?php 

//require_once 'EhrlichAndreas/Pdo/Abstract.php';

//require_once 'EhrlichAndreas/Pdo/Adapter/Abstract.php';

//require_once 'EhrlichAndreas/Pdo/Adapter/Abstract/Statement.php';

/**
 *
 * @author Ehrlich, Andreas <ehrlich.andreas@googlemail.com>
 */
class EhrlichAndreas_Pdo_Adapter_Mysql_Statement extends EhrlichAndreas_Pdo_Adapter_Abstract_Statement
{

    public function closeCursor ()
    {
        if ($this->_result)
        {
            mysql_free_result($this->_result);
            
            $this->_result = false;
        }
    }

    public function columnCount ()
    {
        if ($this->_result)
        {
            return mysql_num_fields($this->_result);
        }
        
        return 0;
    }

    public function rowCount ()
    {
        return mysql_affected_rows($this->_link);
    }

    public function getColumnMeta ($column)
    {
        if ($column >= $this->columnCount())
        {
            return false;
        }
        
        $info = mysql_fetch_field($this->_result, $column);
        
        $result = array();
        
        if ($info->def)
        {
            $result['mysql:def'] = $info->def;
        }
        
        $result['native_type'] = $info->type;
        
        $result['flags'] = explode(' ', mysql_field_flags($this->_result, $column));
        
        $result['table'] = $info->table;
        
        $result['name'] = $info->name;
        
        $result['len'] = mysql_field_len($this->_result, $column);
        
        $result['precision'] = 0;
        
        switch ($result['native_type'])
        {
            // seems like pdo_mysql treats everything as a string
            /*
             * case 'int': case 'real': $pdo_type =
             * EhrlichAndreas_Pdo_Abstract::PARAM_INT; break; case 'blob': $pdo_type =
             * EhrlichAndreas_Pdo_Abstract::PARAM_LOB; break; case 'null': $pdo_type =
             * EhrlichAndreas_Pdo_Abstract::PARAM_NULL; break;
             */
            default:
                
                $pdo_type = EhrlichAndreas_Pdo_Abstract::PARAM_STR;
                
                break;
        }
        
        $result['pdo_type'] = $pdo_type;
        
        return $result;
    }

    protected function _execute ()
    {
        $query = $this->_build_query();
        
        if (! $query)
        {
            return false;
        }
        
        if ($this->getAttribute(EhrlichAndreas_Pdo_Adapter_Abstract::MYSQL_ATTR_USE_BUFFERED_QUERY))
        {
            $this->_result = mysql_query($query, $this->_link);
        }
        else
        {
            $this->_result = mysql_unbuffered_query($query, $this->_link);
        }
        
        if (! $this->_result)
        {
            $this->_set_stmt_error(null, EhrlichAndreas_Pdo_Abstract::ERRMODE_SILENT, 'execute');
            
            return false;
        }
        
        return true;
    }

    protected function _fetch_row ()
    {
        return mysql_fetch_row($this->_result);
    }

    protected function _field_name ($field)
    {
        return mysql_field_name($this->_result, $field);
    }

    protected function _table_name ($field)
    {
        return mysql_field_table($this->_result, $field);
    }

    protected function _set_stmt_error ($state = null, $mode = EhrlichAndreas_Pdo_Abstract::ERRMODE_SILENT, $func = '')
    {
        $errno = mysql_errno($this->_link);
        
        if ($state === null)
        {
            $state = $this->_driver->get_sql_state($errno);
        }
        
        $this->_set_error($errno, mysql_error($this->_link), $state, $mode, $func);
    }
}

