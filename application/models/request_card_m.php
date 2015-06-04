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
        $select_sql = " SELECT RC.open_id, RC.shop_name, RC.shop_location, RC.shop_longitude, RC.shop_latitude, RC.time AS publish_time, RC.discount,"
                    . " RC.ware_type, RC.trade_type, RC.description, RC.user_longitude, RC.user_latitude, RC.user_location,"
                    . " U.nickname, U.avatar, U.gender"
                    . " FROM request_card AS RC JOIN users AS U ON RC.open_id = U.open_id";

        $results = array();

        try {
            $this->load->database();
            $query = $this->db->query($select_sql);
            $this->db->close();

            if ($query->num_rows() > 0){

                foreach($query->result_array() as $row){
                    $row['distance'] = Distance_Util::get_kilometers_between_points($paras['user_latitude'], $paras['user_longitude'],
                        $row['user_latitude'], $row['user_longitude']); // 添加距离信息
                    $this->unset_get($row); // 去除不必要的返回信息
                    $row['distance'] = round($row['distance'], 1); // 距离保留1位小数
                    array_push($results, $row);
                }

                $this->rank_distance($results); // 根据距离进行排序
                $results = $this->page_get($results, $paras); // 获取分页结果
            }

            return $results;
        } catch (Exception $e) {
            $this->db->close();
            return false;
        }
    }

    /**
     * 根据距离将求卡记录进行排序
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
     * 撤销get函数中一些不需要返回的结果信息
     * @param $row
     */
    private function unset_get(& $row)
    {
        unset($row['open_id']);
        unset($row['user_longitude']);
        unset($row['user_latitude']);
    }

    /**
     * 获取分页结果
     * @param $request_cards
     * @param $paras
     * @return array
     */
    private function page_get($request_cards, $paras)
    {
        $offset = ($paras['page_num'] - 1) * $paras['page_size'];
        $length = $paras['page_size'];
        return array_slice($request_cards, $offset, $length);
    }
}