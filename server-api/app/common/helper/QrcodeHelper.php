<?php
/**
 * @Date: 2025/6/26 8:05
 */
declare(strict_types=1);
namespace app\common\helper;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

/**
 * 二维码
 * Class String
 * @package frank\utils\helper
 */
class QrcodeHelper
{
    private array $params = [];

    private string $qrcodePath = '/uploads/share_qrcode/';

    private string $url = "";

    /**
     * 获取小程序码
     */
    public function getImage($url,$params): string
    {
        $this->params = $params;
        $this->url = $url;
        // 判断二维码文件存在则直接返回url
        if (file_exists($this->getPosterPath())) {
            return $this->getPosterUrl();
        }
        //
        $qrcode = $this->saveQrcode($url,$params);
        return $this->savePoster($qrcode);
    }

    private function savePoster($qrcode): string
    {
        copy($qrcode, $this->getPosterPath());
        return $this->getPosterUrl();
    }

    /**
     * 二维码文件路径
     * @return string
     */
    private function getPosterPath(): string
    {
        // 保存路径
        $tempPath =app()->getRootPath() . '/public'.$this->qrcodePath;
        !is_dir($tempPath) && mkdir($tempPath, 0755, true);
        return $tempPath . $this->getPosterName();
    }

    /**
     * 二维码文件名称
     * @return string
     */
    private function getPosterName(): string
    {
        return 'qrcode'. md5($this->url . json_encode($this->params)) . '.png';
    }

    /**
     * 二维码url
     * @return string
     */
    private function getPosterUrl(): string
    {
        return $this->qrcodePath . $this->getPosterName() ;
    }

    /**
     * 保存小程序码到文件
     */
    protected function saveQrcode($url, $params = [],$path=false)
    {
        // 文件目录
        if(!$path){
            $dirPath =  runtime_path().'/'.$this->qrcodePath;
        }else{
            $dirPath =   app()->getRootPath() . 'public/'.$this->qrcodePath.$path;
        }
        !is_dir($dirPath) && mkdir($dirPath, 0755, true);
        $page = $url.'?'.http_build_query($params);
        // 文件名称
        $fileName = $this->getPosterName();
        // 文件路径
        $savePath = "{$dirPath}/{$fileName}";
        $fileUrl = !$path ? $savePath :  baseUrl().$this->qrcodePath.$path.'/'. $fileName ;
        if (file_exists($savePath)) return $fileUrl;

        $qrCode = QrCode::create($page);
        // 设置二维码的大小
        $qrCode->setSize(300);

        // 可选：设置二维码的颜色
        $qrCode->setForegroundColor(new Color(0, 0, 0)); // 黑色

        // 可选：设置背景颜色
        $qrCode->setBackgroundColor(new Color(255, 255, 255)); // 白色

        // 可选：设置边框宽度
        $qrCode->setMargin(10);

        // 可选：设置编码方式
        $qrCode->setEncoding(new Encoding('UTF-8'));

        // 可选：设置错误校正级别
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::High); // H 表示最高级别的错误校正

        // 可选：设置标签格式
        // 生成二维码并保存为文件
        $writer = new PngWriter();

        $res = $writer->write($qrCode);
        // 生成二维码并保存为文件
        $res->saveToFile($savePath);
//        $dataUri = $res->getDataUri();
        return $fileUrl;
    }

}