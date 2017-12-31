<?php

namespace Yison;

/**
 * 数字ID隐藏方案（混淆压缩）.
 *
 * @package Yison
 */
class HashId
{
    /**
     * 设置的进制.
     *
     * @var int
     */
    protected $base;

    /**
     * @var array
     */
    protected $mapper;

    /**
     * @var array
     */
    protected $mapperFlipped;

    /**
     * 支持进制的映射，使用前请先打乱元素顺序.
     *  a. 31进制去除了数字0, 1 与字母i, L, O
     *  b. 36进制包含数字、字母（不区分大小写）
     *  c. 62进制包含数字、小写字母、大写字母
     *  d. 64进制包含数字、小写字母、大写字母、额外2个符号（64 = 2 << 5）
     *
     * @var array
     */
    protected $availableMapper = [
        31 => [2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'],
        36 => [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'],
        62 => [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'],
        64 => [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '_', '='],
    ];

    /**
     * HashId constructor.
     *
     * @param int $base
     */
    public function __construct(int $base = 64)
    {
        if (!isset($this->availableMapper[$base])) {
            throw new Exception('Base ' . $base . ' is NOT support');
        }
        $this->base = $base;
        $this->mapper = $this->availableMapper[$this->base];
        $this->mapperFlipped = array_flip($this->availableMapper[$this->base]); // 反向映射实例化对象时初始化一次
    }

    /**
     * 传入数字id，返回对应的id字符串.
     *
     * @param int $num
     *
     * @return string
     */
    public function encode(int $num): string
    {
        // 进制转换
        $enc = $this->decToBase($num);
        // 字符向左循环移动位置（[移动字符数] = [每个字符ascii值之和] % [长度]。最少移动[0]位，最多移动[长度-1]位）
        $encStrLen = strlen($enc);
        // 至少4位
        if ($encStrLen <= 4) {
            $encStrLen = 4;
            $enc = str_pad($enc, 4, $this->mapper[0], STR_PAD_LEFT);
        }
        $ordSum = 0;
        for ($i = 0; $i < $encStrLen; $i++) {
            $ordSum += ord($enc{$i});
        }
        // 字符串循环左移
        $loop = $ordSum % $encStrLen;
        $enc = substr($enc, $loop) . substr($enc, 0, $loop);
        // 位移完成后，再混淆一次
        $newEnc = '';
        for ($i = 0; $i < $encStrLen; $i++) {
            $newEnc .= $this->mapper[($this->mapperFlipped[$enc{$i}] + ($i + 1) * 2) % $this->base];
        }

        return $newEnc;
    }

    /**
     * 传入字符串id，返回原始数字id. encode()的逆运算.
     *
     * @param string $enc
     *
     * @return int
     */
    public function decode(string $enc): int
    {
        // 细节参见encode()注释
        $encStrLen = strlen($enc);

        // 解除混淆
        $newEnc = '';
        for ($i = 0; $i < $encStrLen; $i++) {
            $index = $this->mapperFlipped[$enc{$i}] - ($i + 1) * 2;
            if ($index < 0) {
                $index += $this->base;
            }
            $newEnc .= $this->mapper[$index];
        }
        $enc = $newEnc;

        // 位移还原
        $ordSum = 0;
        for ($i = 0; $i < $encStrLen; $i++) {
            $ordSum += ord($enc{$i});
        }
        $loop = $ordSum % $encStrLen;
        $enc = substr($enc, $loop * -1, $loop) . substr($enc, 0, $encStrLen - $loop);
        // 进制转换
        return $this->baseToDec($enc);
    }

    /**
     * 十进制转N进制.
     *
     * @param int $dec
     *
     * @return string
     */
    protected function decToBase(int $dec): string
    {
        if ($dec < 0) {
            return false;
        }

        $base = '';
        do {
            $base = $this->mapper[$dec % $this->base] . $base;
            $dec = intdiv($dec, $this->base);

        } while ($dec >= 1);

        return $base;
    }

    /**
     * N进制转十进制.
     *
     * @param string $base
     *
     * @return int
     */
    protected function baseToDec(string $base): int
    {
        $baseLength = strlen($base);
        $dec = 0;
        for ($i = 0; $i < $baseLength; $i++) {
            $dec += $this->mapperFlipped[$base{$i}] * pow($this->base, $baseLength - $i - 1);
        }
        return $dec;
    }
}
