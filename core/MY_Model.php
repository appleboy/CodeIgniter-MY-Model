<?php

/**
 * A base model with a series of CRUD functions
 *
 * @author      appleboy
 * @copyright   2012 appleboy
 * @link        http://blog.wu-boy.com
 * @package     CodeIgniter
 * @subpackage  CI_Model
 */
class MY_Model extends CI_Model
{
    /**
     * Holds an array of tables used
     *
     * @var string
     */
    public $tables = array();

    /**
     * table primary key
     *
     * @var string
     */
    public $_key = 'id';

    /**
     * Unix Time
     *
     * @var int
     */
    public $_time = null;

    /**
     * Where
     *
     * @var array
     */
    public $_where = array();

    /**
     * like
     *
     * @var array
     */
    public $_like = array();

    /**
     * Select
     *
     * @var string
     */
    public $_select = array();

    /**
     * Limit
     *
     * @var string
     */
    public $_limit = null;

    /**
     * Offset
     *
     * @var string
     */
    public $_offset = null;

    /**
     * Order By
     *
     * @var string
     */
    public $_order_by = null;

    /**
     * Order By field
     *
     * @var string
     */
    public $_order_by_field = null;

    /**
     * Order
     *
     * @var string
     */
    public $_order = null;

    /**
     * Response
     *
     * @var string
     */
    protected $response = null;

    /**
     * __construct
     *
     * @return void
     * @author appleboy
     */
    public function __construct()
    {
        parent::__construct();

        // set default time
        $this->_time = time();
    }

    /**
     * set limit for $this->db->limit
     *
     * @var string
     */
    public function limit($limit)
    {
        $this->_limit = $limit;

        return $this;
    }

    /**
     * set offset for $this->db->limit
     *
     * @var string
     */
    public function offset($offset)
    {
        $this->_offset = $offset;

        return $this;
    }

    /**
     * set where for $this->db->where
     *
     * @var array or string
     * @var string
     */
    public function where($where, $value = null)
    {
        if (is_array($where)) {
            foreach ($where as $k => $v) {
                $this->_where[$k] = $v;
            }
        } else {
            $this->_where[$where] = $value;
        }

        return $this;
    }

    /**
     * set search function for $this->db->like
     *
     * @var array or string
     * @var string
     */
    public function like($like, $value = null)
    {
        if (!is_array($like))
        {
            $like = array($like => $value);
        }

        array_push($this->_like, $like);

        return $this;
    }

    /**
     * set select value for $this->db->select
     *
     * @var string
     */
    public function select($select)
    {
        $this->_select[] = $select;

        return $this;
    }

    /**
     * set order value for $this->db->order
     *
     * @var string
     * @var string
     */
    public function order_by($by, $order = 'desc')
    {
        $this->_order_by = $by;
        $this->_order    = $order;

        return $this;
    }

    /**
     * set order by field value
     *
     * @var string
     * @var string
     */
    public function order_by_field($by, $value = '')
    {
        $this->_order_by_field = "FIELD(".$by.", ".$value.")";

        return $this;
    }

    /**
     * get row object data
     *
     * @return object
     */
    public function row()
    {
        $row = $this->response->row();
        $this->response->free_result();

        return $row;
    }

    /**
     * get row array data
     *
     * @return array
     */
    public function row_array()
    {
        $row = $this->response->row_array();
        $this->response->free_result();

        return $row;
    }

    /**
     * get rows object data
     *
     * @return object
     */
    public function result()
    {
        $result = $this->response->result();
        $this->response->free_result();

        return $result;
    }

    /**
     * get rows array data
     *
     * @return array
     */
    public function result_array()
    {
        $result = $this->response->result_array();
        $this->response->free_result();

        return $result;
    }

    /**
     * get rows array data
     *
     * @return array
     */
    protected function handle_process()
    {
        //set select field
        if (isset($this->_select))
        {
            foreach ($this->_select as $select)
            {
                $this->db->select($select, false);
            }

            $this->_select = array();
        }

        //run each where that was passed
        if (isset($this->_where))
        {
            foreach ($this->_where as $k => $v) {
                if (is_array($v)) {
                    $this->db->where_in($k, $v);
                } else {
                    if ($v == null) {
                        $this->db->where($k, $v, false);
                    } else {
                        $this->db->where($k, $v);
                    }
                }
            }

            $this->_where = array();
        }

        //run each like that was passed
        if (isset($this->_like))
        {
            foreach ($this->_like as $like) {
                $this->db->like($like);
            }

            $this->_like = array();
        }

        //set limit and offset
        if (isset($this->_limit) && isset($this->_offset))
        {
            $this->db->limit($this->_limit, $this->_offset);

            $this->_limit  = null;
            $this->_offset = null;
        }

        //set the order
        if (isset($this->_order_by) && isset($this->_order))
        {
            $this->db->order_by($this->_order_by, $this->_order);

            $this->_order    = null;
            $this->_order_by = null;
        }

        //set the order field
        if (isset($this->_order_by_field))
        {
            $this->db->order_by($this->_order_by_field);

            $this->_order_by_field = null;
        }
    }

    /**
     * items
     *
     * @return object
     * @author appleboy
     */
    public function items()
    {
        $this->handle_process();

        $this->response = $this->db->get($this->tables['master']);

        return $this;
    }

    /**
     * item
     *
     * @return object
     * @author appleboy
     */
    public function item($id = null)
    {
        $this->limit(1);
        $this->where($this->tables['master'] . $this->_key, $id);

        $this->items();

        return $this;
    }

    /**
     * Insert Data API
     *
     * @param array $data
     *
     */
    function add($data = null)
    {
        $external_data = array(
            'add_time' => $this->_time,
            'edit_time' => $this->_time
        );

        // merge array data
        $data = array_merge($data, $external_data);
        // insert to database
        $this->db->insert($this->tables['master'], $data);

        return $this->db->insert_id();
    }

    /**
     * Update Data API
     *
     * @param array $data
     *
     */
    function edit($data = null)
    {
        // @TODO
    }

    /**
     * Delete Data API
     *
     * @param (int or array) $id
     *
     */
    public function delete($id)
    {
        $data = array(
            "delete" => 1,
            "edit_time" => $this->_time,
        );

        if(is_array($id)) {
            $this->db->where_in($this->_key, $id)->update($this->tables['master'], $data);
        } else {
            $this->db->where($this->_key, $id)->update($this->tables['master'], $data);
        }

        return true;
    }
}
