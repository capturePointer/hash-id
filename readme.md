# Hash ID
数字ID隐藏方案（混淆压缩），可用于数据库主键对用户的隐藏。

## Features
* 10进制id转换为31/36/62/64进制字符串
* 进制转换实现了id长度压缩，可用于**短链接生成方案**
* 进制转换后，做了一些混淆
* 得到的字符串id不连续，可**防止对id顺序采集**
* 编码得到的字符串id可以还原成数字id
* id支持范围：0到PHP_INT_MAX
* **不是加密**，如果需要绝对安全请使用加密算法

## Install
```shell
composer require yison/hash-id
```
## Usage
```PHP
$base = 64;
$hashId = new Yison\HashId($base);
$id = 100;
$encr = $hashId->encode($id);
$decr = $hashId->decode($encr);

printf('%d, %s, %d' . PHP_EOL, $id, $encr, $decr); // 输出：100, 3E68, 100
```