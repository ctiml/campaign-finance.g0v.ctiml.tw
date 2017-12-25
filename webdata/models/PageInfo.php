<?php

class PageInfo extends Pix_Table
{
    public function init()
    {
        $this->_name = 'page_info';
        $this->_primary = array('id');

        $this->_columns['id'] = array('type' => 'int', 'auto_increment' => true);
        $this->_columns['row_count'] = array('type' => 'int');
    }

    public function addPage($id)
    {
        if (PageInfo::find($id)) {
            return;
        }
        $url = 'http://' . getenv('CAMPAIGN_FINANCE_RONNY') . '/api/tables/' . intval($id);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $ret = curl_exec($curl);

        if (!$json = json_decode($ret) or !$json->data->tables)  {
            throw new Exception("找不到這個 Table");
        }

        $page_info = PageInfo::insert(array(
            'id' => $id,
            'row_count' => count($json->data->tables),
        ));

        PagePromotion::insert(array(
            'id' => $id,
            'page' => $id,
        ));

        $insert = array();
        foreach (array(2, 3, 4, 5, 6, 7, 8, 9) as $column) {
            for ($row = 2; $row <= count($json->data->tables); $row ++) {
                $insert[] = array($id, $row, $column, 0);
            }
        }
        Cell::bulkInsert(array('page', 'x', 'y', 'count'), $insert);
    }
}
