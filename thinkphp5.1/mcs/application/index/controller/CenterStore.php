<?php
namespace app\index\controller;
class CenterStore extends \common\controller\Base{
    /**首页
     */
    public function index(){
        //获取商品的分类
        $modelGoodsCategory = new \app\index\model\GoodsCategory();
        $config =[
            'where' => [
                ['status', '=', 0],
                ['level','=',1]
            ], 'order'=>[
                'sort'=>'desc',
                'id'=>'desc'
            ],  'limit'=>'7'
        ];
        $categoryList  = $modelGoodsCategory->getList($config);
        $this ->assign('categoryList',$categoryList);
        //获取精选的10个 场景
        $modelScene = new \app\index\model\Scene();
        $config =[
            'where' => [
                ['status', '=', 0],
                ['shelf_status','=',3],
                ['is_selection','=',1],
            ], 'order'=>[
                'group'=>'desc',
                'id'=>'desc',
                'sort'=>'desc'
            ],  'limit'=>'11'

        ];

        // 这里
        $sceneList  = $modelScene->getList($config);
        $sceneLists = array();
        $i = 0;
        $j = 0;
/*        foreach($sceneList as $v){
            if($v['group']==3){
                if(count($sceneLists[3][$i])==3){
                    $i ++;
                }
                $sceneLists[3][$i][] = $v;
            }elseif($v['group']==2){
                if(count($sceneLists[2][$j])==2){
                    $j ++;
                }
                $sceneLists[2][$j][] = $v;
            }
        }*/


 /*       p($sceneLists);
        exit;*/
        $i = 0;
        foreach($sceneList as $k => $v){

            $j = count($sceneLists[$v['group']][$i]);

            if (count($sceneLists[$v['group']][$j])==$v['group']){
                $j++;
            }
            //$sceneLists[$v['group']][]= $v;
            $sceneLists[$v['group']][$j][] = $v;

        }


        p($sceneLists);die;
        exit;
        echo 0%3;
        echo 3%3;
        echo 6%3;
        exit;
 /*       for($i=0;$i<100;$i++){

            echo $i.','.$i%2;
            echo '<br/>';
        }
        //echo 4%3;
        exit;*/
        //p($sceneLists);exit;
        $this ->assign('sceneLists',$sceneLists);

        //获取精选的10个项目
        $modelProject = new \app\index\model\Project();
        $config =[
            'where' => [
                ['status', '=', 0],
                ['shelf_status','=',3],
                ['is_selection','=',1],
            ], 'order'=>[
                'sort'=>'desc',
                'id'=>'desc'
            ],  'limit'=>'11'
        ];
        $projectList  = $modelProject->getList($config);
        $this ->assign('projectList',$projectList);
        return $this->fetch();
    }
}