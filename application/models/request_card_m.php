<?php
require_once(dirname(__FILE__) . '/../util/Distance_Util.php');

class Request_card_m extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add($paras)
    {
        try {
            $this->load->database();
            $this->db->insert('request_card', $paras);
            $this->db->close();
            return true;
        } catch (Exception $e) {
            $this->db->close();
            return false;
        }
    }

    /**
     * 先搜索出所有用户求卡记录, 再计算距离, 最后进行排序
     * @param $paras
     * @return bool
     */
    public function get($paras)
    {
        $select_sql = " SELECT open_id, shop_name, shop_location, shop_longitude, shop_latitude, time, discount,"
                    . " ware_type, trade_type, description, user_longitude, user_latitude, user_location"
                    . " FROM request_card";

        $results = array();

        try {
            $this->load->database();
            $query = $this->db->query($select_sql);
            $this->db->close();

            if ($query->num_rows() > 0){
                foreach($query->result_array() as $row){
                    $row['distance'] = Distance_Util::get_kilometers_between_points($paras['user_latitude'], $paras['user_longitude'],
                        $row['user_latitude'], $row['user_longitude']);
                    array_push($results, $row);
                }

                $this->rank_distance($results);
            }

            return $results;
        } catch (Exception $e) {
            $this->db->close();
            return false;
        }
    }

    /**
     * 根据距离排序
     * @param $request_cards
     */
    private function rank_distance(& $request_cards)
    {
        usort($request_cards, function($a, $b) {
            if ($a['distance'] == $b['distance'])   return 0;
            return ($a['distance'] < $b['distance']) ? -1 : 1;
        });
    }

    /**
     * 撤销get函数中一些不必要的信息
     * @param $row
     */
    private function unset_get(& $row)
    {

    }
}