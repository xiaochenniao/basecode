<?php

/**
  上机题目：
  设计封装一个通用无限级分类的class
  环境说明：
  1、无限级分类，一般不会超过10层结构
  2、分类数量在万级左右
  3、分类基本属性包括：主键id、名称name、排序order_id

  具体要求：
  1、设计数据库表结构，利用mysql进行存储
  2、利用缓存机制(文件缓存、内存缓存均可)，尽量避免数据库查询次数
  3、尽量考虑到通用性，比如分类属性可扩展
  4、实现基本的分类查询方法，如果有补充更好，
  例如：
  添加单个节点			addNode
  输出到外部缓存			getNodes（选做）
  输入自外部缓存			setNodes（选做）
  获取指定节点属性		getNode
  获取全部子节点属性		getChildren
  获取上级父节点属性		getParent（选做）
  获取全部上级节点属性	getParents
  ……
  5、数据库相关操作可以省略，返回结果利用自定义数组代替，主要实现从缓存读取后的基本操作方法




  设计mysql存储表结构
  CREATE TABLE `tree` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `parent_id` smallint(6) unsigned NOT NULL,
  `order_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `order_id` (`order_id`)
  ) ENGINE=MyISAM ;

  考点：
  1、php开发中，数组是最常用到的，对数组的基本操作和逻辑处理是基础。考察用到对数组操作的php内置函数有多少个
  2、在大数据量的循环判断中，应用键值搜索，要比直接进行数据搜索要快的很多。所以，可以有针对性的把要搜索的数据先归为一个一维数组，以键值为被搜索值，将会大大的提高效率
  3、设计类的通用性、以及程序结构的严整性

  缓存可以是file ea memcache等等


 */
class tree {

    protected $_nodes = array();   //存储分类数据的数组

    public function addNode($node) {
        $id = $node['id'];
        $parent_id = $node['parent_id'];
        $order_id = $node['order_id'];
        if (isset($this->_nodes[$id])) {
            $this->_nodes[$id] = $node + $this->_nodes[$id];
        } else {
            $this->_nodes[$id] = $node;
        }
        $this->_nodes[$parent_id]['_childIds'][$id] = $order_id; //排序
        //asort($this->_nodes[$parent_id]['_childIds']);				//根据value正向排序，即order_id从小到大
    }

    //获取节点信息，如果该节点不存在，返回false
    public function getNode($id) {
        return isset($this->_nodes[$id]) ? $this->_nodes[$id] : false;
    }

    //获取全部节点信息，用于输出外部缓存
    public function getNodes() {
        return $this->_nodes;
    }

    //设置全部节点信息，用于输入外部缓存
    public function setNodes($nodes) {
        $this->_nodes = $nodes;
    }

    //获取子节点，如果没有子节点，则返回一个空的数组
    //type 1 一维数组形式， 2 多维数组形式
    //depth 返回最大父子层次，最多默认99层
    public function getChildren($id = 0, $type = 1, $depth = 99, $_depth = -1) {
        $result = array();
        if ($depth < 1) {
            return $result;
        }
        $depth--;
        $_depth++;
        $node = $this->getNode($id);
        if (isset($node['_childIds']) && is_array($node['_childIds'])) {
            foreach ($node['_childIds'] as $key => $value) {
                $result[$key] = $this->getNode($key);
                $result[$key]['_depth'] = $_depth;
                if ($type == 1) {
                    $result = $result + $this->getChildren($key, $type, $depth, $_depth);
                } elseif ($type == 2) {
                    $result[$key]['_children'] = $this->getChildren($key, $type, $depth, $_depth);
                }
            }
        }
        return $result;
    }

    //获取子节点，如果没有子节点，则返回一个空的数组
    //depth 返回最大父子层次，最多默认99层
    public function getChildIds($id = 0, $depth = 99, $_depth = -1) {
        $result = array();
        if ($depth < 1) {
            return $result;
        }
        $depth--;
        $_depth++;
        $node = $this->getNode($id);
        if (is_array($node['_childIds'])) {
            foreach ($node['_childIds'] as $key => $value) {
                $result[$key] = $key;
                $result = $result + $this->getChildIds($key, $depth, $_depth);
            }
        }
        return $result;
    }

    //获取父节点的信息，如果该节点不存在或为根节点，返回false
    public function getParent($id) {
        $result = array();
        if ($id == 0 || ($self = $this->getNode($id)) === false)
            return $result;
        if (!$self['parent_id'])
            return $result;
        else
            return $this->getNode($self['parent_id']);
    }

    //获所有父节点
    //type 1 一维数组形式， 2 多维数组形式
    ///depth 返回最大父子层次，最多默认99层
    public function getParents($id, $type = 1, $depth = 99, $_depth = -1) {
        $result = array();
        if ($depth < 1) {
            return $result;
        }
        $depth--;
        $_depth++;
        if ($parent = $this->getParent($id)) {
            $result[$parent['id']] = $parent;
            $result[$parent['id']]['_depth'] = $_depth;
            if ($type == 1) {
                $result = $result + $this->getParents($parent['id'], $type, $depth, $_depth);
            } elseif ($type == 2) {
                $result[$parent['id']]['_parents'] = $this->getParents($parent['id'], $type, $depth, $_depth);
            }
        }
        return $result;
    }

}

/*
  $tree = new tree_Lib();
  //$cache = new cache_file_Lib();
  //$key = 'test_nodes';
  //if(!$nodes = $cache->get($key)){
  //数据库读取
  $nodes = array(
  array('id' => 1, 'name' => '1', 'parent_id' => 0, 'order_id' => 0),
  array('id' => 2, 'name' => '2', 'parent_id' => 1, 'order_id' => 0),
  array('id' => 3, 'name' => '3', 'parent_id' => 2, 'order_id' => 0),
  array('id' => 4, 'name' => '4', 'parent_id' => 1, 'order_id' => 0),
  array('id' => 5, 'name' => '5', 'parent_id' => 4, 'order_id' => 0),
  array('id' => 6, 'name' => '6', 'parent_id' => 5, 'order_id' => 0),
  array('id' => 7, 'name' => '7', 'parent_id' => 4, 'order_id' => 0),
  );
  foreach($nodes as $node){
  $tree->addNode($node);
  }
  $nodes = $tree->getNodes();
  //$cache->set($key, $nodes);
  //}
  $tree->setNodes($nodes);

  //print_r($tree->getChildren(0,1));

  //print_r($tree->getChildren(1,2));

  //print_r($tree->getParents(7,2));
 */
?>