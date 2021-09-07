<?php

namespace tsmd\corpinfo\components\crawlers;

/**
 * 企业信息抓取接口
 *
 * @author Haisen <thirsight@gmail.com>
 * @since 1.0
 */
interface CorpinfoCrawlerInterface
{
    /**
     * 获取单个企业信息
     *
     * @param string $corpNo
     * @return array
     */
    public function grabCorp(string $corpNo);

    /**
     * 获取多个企业信息
     *
     * @param array $corpNos
     * @return array
     */
    public function grabCorps(array $corpNos);

    /**
     * 格式化单个企业信息
     *
     * @param array $corp
     * @param callable $func
     */
    public function format(array $corp, $func = null);
}
