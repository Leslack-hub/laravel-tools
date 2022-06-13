# laravel-tools

laravel 7.x 工具类

## 一、ShardingModel 单字段分表

### 准备:

继承ShardingModel类 设置SHARDING_COLUMN 分表字段
实现SharingTable 方法。按照规则返回指定表名称

```php
class Order extends ShardingModel
{
    /**
     * 分表字段
     */
    const SHARDING_COLUMN = 'user_id';

    /**
     * 获取表名称
     *
     * @param $userId
     *
     * @return string
     */
    public static function shardingTable($userId)
    {
        return 'order_' . (int)($userId / 1000000);
    }
}
```

### 使用：

单字段分表无需修改代码 where中必须含有分表字段，会在where条件中指定的值修改指定的表

```php
注： where在查询字段 必须与SHARDING_COLUMN一致
Order::create([
    'user_id' => $userId,
    'order_id' => 'xxxxxx',
]);
连表查询时 需先获取表名
$tableName = Order::shardingTable($userId);
$builder = OrderDetail::query()
    ->leftjoin($tableName, function ($join) use ($userId, $tableName) {
    $join->on('order_detail.id', '=', $tableName . '.order_id')
        ->where($tableName . '.user_id', '=', $userId)
        ->where($tableName . '.deleted_at', null);
})
```

## 二、MultiShardingModel 多字段分表

### 准备：

需要在单字段分表的基础上实现
按照id分表 为分表的主表
如：按照id 4000w 以下 分了2表 为sharding_order_id_0 sharding_order_id_1
冗余表：
按照user_id 500w用户一张冗余表 实现shardingByUserId 方法，返回指定表名
配置SHARDING_COLUMNS
'user_id' => [ // 分表字段
'condition' => ['id'], // 查询规则
'fill' => ['order_id'], // 需要同步的属性
],
按照merchant_id 分表同理

```php
class Order extends MultiShardingModel
{

    const SHARDING_COLUMN = 'id';
    
    public static function shardingTable($id)
    {
        if ($id > 0) {
            $num = (int)($id / 20000000);
            if ($num < 2) {
                return 'sharding_order_id_' . $num;
            }
        }

        return 'order';
    }

    const SHARDING_COLUMNS = [
        'user_id' => [
            'condition' => ['id'],
            'fill' => ['id','order_id','created_at'],
        ],
        'merchant_id' => [
            'condition' => ['id'],
            'fill' => ['id','user_id','created_at'],
        ],
    ];

    public static function shardingByUserId($uid)
    {
        return 'sharding_order_user_id_' . (int)($uid / 5000000);
    }

    public static function shardingByMerchantId($merchantId)
    {
        return 'sharding_order_merchant_id_' . (int)($merchantId / 5000000);
    }
}
```

### 用法：

查询主表时 同ShardingModel 一样
也可以使用 shardingWhere 方法指定查询表

```php
Order::withTrashed()->shardingWhere('id', 1112)->first()
```

查询冗余表 如：用户 123 的所有订单

```php
$orders = order::query()->zoneWhere('user_id', 123)->get();
更新user_id 所有用户订单时间 会同步更新其他主表 或冗余表 deleteAll 方法能够删除
order::query()->zoneWhere('user_id', 123)->updateAll(['created_at' => '2022-01-01 00:00:00']);
```

连表查询参考ShardingModel

## 三、ArrayService 按照指定key 获取多组的值

使用场景

```php
switch($type) {
case 1:
    $name = 'name_1';
break;
case 2:
    $name = 'name_2';
break;
case 3:
    $name = 'name_3';
break;
case 4:
    $name = 'name_4';
break;
}
// 执行一些业务逻辑
doSomeThing();

switch($type) {
case 1:
    $callback = function(){};
break;
case 2:
    $callback = function(){};
break;
case 3:
    $callback = function(){};
break;
case 4:
    $callback = function(){};
break;
}
```

### 用法：

```php
use LeslackHub\LaravelTools\ArrayService;
$type =1;
$arrayService = new ArrayService([1,2,3,4],['name_1','name_2','name_3','name4'],[function(){},function(){},function(){},function(){}])
$name = $arrayService->getValue('0.'.$type);
$callback = $arrayService->getValue('1.'.$type);
// 增加类型 1 2 3 4 对应的标题 
$arrayService->push(['tilte_1','tilte2','title_3','title_4'],'title');
$arrayService->getValue('title.'.$type);

```
