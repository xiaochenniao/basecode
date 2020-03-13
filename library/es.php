<?php

use Elasticsearch\ClientBuilder;

/**
 * Description of es
 * @author xiaochenniao <374270458@qq.com>
 * Created on : 2020-3-13
 */
abstract class es {

    /**
     * @var ClientBuilder
     */
    private $client;
    private static $instance;

    /**
     * 索引名称相当于数据库
     * @var string
     */
    protected $index = "cyzone";

    /**
     * 索引类型，相当于表
     * @var string
     */
    protected $type = "";

    /**
     * 创建索引的时候的mapping信息
     * @var array
     */
    protected $mappings = [
    ];

    /**
     * 默认的mappings信息
     * @var array
     */
    private $defaultMappings = [
        '_default_' => [//默认配置，每个类型缺省的配置使用默认配置
            '_all' => [//  关闭所有字段的检索
                'enabled' => 'false'
            ],
            '_source' => [//  存储原始文档
                'enabled' => 'true'
            ],
        ]
    ];

    /**
     * 创建索引的时候的配置信息
     * @var array
     */
    private $setting = [
        "index" => [
            "number_of_shards" => 3,
            "number_of_replicas" => 2
        ]
    ];

    private function __construct() {
        $this->client = ClientBuilder::create()
                ->setHosts(['127.0.0.1:9200'])
                ->build();
    }

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 获取默认的搜索字段，就是mapping里面的配置
     * @param array $field
     * @param bool $exceptId
     * @return array
     */
    protected function getSearchFiled($field = [], $exceptId = true) {
        if ($field) {
            return $field;
        }
        $properties = $this->mappings[$this->type]['properties'] ?? [];
        if (empty($properties)) {
            return [];
        }
        $fields = array_keys($properties);
        foreach ($fields as $key => $value) {
            if ($exceptId && strpos($value, "id") !== false) {
                unset($fields[$key]);
            }
        }
        return $fields;
    }

    /**
     * 查看Mapping
     */
    public function getMappings() {
        $params = [
            'index' => $this->index
        ];
        $res = $this->client->indices()->getMapping($params);
        return $res;
    }

    /**
     * 修改Mapping
     * @return array
     */
    public function putMappings() {
        $mappings = array_merge($this->defaultMappings, $this->mappings);
        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'body' => [
                $mappings
            ]
        ];

        return $this->client->indices()->putMapping($params);
    }

    /**
     * 插入单条的文档
     * @param ESBaseDoc $baseDoc
     * @return array
     */
    public function insertOneDoc(ESBaseDoc $baseDoc) {
        //可以对param适当做些检查
        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'body' => [
                $baseDoc->toArray()
            ]
        ];
        return $this->client->index($params);
    }

    /**
     * @param ESBaseDoc[] $docArray
     */
    public function postBulkDoc(array $docArray) {
        if (count($docArray) == 0) {
            return;
        }
        $params = [];
        for ($i = 0; $i < count($docArray); $i++) {
            $params['body'][] = [
                'index' => [
                    '_index' => $this->index,
                    '_type' => $this->type,
                ]
            ];
            $params['body'][] = [
                $docArray[$i]->toArray()
            ];
        }
        $this->client->bulk($params);
    }

    /**
     * 根据id获得doc
     * @param $id
     * @return array|bool
     */
    public function getDocById($id) {
        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'id' => $id
        ];
        try {
            return $this->client->get($params);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * 根据id更新文档的内容
     * @param $id
     * @param ESBaseDoc $baseDoc
     * @return array|bool
     */
    public function updateDocById($id, ESBaseDoc $baseDoc) {

        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'id' => $id,
            'body' => [
                'doc' => [
                    $baseDoc->toArray()
                ]
            ]
        ];
        try {
            return $this->client->update($params);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * 根据id删除文档的内容
     * @param $id
     * @return array |bool
     */
    public function deleteDocById($id) {

        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'id' => $id
        ];
        try {
            return $this->client->delete($params);
        } catch (\Exception $exception) {
            return false;
        }
    }

    //Query的参数 https://www.elastic.co/guide/en/elasticsearch/reference/6.7/query-filter-context.html
    //https://es.xiaoleilu.com/054_Query_DSL/70_Important_clauses.html
    /**
     * 多个字段查询搜索,默认搜索可以用这个
     * @param $keyWords
     * @param array $field
     * @return array
     */
    public function search($keyWords, $field = []) {
        $params = [
            'index' => $this->index,
            'type' => $this->type,
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $keyWords,
                        "fields" => $this->getSearchFiled($field)
                    ]
                ]
            ]
        ];

        return $this->client->search($params);
    }

}
