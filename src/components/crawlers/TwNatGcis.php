<?php

namespace tsmd\corpinfo\components\crawlers;

use Exception;

/**
 * 从商工行政資料開放平臺获取企业信息的组件
 *
 * @see https://data.gcis.nat.gov.tw/od/demo
 */
class TwNatGcis extends CorpinfoCrawler
{
    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();

        $this->setFormatKey('corpNo', 'Business_Accounting_NO');
        $this->setFormatKey('status', 'Company_Status_Desc');
        $this->setFormatKey('corpName', 'Company_Name');
        $this->setFormatKey('capitalStock', 'Capital_Stock_Amount');
        $this->setFormatKey('paidCapital', 'Paid_In_Capital_Amount');
        $this->setFormatKey('responsibleName', 'Responsible_Name');
        $this->setFormatKey('address', 'Company_Location');
        $this->setFormatKey('registerOrganization', 'Register_Organization_Desc');
        $this->setFormatKey('setupDate', 'Company_Setup_Date');
        $this->setFormatKey('approvalDate', 'Change_Of_Approval_Data');
        $this->setFormatKey('revokeDate', 'Revoke_App_Date');
        $this->setFormatKey('caseStatus', 'Case_Status');
        $this->setFormatKey('caseStatusDesc', 'Case_Status_Desc');
        $this->setFormatKey('susDate', 'Sus_App_Date');
        $this->setFormatKey('susBegDate', 'Sus_Beg_Date');
        $this->setFormatKey('susEndDate', 'Sus_End_Date');

    }

    /**
     * 公司登記基本資料-應用一
     *
     * @param string $corpNo
     * @return array
     */
    public function grabCorp(string $corpNo)
    {
        return $this->grabInfoCompany($corpNo) ?: $this->grabInfoBusiness($corpNo);
    }

    /**
     * 公司登記基本資料-應用一
     *
     * @param string $corpNo
     * @return array
     */
    public function grabInfoCompany(string $corpNo)
    {
        try {
            $resp = $this->client->get('https://data.gcis.nat.gov.tw/od/data/api/5F64D864-61CB-4D0D-8AD9-492047CC1EA6', [
                'timeout' => 5,
                'query' => [
                    '$format' => 'json',
                    '$filter' => "Business_Accounting_NO eq {$corpNo}",
                ],
            ]);
            $raw = json_decode($resp->getBody()->getContents(), true)[0];
            return $this->format($raw);

        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * 商業登記基本資料-應用三
     *
     * @param string $corpNo
     * @return array
     */
    public function grabInfoBusiness(string $corpNo)
    {
        $this->setFormatKey('corpNo', 'President_No');
        $this->setFormatKey('status', 'Business_Current_Status_Desc');
        $this->setFormatKey('corpName', 'Business_Name');
        $this->setFormatKey('address', 'Business_Address');
        $this->setFormatKey('registerOrganization', 'Agency_Desc');

        try {
            $resp = $this->client->get('http://data.gcis.nat.gov.tw/od/data/api/426D5542-5F05-43EB-83F9-F1300F14E1F1', [
                'timeout' => 5,
                'query' => [
                    '$format' => 'json',
                    '$filter' => "President_No eq {$corpNo}",
                ],
            ]);
            $raw = json_decode($resp->getBody()->getContents(), true)[0];
            return $this->format($raw);

        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * @param array $raw
     * @param null $func
     */
    public function format(array $raw, $func = null)
    {
        // 格式化原生日期 1090830
        $rawkeys = [
            'Company_Setup_Date',
            'Change_Of_Approval_Data',
            'Revoke_App_Date',
            'Sus_App_Date',
            'Sus_Beg_Date',
            'Sus_End_Date',
        ];
        foreach ($rawkeys as $key) {
            if (!empty($raw[$key]) && preg_match('#(\d{3})(\d{2})(\d{2})#', $raw[$key], $m)) {
                $year = 1912 + $m[1] - 1;
                $raw[$key] = "{$year}-{$m[2]}-{$m[3]}";
            }
        }
        return parent::format($raw, $func);
    }
}
