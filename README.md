# 说明

这是一个 imi WebSocket 项目开发骨架项目，你可以基于这个项目来开发你的项目。

imi 框架：<https://www.imiphp.com>

imi 文档：<https://doc.imiphp.com>

---

这是一个 WebSocket、Http 共存服务的示例

WebSocket 连接地址:`ws://127.0.0.1:8081/ws`

Swoole 支持 WebSocket+Http 共享端口，Http 测试访问地址:<http://127.0.0.1:8081/>、<http://127.0.0.1:8081/api>

Workerman Http 测试访问地址:<http://127.0.0.1:8080/>、<http://127.0.0.1:8080/api>

`test-html/index.html` 文件可以连接 WebSocket 进行调试

## 安装

创建项目：`composer create-project imiphp/project-websocket:~2.1.0`

如果你希望在 Swoole 运行 imi：`composer require imiphp/imi-swoole:~2.1.0`

## 配置

### 项目命名空间

默认是 `ImiApp`，可以在 `composer.json` 中修改：

* `autoload.psr-4.ImiApp`

* `imi.namespace`

然后替换代码中的命名空间即可。

### 运行配置

项目配置目录：`config`

WebSocket 服务器配置目录：`WebSocketServer/config`

## 启动命令

**普通启动：**

**Swoole：**`vendor/bin/imi-swoole swoole/start`

**Workerman：**`vendor/bin/imi-workerman workerman/start`

**网关环境启动：**

**配置文件一键切换网关模式：**`mv config/config.php config/config.php.bak && mv config/config-geteaway.php config/config.php && mv WebSocketServer/config/config.php WebSocketServer/config/config.php.bak && mv WebSocketServer/config/config-geteaway.php WebSocketServer/config/config.php`

**注册中心：**`vendor/bin/imi-workerman workerman/start --name register`

**网关：**`vendor/bin/imi-workerman workerman/start --name gateway`

**Swoole Worker：**`vendor/bin/imi-swoole swoole/start`

**Workerman Worker：**`vendor/bin/imi-workerman workerman/start --name websocketWorker`

指定 worker 名称：(多个 worker 实例需要不一样的名字)

```shell
IMI_WORKER_NAME=xxx vendor/bin/imi-swoole swoole/start
IMI_WORKER_NAME=xxx vendor/bin/imi-workerman workerman/start --name xxx
```

> 切换环境运行前建议删除 `.runtime/imi-runtime` 目录：`rm -rf .runtime/imi-runtime`

> Swoole 网关模式需要依赖 Workerman 启动 register、gateway，其它服务器配置先注释；运行时需要先 workerman 启动命令，再运行 swoole 启动命令

## 测试

连接：`ws://127.0.0.1:8081/ws`

发送：

```js
{"action":"send", "message":"test"}
```

返回：

```js
{"success":true, "data":"[127.0.0.1:50820]: test", "mode":"swoole"}
```

## 权限

`.runtime` 目录需要有可写权限

## 多种模式

### 本地

支持 Swoole，适合单实例部署

支持 Workerman，适合单进程开发调试

**config/config.php：**

```php
[
    // 主服务器配置
    'mainServer'    =>    'swoole' === $mode ? [
        'namespace' =>  'ImiApp\WebSocketServer',
        'type'      =>  Imi\Swoole\Server\Type::WEBSOCKET,
        'host'      =>  '0.0.0.0',
        'port'      =>  8081,
        'mode'      =>  SWOOLE_BASE,
        'configs'   =>    [
            // 'worker_num'        =>  8,
            // 'task_worker_num'   =>  16,
        ],
        'beans' => [
            'ServerUtil' => \Imi\Swoole\Server\Util\LocalServerUtil::class,
        ],
    ] : [],

    // 子服务器（端口监听）配置
    'subServers'        =>    'swoole' === $mode ? [
        // 'SubServerName'   =>  [
        //     'namespace'    =>    'ImiApp\XXXServer',
        //     'type'        =>    Imi\Server\Type::HTTP,
        //     'host'        =>    '0.0.0.0',
        //     'port'        =>    13005,
        // ]
    ] : [],

    // Workerman 服务器配置
    'workermanServer' => 'workerman' === $mode ? [
        'http' => [
            'namespace' => 'ImiApp\WebSocketServer',
            'type'      => Imi\Workerman\Server\Type::HTTP,
            'host'      => '0.0.0.0',
            'port'      => 8080,
            'configs'   => [
                'count' => 1,
            ],
            'beans' => [
                'ServerUtil' => \Imi\Workerman\Server\Util\LocalServerUtil::class,
            ],
        ],
    ] : [],
]
```

**WebSocketServer/config/config.php：**

```php
[
    'beans' => [
        'ConnectionContextStore'   =>  [
            'handlerClass'  =>  \Imi\Server\ConnectionContext\StoreHandler\Local::class,
        ],
        'ConnectionContextLocal'    =>    [
            'lockId'    =>  null, // 非必设，可以用锁来防止数据错乱问题
        ],
        'ServerGroup' => [
            'groupHandler' => 'GroupLocal',
        ],
    ],
]
```

### Redis 分布式

支持 Swoole，适合分布式

**config/config.php：**

```php
[
    
    // 主服务器配置
    'mainServer'    =>    'swoole' === $mode ? [
        'namespace' =>  'ImiApp\WebSocketServer',
        'type'      =>  Imi\Swoole\Server\Type::WEBSOCKET,
        // 'type'      => \Imi\WorkermanGateway\Swoole\Server\Type::BUSINESS_WEBSOCKET, // workerman gateway 模式 Worker
        'host'      =>  '0.0.0.0',
        'port'      =>  8081,
        'mode'      =>  SWOOLE_BASE,
        'configs'   =>    [
            // 'worker_num'        =>  8,
            // 'task_worker_num'   =>  16,
        ],
        'beans' => [
            'ServerUtil' => 'RedisServerUtil',
        ],
    ] : [],
]
```

将 `pools.redis.resource.options` 下的 `\Redis::OPT_READ_TIMEOUT` 注释去除。

**WebSocketServer/config/config.php：**

```php
[
    'beans' => [
        // Redis 模式
        'ConnectionContextStore'   =>  [
            'handlerClass'  =>  \Imi\Server\ConnectionContext\StoreHandler\Redis::class,
        ],
        'ConnectionContextRedis'    =>    [
            'redisPool'    => 'redis', // Redis 连接池名称
            'redisDb'      => 0, // redis中第几个库
            'key'          => 'imi:connect_context', // 键
            'heartbeatTimespan' => 5, // 心跳时间，单位：秒
            'heartbeatTtl' => 8, // 心跳数据过期时间，单位：秒
            'dataEncode'=>  'serialize', // 数据写入前编码回调
            'dataDecode'=>  'unserialize', // 数据读出后处理回调
            'lockId'    =>  null, // 非必设，可以用锁来防止数据错乱问题
        ],
        'ServerGroup' => [
            'groupHandler' => 'GroupRedis',
        ],
    ],
]
```

### Channel 模式

支持 Workerman，适合所有场景

**config/config.php：**

```php
[
    // Workerman 服务器配置
    'workermanServer' => 'workerman' === $mode ? [
        'http' => [
            'namespace' => 'ImiApp\WebSocketServer',
            'type'      => Imi\Workerman\Server\Type::HTTP,
            'host'      => '0.0.0.0',
            'port'      => 8080,
            'configs'   => [
                'count' => 1,
            ],
            'beans' => [
                'ServerUtil' => 'ChannelServerUtil',
            ],
        ],
        // Workerman Gateway 模式请注释 websocket
        'websocket' => [
            'namespace'   => 'ImiApp\WebSocketServer',
            'type'        => Imi\Workerman\Server\Type::WEBSOCKET,
            'host'        => '0.0.0.0',
            'port'        => 8081,
            'shareWorker' => 'http',
            'beans' => [
                'ServerUtil' => 'ChannelServerUtil',
            ],
        ],
        'channel' => [
            'namespace'   => '',
            'type'        => Imi\Workerman\Server\Type::CHANNEL,
            'host'        => '127.0.0.1',
            'port'        => 13005,
            'configs'     => [
            ],
        ],
    ] : [],
    'workerman' => [
        // 多进程通讯组件配置
        'channel' => [
            'host' => '127.0.0.1',
            'port' => 13005,
        ],
    ],
]
```

**WebSocketServer/config/config.php：**

```php
[
    'beans' => [
        'ConnectionContextStore'   => [
            'handlerClass'  => 'ConnectionContextLocal',
            'ttl'           => 600,
        ],
    ],
]
```

### Workerman Gateway 模式

支持 Swoole、Workerman，适合分布式场景

**config/config.php：**

```php
[
    // 主服务器配置
    'mainServer'    =>    'swoole' === $mode ? [
        'namespace' =>  'ImiApp\WebSocketServer',
        'type'      => \Imi\WorkermanGateway\Swoole\Server\Type::BUSINESS_WEBSOCKET, // workerman gateway 模式 Worker
        'host'      =>  '0.0.0.0',
        'port'      =>  8081,
        'mode'      =>  SWOOLE_BASE,
        'configs'   =>    [
            // 'worker_num'        =>  8,
            // 'task_worker_num'   =>  16,
        ],
        'beans' => [
            'ServerUtil' => 'SwooleGatewayServerUtil',
        ],
        // workerman gateway 模式
        'workermanGateway' => [
            'registerAddress'      => '127.0.0.1:13004',
            'worker_coroutine_num' => swoole_cpu_num(),
            'channel'              => [
                'size' => 1024,
            ],
        ],
    ] : [],

    // 子服务器（端口监听）配置
    'subServers'        =>    'swoole' === $mode ? [
        // 'SubServerName'   =>  [
        //     'namespace'    =>    'ImiApp\XXXServer',
        //     'type'        =>    Imi\Server\Type::HTTP,
        //     'host'        =>    '0.0.0.0',
        //     'port'        =>    13005,
        // ]
    ] : [],

    // Workerman 服务器配置
    'workermanServer' => 'workerman' === $mode ? [
        'http' => [
            'namespace' => 'ImiApp\WebSocketServer',
            'type'      => Imi\Workerman\Server\Type::HTTP,
            'host'      => '0.0.0.0',
            'port'      => 8080,
            'configs'   => [
                'count' => 1,
            ],
        ],
        'channel' => [
            'namespace'   => '',
            'type'        => Imi\Workerman\Server\Type::CHANNEL,
            'host'        => '127.0.0.1',
            'port'        => 13005,
            'configs'     => [
            ],
        ],
        // 以下是 Workerman Gateway 模式需要
        'register' => [
            'namespace'   => 'Imi\WorkermanGateway\Test\AppServer\Register',
            'type'        => Imi\WorkermanGateway\Workerman\Server\Type::REGISTER,
            'host'        => '127.0.0.1',
            'port'        => 13004,
            'configs'     => [
            ],
        ],
        'gateway' => [
            'namespace'   => 'Imi\WorkermanGateway\Test\AppServer\Gateway',
            'type'        => Imi\WorkermanGateway\Workerman\Server\Type::GATEWAY,
            'socketName'  => 'websocket://0.0.0.0:8081', // 网关监听的地址
            'configs'     => [
                'lanIp'           => '127.0.0.1',
                'startPort'       => 12900,
                'registerAddress' => '127.0.0.1:13004',
            ],
        ],
        // workerman gateway 模式 Worker
        'websocketWorker' => [
            'namespace'   => 'ImiApp\WebSocketServer',
            'type'        => Imi\WorkermanGateway\Workerman\Server\Type::BUSINESS_WEBSOCKET,
            'shareWorker' => '\\' === \DIRECTORY_SEPARATOR ? 'http' : null,
            'configs'     => [
                'registerAddress' => '127.0.0.1:13004',
                'count'           => 2,
            ],
            'beans' => [
                'ServerUtil' => 'WorkermanGatewayServerUtil',
            ],
        ],
    ] : [],
]
```

**WebSocketServer/config/config.php：**

```php
[
    'beans' => [
        // 网关模式
        'ConnectionContextStore'   => [
            'handlerClass'  => 'ConnectionContextGateway',
        ],
        'ServerGroup' => [
            'groupHandler' => 'GroupGateway',
        ],
    ],
]
```

## 生产环境

**关闭热更新：**`config/beans.php` 中 `hotUpdate.status` 设为 `false`

生产环境建议只保留一个容器，可以提升性能，imi 官方推荐使用 **Swoole**！

**移除 `imi-workerman`：**`composer remove imiphp/imi-workerman`

**移除 `imi-swoole`：**`composer remove imiphp/imi-swoole`（不推荐）

## 代码质量

### 格式化代码

内置 `php-cs-fixer`，统一代码风格。

配置文件 `.php-cs-fixer.php`，可根据自己实际需要进行配置，文档：<https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/doc/config.rst>

**格式化项目：** `./vendor/bin/php-cs-fixer fix`

**格式化指定文件：** `./vendor/bin/php-cs-fixer fix test.php`

### 代码静态分析

内置 `phpstan`，可规范代码，排查出一些隐藏问题。

配置文件 `phpstan.neon`，可根据自己实际需要进行配置，文档：<https://phpstan.org/config-reference>

**分析项目：** `./vendor/bin/phpstan`

**分析指定文件：** `./vendor/bin/phpstan test.php`

### 测试用例

内置 `phpunit`，可以实现自动化测试。

**文档：**<https://phpunit.readthedocs.io/en/9.5/>

**测试用例 demo：**`tests/Module/Test/TestServiceTest.php`

**运行测试用例：**`composer test`
