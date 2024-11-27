#设备通信接口
[TOC]
# sdk使用演示
>php接入sdk

```
composer require xjr-middle/mqtt-sdk
```
>使用demo

```
<?php
use XjrMiddle\MqttSdk\MqttManager;
//消息推送
public function publish($payload,$clientid){
    $app = MqttManager::init("你的APPID","你的APP_SECRET","中间件地址");
	$sendData = ['id'=>$clientid,'payload'=>json_encode($payload)];
	return $app::publish($sendData);
}
//添加设备
public  function addDev($device_id)
{
  $app = MqttManager::init("你的APPID","你的APP_SECRET","中间件地址");
  return $app::addDev(['DeviceId'=>$device_id]);
}
```

# 自定义对接
####接口域名

```
http://api.guangeiot.com/drop/
```
#### 公共参数
|字段|类型|空|注释|
|:----    |:-------    |:--- |--     |
|appId    |varchar(20)    |否 |      接口标识，请在后台查看   |
|sign     |varchar(64)   |否 |       签名 （签名方式在下面）  |


#### 签名算法
```
①设所有发送或者接收到的数据为集合M，将集合M内非空参数值的参数按照参数名ASCII码从小到大排序（字典序），使用URL键值对的格式（即key1=value1&key2=value2…）拼接成字符串stringA。

注意如下规则：

◆参数名ASCII码从小到大排序（字典序）；

◆如果参数的值为空不参与签名；

◆参数名区分大小写；

◆验证调用返回或主动通知签名时，传送的sign参数不参与签名，将生成的签名与该sign值作校验。

②在stringA最后拼接上appSecret(密钥)得到stringSignTemp字符串，并对stringSignTemp进行MD5运算，再将得到的字符串所有字符转换为大写，得到sign值signValue。
```
- 例如

```
param = appId=你的appId&key1=value1&key2=value2
stringSignTemp = param&appSecret=你的密钥
sign = md5(stringSignTemp)
```


##接口文档

    
#### 查询设备类型

> 请求URL
- ` http://xx.com/drop/mqtt/v1/getTypeList `

>  请求方式
- POST 
- Content-Type : application/x-www-form-urlencoded

> 参数

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|appId |是  |string |用户标识   |
|sign |是  |string | 签名    |
|time     |否  |string | 请求时间    |

>  返回示例 
``` 
{
	"requestId": "d6e15d8f-2a4f-46f1-8b5b-03877e9ba6c7", 
	"code": 200, 
	"msg": "查询成功", 
	"data": [
		{
		"key": "XJRCR",  /类型标识
		"name": "快递柜"  //类型名称
		},
		{
		"key": "XJRKR", 
		"name": "快递柜4" 
		}
	] 
}
```

> 返回错误
``` 
{
	"requestId": "d6e15d8f-2a4f-46f1-8b5b-03877e9ba6c7", 
	"code": 500, 
	"msg": "签名错误"
}
```

#### 查询设备列表

> 请求URL
- ` http://xx.com/drop/mqtt/v1/getDevList `

>  请求方式
- POST 
- Content-Type : application/x-www-form-urlencoded

> 参数

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|appId |是  |string |用户标识   |
|sign |是  |string | 签名    |
|id     |否  |string | 要查询的设备id，多个用,隔开 例如 XJRZT0105,DEV0007    |
|queryTime     |否  |string | 请求时间    |

>  返回示例 
``` 
{
	"requestId": "d6e15d8f-2a4f-46f1-8b5b-03877e9ba6c7", 
	"code": 200, 
	"msg": "查询成功", 
	"data": [
	    	{
				"deviceId": "DEV0007",  //设备编号
				"typeKey": "XJRNR",  //类型标识
				"typeName": "智能快递柜", //类型名称
				"devName": "快递柜",  //设备名称
				"status": 0,  //状态 0离线1在线
				"dustbinStatus": "",  //垃圾回收箱不同箱体状态，没有对接垃圾回收箱可忽略
				"offline_time": "" //最后离线时间
			},
			{
				"deviceId": "XJRZT0105", 
				"typeKey": "XJRZR", 
				"typeName": "存储柜2", 
				"devName": "设备名称6", 
				"status": 0, 
				"dustbinStatus": "", 
				"offline_time": "2022-07-08T11:35:08+08:00" 
			}
	]
}
```

> 返回错误
``` 
{
	"requestId": "d6e15d8f-2a4f-46f1-8b5b-03877e9ba6c7", 
	"code": 500, 
	"msg": "签名错误"
}
```


#### 添加设备

> 请求URL
- ` http://xx.com/drop/mqtt/v1/addDev `

>  请求方式
- POST 
- Content-Type : application/x-www-form-urlencoded

> 参数

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|appId |是  |string |用户标识   |
|DeviceId     |是  |string | 设备id    |
|sign |是  |string | 签名    |
|queryTime     |否  |string | 请求时间    |

>  返回示例 
``` 
{
	"requestId": "3eacea65-de7e-4ac0-a6b0-ceab46abc22a", 
	"code": 200, 
	"msg": "创建成功", 
	"data": "XJRZT0105" 
}
```

> 返回错误
``` 
{
	"requestId": "d6e15d8f-2a4f-46f1-8b5b-03877e9ba6c7", 
	"code": 500, 
	"msg": "签名错误"
}
```


#### 删除设备

> 请求URL
- ` http://xx.com/drop/mqtt/v1/delDevs `

>  请求方式
- POST 
- Content-Type : application/x-www-form-urlencoded

> 参数

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|appId |是  |string |用户标识   |
|sign |是  |string | 签名    |
|id     |否  |string | 要删除的设备id，多个用,隔开 例如 XJRZT0105,DEV0007    |
|queryTime     |否  |string | 请求时间    |

>  返回示例 
``` 
{
	"requestId": "3eacea65-de7e-4ac0-a6b0-ceab46abc22a", 
	"code": 200, 
	"msg": "删除成功", 
	"data": "XJRZT0105" 
}
```

> 返回错误
``` 
{
	"requestId": "d6e15d8f-2a4f-46f1-8b5b-03877e9ba6c7", 
	"code": 500, 
	"msg": "签名错误/删除失败"
}
```


#### 推送数据到设备

> 请求URL
- ` http://xx.com/drop/mqtt/v1/publish `

>  请求方式
- POST 
- Content-Type : application/x-www-form-urlencoded

> 参数

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|appId |是  |string |用户标识   |
|sign |是  |string | 签名    |
|id     |是  |string | 设备id，多个用,隔开 例如 XJRZT0105,DEV0007    |
|payload     |是  |string | 推送数据，格式为json 具体要根据所对接的设备类型相关接口推送    |
|queryTime     |否  |string | 请求时间    |

>  返回示例 
``` 
{
	"requestId": "50ae7575-b652-4ca2-9bd9-791071c1712a", 
	"code": 200, 
	"msg": "命令已发送", 
	"data": null 
}
```

> 返回错误
``` 
{
	"requestId": "d6e15d8f-2a4f-46f1-8b5b-03877e9ba6c7", 
	"code": 500, 
	"msg": "签名错误/设备不在线"
}
```



#回调地址配置

>服务器地址说明
用于接收设备请求，如果需要自行实现后台业务逻辑，请配置自己的业务接口，具体对接文档，请根据设备类型，向软件方索要。

>通用回调事件
onDeviceStatus
用于实时监听设备在线状态，当设备离线或者上线时，设备会向回调地址推送该事件，可以实现该事件更新设备状态