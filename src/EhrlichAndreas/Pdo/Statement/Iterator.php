<?php 

/**
 *
 * @author Ehrlich, Andreas <ehrlich.andreas@googlemail.com>
 */
class EhrlichAndreas_Pdo_Statement_Iterator implements Iterator
{

    /**
     *
     * @var EhrlichAndreas_Pdo_Statement
     */
    private $_stmt = null;

    /**
     * The original data for each row.
     * 
     * @var array
     */
    protected $_data = array();

    /**
     * Iterator pointer.
     * 
     * @var integer
     */
    protected $_pointer = - 1;

    /**
     *
     * @param EhrlichAndreas_Pdo_Statement $stmt            
     */
    public function __construct ($stmt)
    {
        $this->_stmt = $stmt;
    }

    protected function _init ()
    {
        if ($this->_pointer == - 1)
        {
            $this->next();
        }
    }

    /**
     * (non-PHPdoc)
     * 
     * @see Iterator::key()
     * @return scalar
     */
    public function key ()
    {
        $this->_init();
        
        return $this->_pointer;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see Iterator::rewind() return void
     */
    public function rewind ()
    {
        $this->_init();
        
        $this->_pointer = 0;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see Iterator::current()
     * @return mixed
     */
    public function current ()
    {
        $this->_init();
        
        if (isset($this->_data[$this->_pointer]) && $this->_data[$this->_pointer] !== false)
        {
            return $this->_data[$this->_pointer];
        }
        
        return null;
    }

    /**
     * (non-PHPdoc)
     * 
     * @see Iterator::next() return void
     */
    public function next ()
    {
        $this->_pointer ++;
        
        $this->_data[$this->_pointer] = $this->_stmt->fetch();
    }

    /**
     * (non-PHPdoc)
     * 
     * @see Iterator::valid()
     */
    public function valid ()
    {
        $this->_init();
        
        if (!isset($this->_data[$this->_pointer]) || $this->_data[$this->_pointer] === false)
        {
            return false;
        }
        
        return true;
    }
}

