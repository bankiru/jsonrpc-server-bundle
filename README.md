# JSON-RPC Server bundle

This bundle provides JSON-RPC 2.0 server ontop of the `bankiru/rpc-server-bundle` library.


## Serialization

This bundle uses `rpc.view` event in order to serialize response if request
was instance of `JsonRpcRequestInterface` and the response is not a 
`JsonRpcResponseInterface` object. You can bypass the serialization process 
with sending pre-created response object or implementing your own view listener

### Context

This library utilizes [JMS Serializer Bundle](https://github.com/schmittjoh/JMSSerializerBundle) 
to automatically serialize non-JSON-RPC controller response to serialized view. 
You can specify the serialization context at the routing level. Also you 
could disable default context usage with `with_default_context: false`

## Exception handling

Any unhandled exception from the controller would be automatically 
converted to the proper JSON-RPC failure response with INTERNAL_ERROR (-32603) code.
If you want do display other error you could extend `JsonRpcException` class or 
configure it manually like following:

```php

$exception = new JsonRpcException();
$execption->setJsonRpcError(
    new JsonRpcError(
        JsonRpcError::METHOD_NOT_FOUND,
        'Invalid method',
        (object)['debug_data' => 'some debug data']
    )
);

```

## Specification

Refer to official JSON-RPC 2.0 specification at  http://www.jsonrpc.org/specification
