<?php

namespace Luke\Yonbip;

/**
 * 用友模块
 */
class Yon extends YonApi
{
    /**
     * 客户组织单元
     * @return boolean|array
     */
    public function getOrg($org_id = '')
    {
        $res = $this->request('POST', '/digitalModel/orgunit/querytree');
        $orgs[] = [
            "hasDefaultInit" => true,
            "rangeType" => 1,
            "org" => "666666",
            "isCreator" => "true",
            "isApplied" => "true",
            "tenant" => 2088893204877568,
            "org_code" => "global00",
            "org_name" => "企业账号级",
            "_status" => "Insert"
        ];
        if (isset($res['data'][0]['code'])) {
            $orgs[] = [
                "hasDefaultInit" => true,
                "rangeType" => 1,
                "org" => $res['data'][0]['id'],
                "isCreator" => "true",
                "isApplied" => "true",
                "tenant" => 2088893204877568,
                "org_code" => $res['data'][0]['code'],
                "org_name" => $res['data'][0]['name'],
                "_status" => "Insert"
            ];
            if ($org_id && $org_id == $res['data'][0]['code']) {
                return $res['data'][0]['id'];
            }
            foreach ($res['data'][0]['children'] as $v) {
                if ($org_id && $org_id == $v['code']) {
                    return $v['id'];
                }
                $orgs[] = [
                    "hasDefaultInit" => true,
                    "rangeType" => 1,
                    "org" => $v['id'],
                    "isCreator" => "true",
                    "isApplied" => "true",
                    "tenant" => 2088893204877568,
                    "org_code" => $v['code'],
                    "org_name" => $v['name'],
                    "_status" => "Insert"
                ];
            }
        }

        if ($org_id) {
            return false;
        }
        return $orgs;
    }

    /**
     * 客户组织单元
     * @return array
     */
    public function getUserOrg()
    {
        $res = $this->request('POST', '/digitalModel/orgunit/querytree');
        $orgs[] = [
            "hasDefaultInit" => true,
            "rangeType" => 1,
            "orgId" => "666666",
            "isCreator" => "true",
            "isApplied" => "true",
            "tenant" => 2088893204877568,
            "orgCode" => "global00",
            "orgName" => "企业账号级",
            "_status" => "Insert"
        ];
        if (isset($res['data'][0]['code'])) {
            $orgs[] = [
                "hasDefaultInit" => true,
                "rangeType" => 1,
                "orgId" => $res['data'][0]['id'],
                "isCreator" => "false",
                "isApplied" => "false",
                "tenant" => 2088893204877568,
                "orgCode" => $res['data'][0]['code'],
                "orgName" => $res['data'][0]['name'],
                "_status" => "Insert"
            ];
            foreach ($res['data'][0]['children'] as $v) {
                $orgs[] = [
                    "hasDefaultInit" => true,
                    "rangeType" => 1,
                    "orgId" => $v['id'],
                    "isCreator" => "false",
                    "isApplied" => "false",
                    "tenant" => 2088893204877568,
                    "orgCode" => $v['code'],
                    "orgName" => $v['name'],
                    "_status" => "Insert"
                ];
            }
        }

        return $orgs;
    }

    /**
     * 搜索客户档案详情查询
     * @param string $code
     * @return array
     */
    public function searchUser($code)
    {
        $code = $this->userCode($code);
        $options = [
            'json' => [
                'pageIndex' => 1,
                'pageSize' => 10,
                'code' => $code,
            ]
        ];
        return $this->request('POST', '/digitalModel/merchant/list', $options);
    }

    /**
     * 客户档案详情查询
     * @param number $id
     * @return array
     */
    public function getUser($id)
    {
        $options = [
            'query' => [
                'id' => $id
            ]
        ];
        return $this->request('GET', '/digitalModel/merchant/detail', $options);
    }

    /**
     * 客户档案保存
     * @param string $name
     * @param string $code
     * @return array
     */
    public function setUser($data)
    {
        $code = $this->userCode($data['code']);
        $name = $data['name'] . '(' . $data['mobile'] . ')';
        $options = [
            'json' => [
                'data' => [
                    'createOrg' => $data['org'],
                    "createOrg_name" => "企业账号级",
                    "createOrg_code" => "global00",
                    'code' => $code,
                    'name' => [
                        "zh_CN" => $name,
                        "en_US" => null,
                        "zh_TW" => null
                    ],
                    "_status" => "Insert",
                ]
            ]
        ];
        $options['json']['data']['merchantApplyRanges'] = $this->getUserOrg();
        return $this->request('POST', '/digitalModel/merchant/save', $options);
    }

    /**
     * 搜索客户档案详情查询
     * @param number $id
     * @return array
     */
    public function searchSupplier($code, $name = '')
    {
        $code = $this->supplierCode($code);
        $options = [
            'json' => [
                'pageIndex' => 1,
                'pageSize' => 10,
                'code' => $code,
            ]
        ];
        return $this->request('POST', '/digitalModel/vendor/list', $options);
    }

    /**
     * 供应商档案保存
     * @param number $id
     * @return array
     */
    public function setSupplier($data)
    {
        if (!$data['name'] || !is_numeric($data['mobile'])) {
            return ['message' => '用户信息不完整', 'code' => '-1'];
        }
        $name = $data['name'] . '(' . $data['mobile'] . ')';
        $code = $this->supplierCode($data['code']);
        $options = [
            'json' => [
                'data' => [
                    'org' => $data['org'],
                    'org_code' => 'global00',
                    "org_name" => "企业账号级",
                    'code' => $code,
                    'name' => [
                        "zh_CN" => $name
                    ],
                    "vendorclass" => $data['vendorclass'],
                    "vendorclass_code" => "01",
                    "vendorclass_name" => "内部供应商",
                    "_status" => "Insert"
                ]
            ]
        ];
        $options['json']['data']['vendorOrgs'] = $this->getOrg();
        return $this->request('POST', '/digitalModel/vendor/save', $options);
    }


    /**
     * 供应商档案查询
     * @param array $data
     * @return array
     */
    public function getSupplier($id, $vendorApplyRangeId)
    {
        $options = [
            'query' => [
                'id' => $id,
                'vendorApplyRangeId' => $vendorApplyRangeId
            ]
        ];
        return $this->request('GET', '/digitalModel/vendor/detail', $options);
    }

    /**
     * 付款单
     * @param array $data
     * @return array
     */
    public function makePayment($data)
    {
        $result = [
            'code' => -1,
            'message' => '连接失败',
            'time' => time(),
            'data' => []
        ];
        $supplier = $this->searchSupplier($data['user']['code']);
        if (isset($supplier['data']['recordList'][0]['code']) && $supplier['data']['recordList'][0]['code'] == $this->supplierCode($data['user']['code'])) {
            $data['supplier'] = $supplier['data']['recordList'][0]['id'];
        } else {
            $res = $this->setSupplier($data['user']);
            if (isset($res['data']['id'])) {
                $data['supplier'] = $res['data']['id'];
            } else {
                $result['message'] = isset($res['message']) ? $res['message'] : "用户插入失败";
                return $result;
            }
        }

        $options = [
            'json' => [
                'data' => [
                    'code' => $data['code'],
                    'vouchdate' => date('Y-m-d H:i:s'),
                    'accentity_code' => $data['org'],
                    'exchangeRateType_code' => "01",
                    'oriSum' => $data['money'],
                    'natSum' => $data['money'],
                    'balance' => $data['balance'],
                    'supplier' => $data['supplier'],
                    'description' => $data['extra'],
                    "exchRate" => 1,
                    "currency" => "2088893397850624",
                    "currency_name" => "人民币",
                    "currency_priceDigit" => "2",
                    "currency_moneyDigit" => "2",
                    "exchangeRateType" => "ghdglz4y",
                    "exchangeRateType_digit" => "6",
                    "tradetype" => "2088893315207425",
                    "_status" => "Insert",
                    "PayBillb" => [
                        [
                            "quickType_code" => $data['type'],
                            "oriSum" => $data['money'],
                            "natSum" => $data['money'],
                            "_status" => "Insert",
                        ]
                    ]
                ]

            ]
        ];

        return $this->request('POST', '/fi/payment/save', $options);
    }

    /**
     * 付款单修改
     * @param array $data
     * @return array
     */
    public function updatePayment($data)
    {
        $result = [
            'code' => -1,
            'message' => '连接失败',
            'time' => time(),
            'data' => []
        ];
        $yonId = explode(',', $data['yon_id']);
        if (count($yonId) < 2) {
            $result['message'] = "id参数错误";
            return $result;
        }
        $supplier = $this->searchSupplier($data['user']['code']);
        if (isset($supplier['data']['recordList'][0]['code']) && $supplier['data']['recordList'][0]['code'] == $this->supplierCode($data['user']['code'])) {
            $data['supplier'] = $supplier['data']['recordList'][0]['id'];
        } else {
            $res = $this->setSupplier($data['user']);
            if (isset($res['data']['id'])) {
                $data['supplier'] = $res['data']['id'];
            } else {
                $result['message'] = isset($res['message']) ? $res['message'] : "用户插入失败";
                return $result;
            }
        }


        $options = [
            'json' => [
                'data' => [
                    'id' => $yonId[0],
                    'vouchdate' => date('Y-m-d H:i:s'),
                    'accentity_code' => $data['org'],
                    'exchangeRateType_code' => "01",
                    'oriSum' => $data['money'],
                    'natSum' => $data['money'],
                    'supplier' => $data['supplier'],
                    'description' => $data['extra'],
                    "exchRate" => 1,
                    "currency" => "2088893397850624",
                    "currency_name" => "人民币",
                    "currency_priceDigit" => "2",
                    "currency_moneyDigit" => "2",
                    "exchangeRateType" => "ghdglz4y",
                    "exchangeRateType_digit" => "6",
                    "tradetype" => "2088893315207425",
                    "_status" => "Update",
                    "PayBillb" => [
                        [
                            'id' => $yonId[1],
                            "quickType_code" => $data['type'],
                            "oriSum" => $data['money'],
                            "natSum" => $data['money'],
                            "_status" => "Update",
                        ]
                    ]
                ]

            ]
        ];

        return $this->request('POST', '/fi/payment/save', $options);
    }

    /**
     * 收款单保存
     * @param array $data
     * @return array
     */
    public function makeReceipt($data)
    {
        $result = [
            'code' => -1,
            'message' => '连接失败',
            'time' => time(),
            'data' => []
        ];
        $user = $this->searchUser($data['user']['code']);
        if (isset($user['data']['recordList'][0]['code']) && $user['data']['recordList'][0]['code'] == $this->userCode($data['user']['code'])) {
            $data['customer'] = $user['data']['recordList'][0]['id'];
        } else {
            $res = $this->setUser($data['user']);
            if (isset($res['data']['id'])) {
                $data['customer'] = $res['data']['id'];
            } else {
                $result['message'] = isset($res['message']) ? $res['message'] : "用户插入失败";
                return $result;
            }
        }

        $options = [
            'json' => [
                'data' => [
                    'customer' => $data['customer'],
                    "customer_code" => $this->userCode($data['user']['code']),
                    'code' => $data['code'],
                    'accentity_code' => $data['org'],
                    'vouchdate' => date('Y-m-d H:i:s'),
                    "tradetype" => "2088893315207431",
                    "exchangeRateType" => "ghdglz4y",
                    'exchangeRateType_code' => "01",
                    "exchRate" => 1,
                    'oriSum' => $data['money'],
                    'natSum' => $data['money'],
                    "_status" => "Insert",
                    "currency" => "2088893397850624",
                    "currency_name" => "人民币",
                    "currency_priceDigit" => "2",
                    "currency_moneyDigit" => "2",
                    "ReceiveBill_b" => [
                        [
                            "quickType" => "2088655373196158",
                            "oriSum" => $data['money'],
                            "natSum" => $data['money'],
                            "_status" => "Insert",
                        ]
                    ]
                ]

            ]
        ];

        if (isset($data['extra'])) {
            $options['json']['data']['description'] = $data['extra'];
        }

        return $this->request('POST', '/fi/receivebill/save', $options);
    }

    /**
     * 收款单修改
     * @param array $data
     * @return array
     */
    public function updateReceipt($data)
    {
        $result = [
            'code' => -1,
            'message' => '连接失败',
            'time' => time(),
            'data' => []
        ];
        $yonId = explode(',', $data['yon_id']);
        if (count($yonId) < 2) {
            $result['message'] = "id参数错误";
            return $result;
        }
        $user = $this->searchUser($data['user']['code']);
        if (isset($user['data']['recordList'][0]['code']) && $user['data']['recordList'][0]['code'] == $this->userCode($data['user']['code'])) {
            $data['customer'] = $user['data']['recordList'][0]['id'];
        } else {
            $res = $this->setUser($data['user']);
            if (isset($res['data']['id'])) {
                $data['customer'] = $res['data']['id'];
            } else {
                $result['message'] = isset($res['message']) ? $res['message'] : "用户插入失败";
                return $result;
            }
        }


        $options = [
            'json' => [
                'data' => [
                    'id' => $yonId[0],
                    'customer' => $data['customer'],
                    "customer_code" => $this->userCode($data['user']['code']),
                    'accentity_code' => $data['org'],
                    'vouchdate' => date('Y-m-d H:i:s'),
                    "tradetype" => "2088893315207431",
                    "exchangeRateType" => "ghdglz4y",
                    'exchangeRateType_code' => "01",
                    "exchRate" => 1,
                    'oriSum' => $data['money'],
                    'natSum' => $data['money'],
                    "_status" => "Update",
                    "currency" => "2088893397850624",
                    "currency_name" => "人民币",
                    "currency_priceDigit" => "2",
                    "currency_moneyDigit" => "2",
                    "ReceiveBill_b" => [
                        [
                            "id" => $yonId[1],
                            "quickType" => "2088655373196158",
                            "oriSum" => $data['money'],
                            "natSum" => $data['money'],
                            "_status" => "Update",
                        ]
                    ]
                ]

            ]
        ];

        if (isset($data['extra'])) {
            $options['json']['data']['description'] = $data['extra'];
        }

        return $this->request('POST', '/fi/receivebill/save', $options);
    }

    /**
     * 收款单退款
     * @param array $data
     * @return array
     */
    public function returnReceipt($data)
    {
        $result = [
            'code' => -1,
            'message' => '连接失败',
            'time' => time(),
            'data' => []
        ];
        $user = $this->searchUser($data['user']['code']);
        if (isset($user['data']['recordList'][0]['code']) && $user['data']['recordList'][0]['code'] == $this->userCode($data['user']['code'])) {
            $data['customer'] = $user['data']['recordList'][0]['id'];
        } else {
            $res = $this->setUser($data['user']);
            if (isset($res['data']['id'])) {
                $data['customer'] = $res['data']['id'];
            } else {
                $result['message'] = isset($res['message']) ? $res['message'] : "用户插入失败";
                return $result;
            }
        }

        $period = $this->getPeriod();
        if (!$period) {
            $result['message'] = "区间为空";
            return $result;
        }

        $org = $this->getOrg($data['org']);
        if (!$org) {
            $result['message'] = "公司代码为空";
            return $result;
        }

        $options = [
            'json' => [
                'data' => [
                    "busiaccbook" => 2102831439895552,
                    "exchangeRateType_code" => "01",
                    "vouchdate" => date('Y-m-d H:i:s'),
                    "code" => $data['code'],
                    "accentity" => $org,
                    //"accentity_code" => $data['org'],
                    //"accentity_name" => "禄可科技集团有限公司",
                    "oriSum" => $data['money'],
                    "natSum" => $data['money'],
                    //"balance" => 333,
                    "customer" => $data['customer'],
                    //"customer_code" => "U000951570",
                    //"customer_name" => "陈敏11(18659125240)",
                    "period" => $period,
                    //"period_code" => date('Y-m'),
                    "currency" => "2088893397850624",
                    //"currency_name" => "人民币",
                    "currency_priceDigit" => "2",
                    "currency_moneyDigit" => "2",
                    "natCurrency" => "2088893397850624",
                    //"natCurrency_name" => "人民币",
                    //"natCurrency_priceDigit" => "2",
                    //"natCurrency_moneyDigit" => "2",
                    "exchangeRateType" => "ghdglz4y",
                    "exchangeRateType_name" => "基准汇率",
                    "exchangeRateType_digit" => "6",
                    "exchRate" => 1,
                    "status" => 0,
                    "tradetype" => "2088893315207425",
                    //"tradetype_name" => "其他付款",
                    //"tradetype_code" => "arap_payment_other",
                    "srcitem" => 6,
                    "billtype" => 9,
                    "basebilltype_name" => "付款单",
                    "caobject" => 1,
                    "basebilltype" => "FICA2",
                    "initflag" => false,
                    "PayBillb" => [
                        [
                            "quickType" => 2088655373196158,
                            //"quickType_name" => "应收款",
                            //"quickType_code" => "2",
                            "_status" => "Insert",
                            "bookAmount" => 0,
                            //"balance" => 333,
                            "localbalance" => $data['money'],
                            "natSum" => $data['money'],
                            "oriSum" => $data['money'],
                            "_tableDisplayOutlineAll" => false,
                            "customer" => $data['customer'],
                            //"customer_name" => "陈敏11(18659125240)"
                        ]
                    ],
                    "_status" => "Insert"
                ]

            ]
        ];

        if (isset($data['extra'])) {
            $options['json']['data']['description'] = $data['extra'];
        }

        return $this->request('POST', '/fi/paybill/save', $options);
    }

    /**
     * 收款单退款修改
     * @param array $data
     * @return array
     */
    public function updateReturnReceipt($data)
    {
        $result = [
            'code' => -1,
            'message' => '连接失败',
            'time' => time(),
            'data' => []
        ];
        $yonId = explode(',', $data['yon_id']);
        if (count($yonId) < 2) {
            $result['message'] = "id参数错误";
            return $result;
        }
        $user = $this->searchUser($data['user']['code']);
        if (isset($user['data']['recordList'][0]['code']) && $user['data']['recordList'][0]['code'] == $this->userCode($data['user']['code'])) {
            $data['customer'] = $user['data']['recordList'][0]['id'];
        } else {
            $res = $this->setUser($data['user']);
            if (isset($res['data']['id'])) {
                $data['customer'] = $res['data']['id'];
            } else {
                $result['message'] = isset($res['message']) ? $res['message'] : "用户插入失败";
                return $result;
            }
        }

        $period = $this->getPeriod();
        if (!$period) {
            $result['message'] = "区间为空";
            return $result;
        }

        $org = $this->getOrg($data['org']);
        if (!$org) {
            $result['message'] = "公司代码为空";
            return $result;
        }

        $options = [
            'json' => [
                'data' => [
                    "id" => $yonId[0],
                    "code" => '12121212',
                    "busiaccbook" => 2102831439895552,
                    "exchangeRateType_code" => "01",
                    "vouchdate" => date('Y-m-d H:i:s'),
                    "accentity" => $org,
                    "oriSum" => $data['money'],
                    "natSum" => $data['money'],
                    "customer" => $data['customer'],
                    "period" => $period,
                    "currency" => "2088893397850624",
                    "currency_priceDigit" => "2",
                    "currency_moneyDigit" => "2",
                    "natCurrency" => "2088893397850624",
                    "exchangeRateType" => "ghdglz4y",
                    "exchangeRateType_name" => "基准汇率",
                    "exchangeRateType_digit" => "6",
                    "exchRate" => 1,
                    "status" => 0,
                    "tradetype" => "2088893315207425",
                    "srcitem" => 6,
                    "billtype" => 9,
                    "basebilltype_name" => "付款单",
                    "caobject" => 1,
                    "basebilltype" => "FICA2",
                    "initflag" => false,
                    "PayBillb" => [
                        [
                            "id" => $yonId[1],
                            "quickType" => 2088655373196158,
                            "_status" => "Update",
                            "bookAmount" => 0,
                            "localbalance" => $data['money'],
                            "natSum" => $data['money'],
                            "oriSum" => $data['money'],
                            "_tableDisplayOutlineAll" => false,
                            "customer" => $data['customer'],
                        ]
                    ],
                    "_status" => "Update"
                ]

            ]
        ];

        if (isset($data['extra'])) {
            $options['json']['data']['description'] = $data['extra'];
        }

        return $this->request('POST', '/fi/paybill/save', $options);
    }

    //获取区间查询
    public function getPeriod()
    {
       $all = $this->request('POST', '/fi/fipub/basedoc/querybd/accperiod');
       if (isset($all['data'])) {
           foreach ($all['data'] as $v) {
               if ($v['code'] == date('Y-m')) {
                   return $v['id'];
               }
           }
       }

       return false;
    }


    //设置供应商code
    private function supplierCode($code)
    {
        return 'S' . str_pad($code, 9, "0", STR_PAD_LEFT);
    }

    //设置用户code
    private function userCode($code)
    {
        return 'U' . str_pad($code, 9, "0", STR_PAD_LEFT);
    }

}