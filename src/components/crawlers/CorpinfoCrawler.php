<?php

namespace tsmd\corpinfo\components\crawlers;

use yii\base\BaseObject;
use yii\base\InvalidValueException;
use GuzzleHttp\Client;

/**
 * 企业信息抓取抽象类
 */
abstract class CorpinfoCrawler extends BaseObject implements CorpinfoCrawlerInterface
{
    /**
     * @var array 将获取到的数据键名转换成固定键名
     */
    private $_formatKeys = [
        'corpNo'               => 'eg. 公司統一編號',
        'status'               => 'eg. 公司狀況描述',
        'corpName'             => 'eg. 公司名稱',
        'capitalStock'         => 'eg. 資本總額(元)',
        'paidCapital'          => 'eg. 實收資本額(元)',
        'responsibleName'      => 'eg. 代表人姓名',
        'address'              => 'eg. 公司登記地址',
        'registerOrganization' => 'eg. 登記機關名稱',
        'setupDate'            => 'eg. 核准設立日期',
        'approvalDate'         => 'eg. 最後核准變更日期',
        'revokeDate'           => 'eg. 撤銷日期',
        'caseStatus'           => 'eg. 停復業狀況',
        'caseStatusDesc'       => 'eg. 停復業狀況描述',
        'susDate'              => 'eg. 停業核准日期',
        'susBegDate'           => 'eg. 停業/延展期間(起)',
        'susEndDate'           => 'eg. 停業/延展期間(迄)',
    ];

    /**
     * @var Client
     */
    protected $client;
    /**
     * @var array
     */
    protected $reqHeaders = [];
    /**
     * @var array
     */
    protected $reqCookies = [];

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->client = new Client();
    }

    /**
     * @param string $formatKey
     * @param string $grabKey
     */
    public function setFormatKey($formatKey, $grabKey)
    {
        if (!isset($this->_formatKeys[$formatKey])) {
            throw new InvalidValueException("Format key `{$formatKey}` doesn't exist.");
        }
        $this->_formatKeys[$formatKey] = $grabKey;
    }

    /**
     * @param array $raw
     * @param callable $func
     */
    public function format(array $raw, $func = null)
    {
        $formatted = [];
        foreach ($this->_formatKeys as $formatKey => $grabKey) {
            if (stripos($grabKey, 'eg.') !== false) {
                $formatted[$formatKey] = null;
                continue;
            }
            $formatted[$formatKey] = key_exists($grabKey, $raw)
                ? (string) $raw[$grabKey]
                : '';

            // 日期格式化
            $dateFields = ['setupDate', 'approvalDate', 'revokeDate', 'susDate', 'susBegDate', 'susEndDate'];
            if (in_array($formatKey, $dateFields) && $formatted[$formatKey]) {
                $formatted[$formatKey] = ($time = strtotime($formatted[$formatKey]))
                    ? date('Y-m-d', $time)
                    : null;
            }
        }
        if (is_callable($func)) {
            $func($formatted, $raw);
        }
        return $formatted;
    }

    /**
     * @param array $corpNos
     * @return array
     */
    public function grabCorps(array $corpNos)
    {
        $corps = [];
        foreach ($corpNos as $corpNo) {
            if ($info = $this->grabCorp($corpNo)) {
                $corps[$corpNo] = $info;
            }
        }
        return $corps;
    }

    /**
     * 添加 Cookie
     * @param array $respCookies GuzzleHttp\Client 的响应头中的 Set-Cookie
     * @param bool $clear
     */
    public function addCookies(array $respCookies, $clear = false)
    {
        if ($clear) {
            $this->reqCookies = [];
        }
        foreach ($respCookies as $name => $cookie) {
            if (preg_match('#([^=]+)=([^;]*)#', $cookie, $m)) {
                $this->reqCookies[$m[1]] = $m[2];
            } else {
                $this->reqCookies[$name] = $cookie;
            }
        }
    }

    /**
     * 生成字符串 Cookie
     * @param array $cookies
     * @return string
     */
    public function buildCookies(array $cookies = [])
    {
        $out = '';
        foreach (array_merge($this->reqCookies ?: [], $cookies) as $key => $val) {
            $out .= "{$key}={$val}; ";
        }
        return rtrim($out, '; ');
    }

    /**
     * 生成請求頭
     * @param array $headers
     * @param array $cookies
     * @return array
     */
    public function buildHeaders(array $headers = [], array $cookies = [])
    {
        return array_merge($this->reqHeaders ?: [], ['Cookie' => $this->buildCookies($cookies)], $headers);
    }
}
