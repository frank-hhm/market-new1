<?php
/**
 * @Date: 2025/6/28 11:02
 */
declare(strict_types=1);
namespace app\common\services;

use app\common\dao\ArticleDao;
use app\common\exception\CommonException;
use app\common\helper\StringHelper;
use app\common\services\common\CacheService;
use think\facade\Db;
use think\facade\Filesystem;
/**
 * 文章
 * Class ArticleService
 */
class ArticleService extends BaseService
{

    /**
     * ArticleService constructor.
     * @param ArticleDao $dao
     */
    public function __construct(ArticleDao $dao)
    {
        $this->dao = $dao;
    }

    public function getDetail($filter)
    {
        $detail = $this->dao->detail($filter);
        if (!$detail) {
            throw new CommonException('文章不存在');
        }
        return $detail->toArray();
    }

    public function getList($params = [])
    {
        [$page, $limit] = $this->dao->getPageValue();
        $filter = [];
        if(!empty($params['time'][0])&&!empty($params['time'][1])){
            $filter[] = ['create_time','>=',strtotime($params['time'][0])];
            $filter[] = ['create_time','<=',strtotime($params['time'][1])+86400];
        }
        $list = $this->dao->model->where($filter)->order(['create_time DESC'])->page($page)->paginate($limit)->toArray();
        return $list;
    }
    /**
     * 批量新增
     */
    public function createArticles(array $data = []){
        // 保存数据
        Db::startTrans();
        try {
            $this->dao->model->insertAll($data);
            // 保存
            Db::commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage() ?? '';
            // 回滚事务
            Db::rollback();
            return false;
        }
    }
    public function getHomeSelect($params = [])
    {
        $list = $this->dao->model->where([
            'status'=>1
        ])->order(['create_time DESC'])->field('id,cover,title,create_time')->limit(5)->select()->toArray();
        return $list;
    }



    public function gather(){


        $apiUrl = "http://apis.juhe.cn/fapigx/caijing/query"; // 接口请求URL
        $method = "GET"; // 接口请求方式
        $headers = ["Content-Type: application/x-www-form-urlencoded"]; // 接口请求header
        $apiKey = "f97ed4b1b73b1635b76e261b0fe2a7e9"; // 在个人中心->我的数据,接口名称上方查看
        // 接口请求入参配置
        $requestParams = [
            'key' => $apiKey,
            'num'=> '30',
            'page'=> '0',
            'rand'=> '',
            'word'=> '',
        ];
        $requestParamsStr = http_build_query($requestParams);

        // 发起接口网络请求
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $apiUrl . '?' . $requestParamsStr);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        if (1 == strpos("$" . $apiUrl, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        $response = curl_exec($curl);
        $httpInfo = curl_getinfo($curl);
        curl_close($curl);
        // 解析响应结果
        $res = json_decode($response, true);
//        dump($res);die;
        if (isset($res['reason']) && $res['reason'] == 'success') {
            require_once root_path() . '/extend/simplehtmldom/simple_html_dom.php';
            $articles = [];
            // 在 <ul> 中查找所有 <li>
            $cacheService = app(CacheService::class);
            $cacheKey = "article:gather:ids:";
            $articleIds = [];
            $ids = $cacheService->get($cacheKey);
            $data = $res['result']['newslist'] ?? [];
            $articleData = [];
            $num = 0;
            foreach ($data as $item){
                $contentUrl = $item['url'];
                $items = [
                    'old_id'=>$item['id'] ?? "",
                    'title'=>$item['title'],
                    'title_desc'=>$item['description'] ?? "",
                    'cover'=>$item['picUrl'],
                    'create_time'=>strtotime($item['ctime']),
                    'status'=>1,
                ];
                if ($items[ 'old_id' ]){
                    if( !empty($ids) && in_array($items[ 'old_id' ],$ids)){
                        continue;
                    }
                }else{
                    continue;
                }
                try {

                    $content_html = file_get_html("https:".$contentUrl);
                    $linshi_html = $content_html-> innertext;
                    $linshi_html = str_replace('class="article"','id="Content"',$linshi_html);
                    $content_html = str_get_html ( $linshi_html );
                    $ruku = false;
                    if ($content_html->find('div[id=Content]', 0)){
                        $content = $content_html->find('div[id=Content]', 0)-> innertext;
                        $items['content'] = $content;
                        $ruku = true;
                    }

                }catch (\Exception $e){
                    continue;
                }

                //有内容即入库

                if ($ruku && !empty($items['content'])){
                    $articleIds[] = $items[ 'old_id' ];
                    $articles[] = $items;
                    $num++;
                }
            }

            $articleService = app(ArticleService::class);
            if($res = $articleService->createArticles($articles)){
                $res2 = $cacheService->set($cacheKey,!empty($ids) ? array_merge($ids,$articleIds):$articleIds,86400);
                return true;
            }
            // 网络请求成功。可依据业务逻辑和接口文档说明自行处理。

        } else {
            // 网络异常等因素，解析结果异常。可依据业务逻辑自行处理。
            // var_dump($httpInfo);
            throw new CommonException($res["reason"] ?? "未知错误");
        }
    }

}