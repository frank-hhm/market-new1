<?php
/**
 * @Date: 2025/7/6 20:52
 */
declare(strict_types=1);

namespace app\common\library\check;

use app\common\library\check\HeaderUserAgentRole;
use foroco\BrowserDetection;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Jenssegers\Agent\Agent;

class HeaderUserAgentLibrary extends BaseLibrary
{
    private array $info = [];
    protected array $httpHeaders = [];
    /**
     * CloudFront标头。例如，CloudFront是桌面查看器、CloudFront是移动查看器和CloudFront是平板查看器。
     */
    protected array $cloudfrontHeaders = [];

    /**
     * 已解析匹配项的缓存
     * @var array
     */
    protected array $cache = [];

    protected mixed $userAgent = "";

    protected mixed $matchesArray = null;

    protected mixed $matchingRegex = null;



    /**
     * 用于提取版本号的常用正则表达式。
     */
    const VER                       = '([\w._\+]+)';
    const VERSION                   = '2.8.45';
    const VERSION_TYPE_STRING       = 'text';

    const VERSION_TYPE_FLOAT        = 'float';

    protected array $botMatches = [];

    /**
     * 移动检测类型。
     */
    const DETECTION_TYPE_MOBILE     = 'mobile';
    /**
     * 扩展检测类型。
     */
    const DETECTION_TYPE_EXTENDED   = 'extended';
    /**
     * 检测类型，使用self:：detection_type_MOBILE或self:：detection_type_EXTENDD。
     */
    protected string $detectionType = self::DETECTION_TYPE_MOBILE;

    public function __construct($header = "")
    {
        if(!empty($header)){
            $this->httpHeaders = json_decode($header,true);
        }else{
            $this->httpHeaders = $_SERVER;
        }

    }


    public function getInfo($userAgent){
//        $testServer = '{"cf-ipcountry":"SG","cdn-loop":"cloudflare","cf-connecting-ip":"206.238.40.253","accept":"*\/*","user-agent":"python-requests\/2.25.1","cf-visitor":"{\"scheme\":\"https\"}","x-forwarded-proto":"https","cf-ray":"8bc3c656fcd9858b-HKG","x-forwarded-for":"206.238.40.253","accept-encoding":"gzip, br","connection":"Keep-Alive","host":"web-e.fskbullion.com","content-length":"","content-type":""}';
//        $testServer = json_decode($testServer,true);
//        $userAgent =  $_SERVER['HTTP_USER_AGENT'] ?? "";
//        $userAgent =  $testServer['HTTP_USER_AGENT'] ?? $testServer["user-agent"] ?? "";
        $this->userAgent = $userAgent;
        $agent = new Agent();
        $agent->setUserAgent($userAgent);
        if($this->isBot()){
            $this->info['is_bot'] = $this->isBot();
            $this->info['bot_name'] = $this->getBotName();
        }elseif(empty($userAgent)){
            $this->info['is_bot'] = true;
            $this->info['bot_name'] = "curl";
        }else{
            $this->info['languages'] = $this->getLanguages();
            $this->info['platform'] = $this->getPlatformVersion();
            $this->info['device'] = $this->getDeviceVersion();
            $this->info['browser'] = $this->getBrowserVersion();

            $BrowserService = new BrowserDetection();
            $BrowserRes = $BrowserService->getBrowser($userAgent);
            if(!empty($BrowserRes['browser_name'])){
                $this->info['browser'] = [
                    "name"=>$BrowserRes['browser_name'] ?? '',
                    "version"=>$BrowserRes['browser_version'] ?? 0
                ];
            }else{
                $this->info['browser'] = $this->getBrowserVersion();
            }
        }
        $this->info['type'] = $this->getType();

        return $this->info;
    }


    public function getType(): array
    {
        if($this->isDesktop()){
            return [
                "name"=>"电脑",
                "value"=>"pc"
            ];
        }elseif ($this->isPhone()){
            return [
                "name"=>"手机",
                "value"=>"mobile"
            ];
        }elseif ($this->isTablet()){
            return [
                "name"=>"平板",
                "value"=>"tablet"
            ];
        }elseif ($this->isBot()){
            return [
                "name"=>"爬虫/工具/机器人",
                "value"=>"bot"
            ];
        }else{
            return [
                "name"=>"未知",
                "value"=>"unknown"
            ];
        }
    }

    public function getBrowser()
    {
        return $this->findDetectionRulesAgainstUA($this->getBrowsers());
    }
    public function getPlatform()
    {
        return $this->findDetectionRulesAgainstUA($this->getPlatforms());
    }


    /**
     * 获取设备名称。
     */
    public function getDevice()
    {
        if (preg_match('/\b(SM|GT|SGH|SCH)\-[A-Z0-9]+\b/i', $this->userAgent, $regs)) {
            $deviceModel = $regs[0];
            // 尝试识别具体的设备品牌
            if (strpos($deviceModel, 'SM-') === 0 || strpos($deviceModel, 'GT-') === 0 || strpos($deviceModel, 'SGH-') === 0 || strpos($deviceModel, 'SCH-') === 0) {
                $mobileBrand = '三星';
            }
        } elseif (preg_match('/(Samsung.*?).*?Build/i', $this->userAgent)) {
            $mobileBrand = '三星';
        }

        if(!empty($mobileBrand)){
            return $mobileBrand;
        }

        $rules = $this->mergeRules(
            $this->getDesktopDevices(),
            $this->getPhoneDevices(),
            $this->getTabletDevices(),
            $this->getUtilities()
        );

        return $this->findDetectionRulesAgainstUA($rules);
    }


    public function getBrowserVersion(): array
    {
        $browser = $this->getBrowser();
        $version = $this->version($browser);
        return [
            "name"=>$browser,
            "version"=>$this->setVersionDefault($version),
        ];
    }

    public function getPlatformVersion(): array
    {
        $platform = $this->getPlatform();
        $version = $this->version($platform);
        return [
            "name"=>$platform,
            "version"=>$this->setVersionDefault($version),
        ];
    }

    public function getDeviceVersion(): array
    {
        $device = $this->getDevice();
        $version = $this->version($device);

        if (preg_match('/\b(SM|GT|SGH|SCH)\-[A-Z0-9]+\b/i', $this->userAgent, $regs)) {
            $deviceModel = $regs[0];
            if (strpos($deviceModel, 'SM-') === 0 || strpos($deviceModel, 'GT-') === 0 || strpos($deviceModel, 'SGH-') === 0 || strpos($deviceModel, 'SCH-') === 0) {
                $version = $deviceModel;
            }
        }
        return [
            "name"=>$device,
            "version"=>$this->setVersionDefault($version),
        ];
    }

    public function setVersionDefault($version): string
    {
        if(empty($version)){
            return "0";
        }elseif(is_numeric($version)){
            return $version;
        }
        return str_replace('_', '.', $version);
    }


    public function getBrowsers(): array
    {
        return $this->mergeRules(
            HeaderUserAgentRole::BROWSERS,
            HeaderUserAgentRole::OTHER_BROWSERS
        );
    }

    public function getPlatforms(): array
    {
        return $this->mergeRules(
            HeaderUserAgentRole::SYSTEMS,
            HeaderUserAgentRole::OTHER_SYSTEMS
        );
    }



    public function getDesktopDevices(): array
    {
        return HeaderUserAgentRole::DESKTOP_DEVICES;
    }

    /**
     * 检索已知电话设备的列表。
     */
    public function getPhoneDevices(): array
    {
        return HeaderUserAgentRole::PHONE_DEVICES;
    }

    /**
     * 检索已知平板设备的列表。
     */
    public function getTabletDevices(): array
    {
        return HeaderUserAgentRole::TABLET_DEVICES;
    }

    /**
     * 检索已知实用程序的列表。
     */
    public function getUtilities(): array
    {
        return HeaderUserAgentRole::UTILITIES;
    }

    protected function findDetectionRulesAgainstUA(array $rules)
    {
        if($this->isBot()){
            return ucfirst($this->getBotMatches());
        }
        $userAgent = $this->userAgent;
        // Loop given rules
        foreach ($rules as $key => $regex) {
            if (empty($regex)) {
                continue;
            }

            // Check match
            if ($this->match($regex, $userAgent)) {
                return $key ?: reset($this->matchesArray);
            }
        }
        return false;
    }

    public function match($regex, $userAgent = null): bool
    {
        if (!\is_string($userAgent) && !\is_string($this->userAgent)) {
            return false;
        }

        $match = (bool) preg_match(sprintf('#%s#is', $regex), (false === empty($userAgent) ? $userAgent : (is_string($this->userAgent) ? $this->userAgent : '')), $matches);

        if ($match) {
            $this->matchingRegex = $regex;
            $this->matchesArray = $matches;
        }
        return $match;
    }

    public function getLanguages($acceptLanguage = null): array
    {
        if (empty($acceptLanguage)) {
            $acceptLanguage = $this->getHttpHeader('HTTP_ACCEPT_LANGUAGE');
        }

        if (!$acceptLanguage) {
            return [];
        }
        $languages = [];
        foreach (explode(',', $acceptLanguage) as $piece) {
            $parts = explode(';', $piece);
            $language = strtolower($parts[0]);
            $priority = empty($parts[1]) ? 1. : floatval(str_replace('q=', '', $parts[1]));
            $languages[$language] = $priority;
        }
        arsort($languages);

        return array_keys($languages);
    }

    public function getHttpHeader($header)
    {
        if (!str_contains($header, '_')) {
            $header = str_replace('-', '_', $header);
            $header = strtoupper($header);
        }

        $altHeader = 'HTTP_' . $header;

        if (isset($this->httpHeaders[$header])) {
            return $this->httpHeaders[$header];
        } elseif (isset($this->httpHeaders[$altHeader])) {
            return $this->httpHeaders[$altHeader];
        }
        return null;
    }
    protected function mergeRules(...$all): array
    {
        $merged = [];

        foreach ($all as $rules) {
            foreach ($rules as $key => $value) {
                if (empty($merged[$key])) {
                    $merged[$key] = $value;
                } elseif (is_array($merged[$key])) {
                    $merged[$key][] = $value;
                } else {
                    $merged[$key] .= '|' . $value;
                }
            }
        }
        return $merged;
    }


    public function version($propertyName, $type = self::VERSION_TYPE_STRING): float|bool|string
    {
        if (empty($propertyName)) {
            return false;
        }

        if ($type !== self::VERSION_TYPE_STRING && $type !== self::VERSION_TYPE_FLOAT) {
            $type = self::VERSION_TYPE_STRING;
        }

        $properties = $this->getProperties();
        if (true === isset($properties[$propertyName])) {
            $properties[$propertyName] = (array) $properties[$propertyName];
//            dump($propertyName,$properties[$propertyName]);
            foreach ($properties[$propertyName] as $propertyMatchString) {
                if (is_array($propertyMatchString)) {
                    $propertyMatchString = implode("|", $propertyMatchString);
                }
                $propertyPattern = str_replace('[VER]', self::VER, $propertyMatchString);

                preg_match(sprintf('#%s#is', $propertyPattern), $this->userAgent, $match);

                if (false === empty($match[1])) {
                    return ($type === self::VERSION_TYPE_FLOAT ? $this->prepareVersionNo($match[1]) : $match[1]);
                }
            }
        }
        return false;
    }


    public function getProperties(): array
    {
        return HeaderUserAgentRole::PROPERTIES;
    }
    /**
     * 检索移动操作系统列表。
     */
    public function getOperatingSystems(): array
    {
        return HeaderUserAgentRole::SYSTEMS;
    }
    /**
     * 准备版本号。
     *
     */
    public function prepareVersionNo($ver): float
    {
        $ver = str_replace(array('_', ' ', '/'), '.', $ver);
        $arrVer = explode('.', $ver, 2);

        if (isset($arrVer[1])) {
            $arrVer[1] = @str_replace('.', '', $arrVer[1]);
        }

        return (float) implode('.', $arrVer);
    }


    public function compileRegex($patterns): string
    {
        return '('.implode('|', $patterns).')';
    }
    /**
     * 检查设备是否为机器人。
     */
    public function isBot(): bool
    {
        $compiledExclusions = $this->compileRegex(HeaderUserAgentRole::EXCLUSIONS);
        $agent = trim(preg_replace(
            "/{$compiledExclusions}/i",
            '',
            $this->userAgent
        ));

        if ($agent === '') {
            return false;
        }
        $compiledRegex = $this->compileRegex(HeaderUserAgentRole::CRAWLERS);
        return (bool) preg_match("/{$compiledRegex}/i", $agent, $this->botMatches);
    }

    /**
     * Get the robot name.
     */
    public function getBotName()
    {
        if ($this->isBot()) {
            return $this->botMatches[0] ?? null;
        }

        return false;
    }
    public function getBotMatches()
    {
        return $this->botMatches[0] ?? null;
    }



    /**
     * 检查设备是否为手机。
     */
    public function isPhone($userAgent = null, $httpHeaders = null): bool
    {
        return $this->isMobile($userAgent, $httpHeaders) && !$this->isTablet($userAgent);
    }

    /**
     * 检查设备是否为台式计算机。
     */
    public function isDesktop($userAgent = null, $httpHeaders = null): bool
    {
        return !$this->isMobile($userAgent,$httpHeaders) && !$this->isTablet($userAgent,$userAgent) && !$this->isBot();
    }
    /**
     * 检查设备是否为平板电脑。
     * 如果检测到任何类型的平板电脑设备，则返回true。
     */
    public function isTablet($userAgent = null, $httpHeaders = null): bool
    {
        if ($this->getUserAgent() === 'Amazon CloudFront') {
            $cfHeaders = $this->getCfHeaders();
            if(array_key_exists('HTTP_CLOUDFRONT_IS_TABLET_VIEWER', $cfHeaders) && $cfHeaders['HTTP_CLOUDFRONT_IS_TABLET_VIEWER'] === 'true') {
                return true;
            }
        }
        $this->setDetectionType(self::DETECTION_TYPE_MOBILE);

        foreach ($this->getTabletDevices() as $_regex) {
            if ($this->match($_regex, $userAgent)) {
                return true;
            }
        }
        return false;
    }



    /**
     * 检查设备是否为移动设备。
     * 如果检测到任何类型的移动设备，包括特殊设备，则返回true
     */
    public function isMobile($userAgent = null, $httpHeaders = null): bool
    {

        if ($httpHeaders) {
            $this->setHttpHeaders($httpHeaders);
        }

        if ($userAgent) {
            $this->setUserAgent($userAgent);
        }

        if ($this->getUserAgent() === 'Amazon CloudFront') {
            $cfHeaders = $this->getCfHeaders();
            if(array_key_exists('HTTP_CLOUDFRONT_IS_MOBILE_VIEWER', $cfHeaders) && $cfHeaders['HTTP_CLOUDFRONT_IS_MOBILE_VIEWER'] === 'true') {
                return true;
            }
        }

        $this->setDetectionType(self::DETECTION_TYPE_MOBILE);

        if ($this->checkHttpHeadersForMobile()) {
            return true;
        } else {
            return $this->matchDetectionRulesAgainstUA();
        }

    }


    /**
     * 查找与当前用户代理匹配的检测规则。
     */
    protected function matchDetectionRulesAgainstUA($userAgent = null): bool
    {
//        dump($this->getRules() );die;
        // 开始一般搜索。
        foreach ($this->getRules() as $_regex) {
            if (empty($_regex)) {
                continue;
            }

            if ($this->match($_regex, $userAgent)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 检索当前规则集。
     */
    public function getRules()
    {
//        dump(self::DETECTION_TYPE_EXTENDED );die;
        if ($this->detectionType === self::DETECTION_TYPE_EXTENDED) {
            return $this->getDetectionRulesExtended();
        }

        return $this->getMobileDetectionRules();
    }


    /**
     *获取所有检测规则。这些规则包括
     */
    public function getDetectionRulesExtended()
    {
        static $rules;

        if (!$rules) {
            $rules = $this->mergeRules(
                HeaderUserAgentRole::DESKTOP_DEVICES,
                HeaderUserAgentRole::PHONE_DEVICES,
                HeaderUserAgentRole::TABLET_DEVICES,
                HeaderUserAgentRole::SYSTEMS,
                HeaderUserAgentRole::SYSTEMS,
                HeaderUserAgentRole::OTHER_SYSTEMS,
                HeaderUserAgentRole::BROWSERS,
                HeaderUserAgentRole::OTHER_BROWSERS,
                HeaderUserAgentRole::UTILITIES
            );
        }

        return $rules;
    }
    public function getMobileDetectionRulesExtended()
    {
        static $rules;

        if (!$rules) {
            $rules = array_merge(
                $this->getPhoneDevices(),
                $this->getTabletDevices(),
                $this->getOperatingSystems(),
                $this->getBrowsers(),
                $this->getUtilities()
            );
        }

        return $rules;
    }


    /**
     * 方法获取移动检测规则。此方法用于魔术方法 $detect->is*().
     */
    public  function getMobileDetectionRules()
    {
        static $rules;

        if (!$rules) {
            $rules = array_merge(
                HeaderUserAgentRole::PHONE_DEVICES,
                HeaderUserAgentRole::TABLET_DEVICES,
                HeaderUserAgentRole::SYSTEMS,
                HeaderUserAgentRole::BROWSERS
            );
        }
        return $rules;
    }
    /**
     * 设置检测类型。必须是self:：DETECTION_TYPE_MOBILE或
     * self:：检测类型_输出。否则，什么都不会设置。
     */
    public function setDetectionType($type = null): void
    {
        if ($type === null) {
            $type = self::DETECTION_TYPE_MOBILE;
        }

        if ($type !== self::DETECTION_TYPE_MOBILE && $type !== self::DETECTION_TYPE_EXTENDED) {
            return;
        }

        $this->detectionType = $type;
    }


    /**
     * 检索用户代理
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    public function getMobileHeaders(): array
    {
        return HeaderUserAgentRole::MOBIlEHEADERS;
    }

    /**
     * 检索cloudfront标头。
     */
    public function getCfHeaders(): array
    {
        return $this->cloudfrontHeaders;
    }

    /**
     * 检查HTTP标头是否有移动设备的迹象。
     * 这是最快的移动支票；它被使用了
     * isMobile（）方法内部。
     */
    public function checkHttpHeadersForMobile(): bool
    {
        foreach ($this->getMobileHeaders() as $mobileHeader => $matchType) {
            if (isset($this->httpHeaders[$mobileHeader])) {
                if (isset($matchType['matches']) && is_array($matchType['matches'])) {
                    foreach ($matchType['matches'] as $_match) {
                        if (str_contains($this->httpHeaders[$mobileHeader], $_match)) {
                            return true;
                        }
                    }

                    return false;
                } else {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * 设置HTTP标头。必须是PHP风格的。此方法将重置现有标头。
     */
    public function setHttpHeaders($httpHeaders = null): void
    {
        if (!is_array($httpHeaders) || !count($httpHeaders)) {
            $httpHeaders = $_SERVER;
        }

        $this->httpHeaders = array();

        // 仅保存HTTP标头。在PHP语言中，这意味着只有_SERVER变量
        // start with HTTP_.
        foreach ($httpHeaders as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $this->httpHeaders[$key] = $value;
            }
        }

        // 如果我们正在处理CloudFront，我们需要知道。
        $this->setCfHeaders($httpHeaders);
    }

    /**
     * Set the User-Agent to be used.
     */
    public function setUserAgent($userAgent = null): ?string
    {
        $this->cache = array();

        if (false === empty($userAgent)) {
            return $this->userAgent = $this->prepareUserAgent($userAgent);
        } else {
            $this->userAgent = null;
            foreach ($this->getUaHttpHeaders() as $altHeader) {
                if (false === empty($this->httpHeaders[$altHeader])) {
                    $this->userAgent .= $this->httpHeaders[$altHeader] . " ";
                }
            }

            if (!empty($this->userAgent)) {
                return $this->userAgent = $this->prepareUserAgent($this->userAgent);
            }
        }

        if (count($this->getCfHeaders()) > 0) {
            return $this->userAgent = 'Amazon CloudFront';
        }
        return $this->userAgent = null;
    }

    private function prepareUserAgent($userAgent): string
    {
        $userAgent = trim($userAgent);
        return substr($userAgent, 0, 500);
    }

    /**
     */
    public function getUaHttpHeaders(): array
    {
        return HeaderUserAgentRole::UA_HTTP_HEADERS;
    }


    /**
     * 设置CloudFront标头
     */
    public function setCfHeaders($cfHeaders = null): bool
    {
        if (!is_array($cfHeaders) || !count($cfHeaders)) {
            $cfHeaders = $_SERVER;
        }

        $this->cloudfrontHeaders = array();
        $response = false;
        foreach ($cfHeaders as $key => $value) {
            if (str_starts_with(strtolower($key), 'http_cloudfront_')) {
                $this->cloudfrontHeaders[strtoupper($key)] = $value;
                $response = true;
            }
        }

        return $response;
    }
}