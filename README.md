## Intro

a extension based on resque/php-resque, just make easier to use resque/php-resque

## How To Use

For Client:

```
    // initialize resque client config
    // todo this must be first initialized before class ResqueClient functions
    $instance = ResqueClientConfig::instance();
    $instance->setRedisBackEnd('localhost');
    $instance->setRedisBackEndDb(0);

    // push job to queue
    ResqueClient::enqueue('test',job_echo::class, ['time' => date('Y-m-d H:i:s')]);

    // push job to queue at [time() + 60]
    ResqueClient::enqueueAt(time() + 60, 'test',job_echo::class, ['time' => date('Y-m-d H:i:s')]);

    // push job to queue after 60 seconds
    ResqueClient::enqueue(60 , 'test',job_echo::class, ['time' => date('Y-m-d H:i:s')]);
```


For Server:

if you just want to run one worker, use ResqueServer is more easier.

e still recommend you to use ResqueDeamon for a quick expand at a furture moment ,even though  ResqueServer is more easier.

```
    // set server config
    $config = new ResqueServerConfig(true, ['test'], 1, 'localhost');

    // start up resque server, this will block the process
    ResqueServer::startup($config);
```

if you need multi resque service to run, use ResqueDeamon is highly recommended.
```
    // set deamon configs
    $configs = [];
    // resque scheduler
    $configs[] = new ResqueServerConfig(false, ['test-01'], 1, 'localhost');

    // three workers 
    $configs[] = new ResqueServerConfig(true, ['test-01'], 1, 'localhost');
    $configs[] = new ResqueServerConfig(true, ['test-02'], 2, 'localhost');
    $configs[] = new ResqueServerConfig(true, ['test-03'], 3, 'localhost');


    // start up resque services
    ResqueDeamon::startup($configs);

    // shutdown resque services
    ResqueDeamon::shutdown($configs);

    // re start up resque services
    ResqueDeamon::restartup($configs);

```
