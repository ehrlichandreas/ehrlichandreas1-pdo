<?php 

require_once 'EhrlichAndreas/Pdo/Abstract.php';

require_once 'EhrlichAndreas/Pdo/Adapter/Abstract.php';

require_once 'EhrlichAndreas/Pdo/Adapter/Abstract/Statement.php';

/**
 *
 * @author Ehrlich, Andreas <ehrlich.andreas@googlemail.com>
 */
class EhrlichAndreas_Pdo_Adapter_Sqlite_Statement extends EhrlichAndreas_Pdo_Adapter_Abstract_Statement
{
    public function closeCursor()
    {
        if($this->_result)
        {
            $this->_result = false;
        }
    }

    public function columnCount()
    {
        if($this->_result)
        {
            return sqlite_num_fields($this->_result);
        }

        return 0;
    }

    public function rowCount()
    {
        return sqlite_changes($this->_link);
    }

    public function getColumnMeta($column)
    {
        if($column >= $this->columnCount())
        {
            return false;
        }

        $result['name'] = $info->name;
        
        $result['pdo_type'] = EhrlichAndreas_Pdo_Abstract::PARAM_STR;

        return $result;
    }

    protected function _execute()
    {
        $query = $this->_build_query();
        
        if(!$query)
        {
            return false;
        }

        $this->_result = @sqlite_query($this->_link, $query, SQLITE_NUM, $errstr);

        if(!$this->_result)
        {
            $this->_set_error(0, $errstr, 'HY000', EhrlichAndreas_Pdo_Abstract::ERRMODE_SILENT, 'execute');
            
            return false;
        }

        return true;
    }

    protected function _fetch_row()
    {
        return sqlite_fetch_array($this->_result, SQLITE_NUM);
    }

    protected function _field_name($field)
    {
        return sqlite_field_name($this->_result, $field);
    }

    protected function _table_name($field)
    {
        return '';
    }

    protected function _set_stmt_error($state = null, $mode = EhrlichAndreas_Pdo_Abstract::ERRMODE_SILENT, $func = '')
    {
        $errno = sqlite_last_error($this->_link);
        
        if($state === null)
        {
            $state = 'HY000';
        }

        $this->_set_error($errno, sqlite_error_string($errno), $state, $mode, $func);
    }
}

?>