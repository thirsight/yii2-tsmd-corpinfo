<?php

namespace tsmd\corpinfo\components;

use Yii;
use yii\base\BaseObject;
use tsmd\corpinfo\components\crawlers\CorpinfoCrawler;

/**
 * @author Haisen <thirsight@gmail.com>
 * @since 1.0
 */
class Corpinfo extends BaseObject
{
    /**
     * @var CorpinfoCrawler[]
     */
    public $crawlers;

    /**
     * @inheritDoc
     */
    public function init()
    {
        foreach ($this->crawlers as &$class) {
            $class = Yii::createObject($class);
        }
    }

    /**
     * @param string $corpNo
     * @return array
     */
    public function grabCorp(string $corpNo)
    {
        foreach ($this->crawlers as $crawler) {
            if ($info = $crawler->grabCorp($corpNo)) {
                return $info;
            }
        }
        return [];
    }

    /**
     * @param array $corpNos
     * @return array
     */
    public function grabCorps(array $corpNos)
    {
        $all = [];
        foreach ($this->crawlers as $crawler) {
            $infos = $crawler->grabCorps($corpNos);
            $all = array_merge($all, $infos);

            $corpNos = array_diff($corpNos, array_keys($infos));
            if (empty($corpNos)) break;
        }
        return $all;
    }
}
