<?php
namespace core;


class Page
{
	public     $first_row; 		  //起始行数
	public     $list_rows = 20;   //列表每页显示行数
	protected  $total_pages;	  //总页数
	public     $total_rows;		  //总行数
	protected  $now_page;         //当前页数
	protected  $page_name;        //分页参数的名称
	protected  $db;				  //数据库resource
	protected  $table;			  //表名
	protected  $where;			  //查表条件
	public 	   $plus = 3;         //分页偏移量
	public     $url;
	
	
	/**
	 * 构造函数
	 * @param \core\Mysql $db
     * @param string $table
     * @param string $url
	 */
	public function __construct($db, $table, $url, $where = null, $data = null)
	{
		$data && $this->_set($data);
		
		$this->db    			= $db;
		$this->table 			= $table;
		$this->where 			= $where;
		$this->url  			= $url;
		
		$this->total_rows		= $this->_getTotalRows('count(*)');
		
		$this->_setListRows();
		
		$this->total_pages		= ceil($this->total_rows / $this->list_rows);	//ceil函数向上取整
		$this->page_name  		= $this->page_name ? $this->page_name : 'page';
		
		
		/* 当前页面 */
		$this->now_page   = !empty($_GET[$this->page_name]) ? intval($_GET[$this->page_name]) : 1;
		$this->now_page   = $this->now_page <= 0 ? 1 : $this->now_page;
	
		
		if(!empty($this->total_pages) && $this->now_page > $this->total_pages)
		{
			$this->now_page = $this->total_pages;
		}
		$this->first_row = $this->list_rows * ($this->now_page - 1);
	}
	
	/**
     * 设定总接口
     * @param string|array $key
     * @param string $value
     */
    protected function _set($key, $value=null)
    {
        if (is_array($key))
        {
            foreach ($key as $k => $v)
            {
            	$v && $this->$k = $v;
            }
        }
        else
       	{
            $value && $this->$key = $value;
        }
    }
	
	protected  function _getTotalRows($count = 'count(*)')
	{
		$rs = $this->db->row("SELECT $count AS cc FROM {$this->table} {$this->where['where']}", $this->where['bind']);
		return $rs['cc'];
	}
	
	protected function _setListRows()
	{
		$this->list_rows    = $_GET['hans'] ? ($_GET['hans'] > 100 ? $this->list_rows : $_GET['hans']) : $this->list_rows;
	}
	
	/**
	 * 得到当前连接
	 * @param $page
	 * @param $text
	 * @return string
	 */
	protected function _get_link($page,$text)
	{
		return '<a href="' . $this->_get_url($page) . '">' . $text . '</a>' . "\n";
	}
	
	
    /**
     * 得到$page的url
     * @param $page 页面
     * @return string
     */
    protected function _get_url($page)
    {
    	return $this->url . $this->page_name . '=' . $page;
    }
    
    
    /**
     * 得到第一页
     * @return string
     */
    public function first_page($name = '第一页')
    {
 		if($this->now_page > 5)
 		{
 			return $this->_get_link('1', $name);
 		}	
 		return '';
    }
    
    /**
     * 最后一页
     * @param $name
     * @return string
     */
    public function last_page($name = '最后一页')
    {
 		if($this->now_page < $this->total_pages - 5)
 		{
 			return $this->_get_link($this->total_pages, $name);
 		}	
 		return '';
    }  
	
    /**
     * 上一页
     * @return string
     */
    public function up_page($name = '上一页')
    {
    	if($this->now_page != 1)
    	{
    		return $this->_get_link($this->now_page - 1, $name);
    	}
    	return '';
    }
	
    /**
     * 下一页
     * @return string
     */
    public function down_page($name = '下一页')
    {
    	if($this->now_page < $this->total_pages)
    	{
    		return $this->_get_link($this->now_page + 1, $name);
    	}
    	return '';
    }

    /**
     * 分页样式输出
     * @param $param
     * @return string
     */
    public function show($param = 1)
    {
    	if ($this->total_rows < 1)
    	{
    		return '';
    	}
    	
    	$className = 'show_' . $param;
    	
    	$classNames = get_class_methods($this);

    	if (in_array($className, $classNames))
    	{
    		return $this->$className();
    	}
  		return '';
    }
    
    
    protected function show_1()
    {
    	$plus = $this->plus;
    	if( $plus + $this->now_page > $this->total_pages)
    	{
    		$begin = $this->total_pages - $plus * 2;
    	}else
    	{
    		$begin = $this->now_page - $plus;
    	}
    	 
    	$begin = ($begin >= 1) ? $begin : 1;
    	$return = '';
    	$return .= $this->first_page();
    	$return .= $this->up_page();
    	for ($i = $begin; $i <= $begin + $plus * 2; $i++)
    	{
    		if($i > $this->total_pages)
    		{
    			break;
    		}
    		if($i == $this->now_page)
    		{
    			$return .= "<a class='now_page'>$i</a>\n";
    		} else
    		{
    			$return .= $this->_get_link($i, $i) . "\n";
    		}
    	}
    	
    	$return .= $this->down_page();
    	$return .= $this->last_page();
    	return $return;
    }
    
    
    protected function show_2()
    {
        if($this->total_pages != 1)
    	{
    		$return = '';
    		$return .= $this->up_page('<');
			for($i = 1;$i<=$this->total_pages;$i++)
			{
				if($i == $this->now_page)
				{
					$return .= "<a class='now_page'>$i</a>\n";
				}
				else
				{
					if($this->now_page-$i>=4 && $i != 1)
					{
						$return .="<span class='pageMore'>...</span>\n";
						$i = $this->now_page-3;
					}
					else
					{
						if($i >= $this->now_page+5 && $i != $this->total_pages)
						{
							$return .="<span>...</span>\n"; 
							$i = $this->total_pages;
						}
						$return .= $this->_get_link($i, $i) . "\n";
					}
				}
			}
			$return .= $this->down_page('>');
    		return $return;
    	}
    }
}
