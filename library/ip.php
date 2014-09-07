<?php
/**
 * <code>
 * $ipope = new Library_Ip();
 * $location = $ipope->getAddr($ip); //string 2.2.3.4
 * </code>
 * 修改自
 * https://github.com/itbdw/ip-database
 */

/**
 * 获取IP接口
 *
 * 采用直接读文件形式获取IP
 * qqwry.dat 文件为纯真网络的IP库文件
 * 升级非常方便，如果安装了纯真数据库，只需先对 ip.exe 进行升级，
 * 然后将目录下的 qqary.dat 文件复制过来覆盖掉旧的文件即可。
 *
 * filename:    ip.php
 * charset:     UTF-8
 * create date: 2012-08-25
 * update date: 2013-11-09
 *
 * @author    Zhao Binyan <itbudaoweng@gmail.com>
 * @copyright 2011-2013 Zhao Binyan
 * @link      http://yungbo.com
 * @link      http://weibo.com/itbudaoweng
 */

/**
 * IP 地理位置查询类
 *
 * 2013-11-10 赵彬言        1，优化，新增支持到市区，县城
 *                          2，返回结构增加 smallarea，具体请看注释
 *
 * 2012-10-21 赵彬言        1，增加市，县显示
 *                          2，去掉不靠谱的自动转码
 *                             先统一改为 GBK，最后再做转换解决编码问题
 *
 * 2012-08-15 赵彬言        1，更新为 PHP5 的规范
 *                          2，增加 wphp_ip2long 方法
 *                          3，增加 get_province 方法
 *                          4，增加 get_isp 方法
 *                          5，增加 is_valid_ip 方法
 * 此类基于 马秉尧 先生的 1.5 版本，在此感谢。目前您看到的这个文件是由赵彬言维护的。
 *
 * 采用直接读文件形式获取IP
 * qqwry.dat 文件为纯真网络的IP库文件
 * 升级非常方便，安装纯真ip库软件，先对 ip.exe 进行升级，
 * 然后将目录下的 qqary.dat 文件复制过来覆盖掉旧的文件即可。
 *
 * @author    马秉尧，赵彬言<itbudaoweng@gmail.com>
 * @version   2.0
 * @copyright 2005 CoolCode.CN，2012-1013 yungbo.com
 */
class Library_Ip {
    /**
     * qqwry.dat文件指针
     *
     * @var resource
     */
    private $fp;

    /**
     * 第一条IP记录的偏移地址
     *
     * @var int
     */
    private $firstip;

    /**
     * 最后一条IP记录的偏移地址
     *
     * @var int
     */
    private $lastip;

    /**
     * IP记录的总条数（不包含版本信息记录）
     *
     * @var int
     */
    private $totalip;

    /**
     * 输出的字符编码
     *
     * 默认是 UTF-8
     *
     * @var string
     */
    public $out_charset = 'UTF-8';

    /**
     * 运营商词典
     *
     * @var array
     */
    public $dict_isp = array(
        '联通', '移动', '铁通', '电信',
    );

    /**
     * 中国直辖市
     *
     * @var array
     */
    public $dict_city_directly = array(
        '北京', '天津', '重庆', '上海',
    );

    /**
     * 中国省份
     *
     * @var array
     */
    public $dict_province = array(
        '北京', '天津', '重庆', '上海',

        '河北', '山西', '辽宁', '吉林',
        '黑龙江', '江苏', '浙江', '安徽',
        '福建', '江西', '山东', '河南',
        '湖北', '湖南', '广东', '海南',
        '四川', '贵州', '云南', '陕西',
        '甘肃', '青海', '台湾',

        '内蒙古', '广西', '宁夏', '新疆', '西藏',

        '香港', '澳门',
    );

    /**
     * @param string $filename
     */
    public function __construct($filename = "qqwry.dat") {
        //只使用绝对路径
        $filename = ROOT . DS . 'library' . DS . $filename;
        $this->fp = 0;
        if (($this->fp = fopen($filename, 'rb')) !== false) {
            $this->firstip = $this->getlong();
            $this->lastip  = $this->getlong();
            $this->totalip = ($this->lastip - $this->firstip) / 7;
        } else {
            trigger_error($filename . ' not found!');
            exit();
        }

        foreach ($this->dict_isp as $key => $value) {
            $this->dict_isp[$key] = iconv('UTF-8', 'GBK', $value);
        }

        foreach ($this->dict_city_directly as $key => $value) {
            $this->dict_city_directly[$key] = iconv('UTF-8', 'GBK', $value);
        }

        foreach ($this->dict_province as $key => $value) {
            $this->dict_province[$key] = iconv('UTF-8', 'GBK', $value);
        }
    }

    /**
     * 如果ip错误，返回 $result['error'] 信息
     * 示例地点和本人无任何关系
     * <code>
     * $result 是返回的数组
     * $result['ip']            输入的ip
     * $result['beginip']       起始段
     * $result['endip']         结束段
     * $result['country']       国家 如 中国
     * $result['province']      省份信息 如 河北省
     * $result['city']          市区 如 邢台市
     * $result['county']        郡县 如 威县
     * $result['isp']           运营商 如 联通
     * $result['remark']        市区县信息 如 河北省邢台市威县
     * $result['smallarea']     小区信息 如 新科网吧(北外街)
     * $result['area']          最完整的信息 如 中国河北省邢台市威县新科网吧(北外街)
     *
     * </code>
     * @param $ip
     * @return array
     */
    public function getAddr($ip) {
        $result    = array();
        $is_china  = false;
        $gbk_sheng = iconv('UTF-8', 'GBK', '省');
        $gbk_shi   = iconv('UTF-8', 'GBK', '市');
        $gbk_xian  = iconv('UTF-8', 'GBK', '县');
        $gbk_qu    = iconv('UTF-8', 'GBK', '区');

        if (!$this->is_valid_ip($ip)) {
            $result['error'] = 'ip invalid';
        } else {
            $location = $this->getlocation($ip); // $location[country] [area]

            $location['remark']    = $location['country']; //北京市朝阳区
            $location['smallarea'] = $location['area']; // 金桥国际小区
            $location['province']  = $location['city'] = $location['county'] = '';

            $_tmp_province = explode($gbk_sheng, $location['country']);
            //存在 省 标志 xxx省yyyy 中的yyyy
            if (isset($_tmp_province[1])) {
                $is_china = true;
                //省
                $location['province'] = $_tmp_province[0] . $gbk_sheng; //河北省

                if (strpos($_tmp_province[1], $gbk_shi) !== false) {
                    $_tmp_city = explode($gbk_shi, $_tmp_province[1]);
                    //市
                    $location['city'] = $_tmp_city[0] . $gbk_shi;

                    //县
                    if (isset($_tmp_city[1])) {
                        if (strpos($_tmp_city[1], $gbk_xian) !== false) {
                            $_tmp_county        = explode($gbk_xian, $_tmp_city[1]);
                            $location['county'] = $_tmp_county[0] . $gbk_xian;
                        }

                        //区
                        if (!$location['county'] && strpos($_tmp_city[1], $gbk_qu) !== false) {
                            $_tmp_qu            = explode($gbk_qu, $_tmp_city[1]);
                            $location['county'] = $_tmp_qu[0] . $gbk_qu;
                        }
                    }
                }
            } else {
                //处理内蒙古不带省份类型的和直辖市
                foreach ($this->dict_province as $key => $value) {

                    if (false !== strpos($location['country'], $this->dict_province[$key])) {
                        $is_china = true;
                        //直辖市
                        if (in_array($value, $this->dict_city_directly)) {
                            $_tmp_province = explode($gbk_shi, $location['country']);
                            //直辖市
                            $location['province'] = $_tmp_province[0] . $gbk_shi;

                            //市辖区
                            if (isset($_tmp_province[1])) {
                                if (strpos($_tmp_province[1], $gbk_qu) !== false) {
                                    $_tmp_qu          = explode($gbk_qu, $_tmp_province[1]);
                                    $location['city'] = $_tmp_qu[0] . $gbk_qu;
                                }
                            }
                        } else {
                            //省
                            $location['province'] = $this->dict_province[$key];
                            $_tmp_city            = $location['country'];

                            //没有省份标志 只能替换
                            $_tmp_city = str_replace($location['province'], '', $_tmp_city);
                            $_tmp_city = ltrim($_tmp_city, $gbk_shi); //防止直辖市捣乱 上海市xxx区 =》 市xx区

                            //内蒙古 类型的 获取市县信息
                            if (strpos($_tmp_city, $gbk_shi) !== false) {
                                //市
                                $_tmp_city = explode($gbk_shi, $_tmp_city);

                                $location['city'] = $_tmp_city[0] . $gbk_shi;

                                //县
                                if (isset($_tmp_city[1])) {
                                    if (strpos($_tmp_city[1], $gbk_xian) !== false) {
                                        $_tmp_county        = explode($gbk_xian, $_tmp_city[1]);
                                        $location['county'] = $_tmp_county[0] . $gbk_xian;
                                    }

                                    //区
                                    if (!$location['county'] && strpos($_tmp_city[1], $gbk_qu) !== false) {
                                        $_tmp_qu            = explode($gbk_qu, $_tmp_city[1]);
                                        $location['county'] = $_tmp_qu[0] . $gbk_qu;
                                    }
                                }
                            }
                        }

                        break;
                    }
                }
            }

            if ($is_china) {
                $location['country'] = iconv('UTF-8', 'GBK', '中国');
            }

            $location['isp']     = $this->get_isp($location['area']);
            $result['ip']        = $location['ip'];
            $result['beginip']   = $location['beginip'];
            $result['endip']     = $location['endip'];
            $result['country']   = $location['country'];
            $result['province']  = $location['province'];
            $result['city']      = $location['city'];
            $result['county']    = $location['county'];
            $result['isp']       = $location['isp'];
            $result['remark']    = $location['remark'];
            $result['smallarea'] = $location['smallarea'];
            $result['area']      = $location['country'] . $location['province'] . $location['city'] . $location['county'] . $location['smallarea'];

            if ('GBK' != strtoupper($this->out_charset)) {
                foreach ($result as $key => $value) {
                    $result[$key] = iconv('GBK', $this->out_charset, $value);
                }
            }
        }
        return $result; //array
    }

    /**
     * 根据所给 IP 地址或域名返回所在地区信息
     *
     * @access public
     * @param string $ip
     * @return array
     */
    private function getlocation($ip) {
        if (!$this->fp) return null; // 如果数据文件没有被正确打开，则直接返回空

        $location['ip'] = $ip;

        $ip = $this->packip($location['ip']); // 将输入的IP地址转化为可比较的IP地址
        // 不合法的IP地址会被转化为255.255.255.255
        // 对分搜索
        $l      = 0; // 搜索的下边界
        $u      = $this->totalip; // 搜索的上边界
        $findip = $this->lastip; // 如果没有找到就返回最后一条IP记录（qqwry.dat的版本信息）
        while ($l <= $u) { // 当上边界小于下边界时，查找失败
            $i = floor(($l + $u) / 2); // 计算近似中间记录
            fseek($this->fp, $this->firstip + $i * 7);
            $beginip = strrev(fread($this->fp, 4)); // 获取中间记录的开始IP地址
            // strrev函数在这里的作用是将little-endian的压缩IP地址转化为big-endian的格式
            // 以便用于比较，后面相同。
            if ($ip < $beginip) { // 用户的IP小于中间记录的开始IP地址时
                $u = $i - 1; // 将搜索的上边界修改为中间记录减一
            } else {
                fseek($this->fp, $this->getlong3());
                $endip = strrev(fread($this->fp, 4)); // 获取中间记录的结束IP地址
                if ($ip > $endip) { // 用户的IP大于中间记录的结束IP地址时
                    $l = $i + 1; // 将搜索的下边界修改为中间记录加一
                } else { // 用户的IP在中间记录的IP范围内时
                    $findip = $this->firstip + $i * 7;
                    break; // 则表示找到结果，退出循环
                }
            }
        }

        //获取查找到的IP地理位置信息
        fseek($this->fp, $findip);
        $location['beginip'] = long2ip($this->getlong()); // 用户IP所在范围的开始地址
        $offset              = $this->getlong3();
        fseek($this->fp, $offset);
        $location['endip'] = long2ip($this->getlong()); // 用户IP所在范围的结束地址
        $byte              = fread($this->fp, 1); // 标志字节
        switch (ord($byte)) {
            case 1: // 标志字节为1，表示国家和区域信息都被同时重定向
                $countryOffset = $this->getlong3(); // 重定向地址
                fseek($this->fp, $countryOffset);
                $byte = fread($this->fp, 1); // 标志字节
                switch (ord($byte)) {
                    case 2: // 标志字节为2，表示国家信息被重定向
                        fseek($this->fp, $this->getlong3());
                        $location['country'] = $this->getstring();
                        fseek($this->fp, $countryOffset + 4);
                        $location['area'] = $this->getarea();
                        break;
                    default: // 否则，表示国家信息没有被重定向
                        $location['country'] = $this->getstring($byte);
                        $location['area']    = $this->getarea();
                        break;
                }
                break;
            case 2: // 标志字节为2，表示国家信息被重定向
                fseek($this->fp, $this->getlong3());
                $location['country'] = $this->getstring();
                fseek($this->fp, $offset + 8);
                $location['area'] = $this->getarea();
                break;
            default: // 否则，表示国家信息没有被重定向
                $location['country'] = $this->getstring($byte);
                $location['area']    = $this->getarea();
                break;
        }

        if ($location['country'] == iconv('UTF-8', 'GBK', " CZ88.NET") or $location['country'] == iconv('UTF-8', 'GBK', "纯真网络")) { // CZ88.NET表示没有有效信息
            $location['country'] = "";
        }
        if ($location['area'] == iconv('UTF-8', 'GBK', " CZ88.NET")) {
            $location['area'] = "";
        }

        return $location;
    }

    /**
     * Ip 地址转为数字地址
     *
     * php 的 ip2long 这个函数有问题
     * 133.205.0.0 ==>> 2244804608
     *
     * @param string $ip 要转换的 ip 地址
     * @return int    转换完成的数字
     */
    private function wphp_ip2long($ip) {
        $ip_arr = explode('.', $ip);
        $iplong = (16777216 * intval($ip_arr[0])) + (65536 * intval($ip_arr[1])) + (256 * intval($ip_arr[2])) + intval($ip_arr[3]);
        return $iplong;
    }

    /**
     * 返回读取的长整型数
     *
     * @access private
     * @return int
     */
    private function getlong() {
        //将读取的little-endian编码的4个字节转化为长整型数
        $result = unpack('Vlong', fread($this->fp, 4));
        return $result['long'];
    }

    /**
     * 返回读取的3个字节的长整型数
     *
     * @access private
     * @return int
     */
    private function getlong3() {
        //将读取的little-endian编码的3个字节转化为长整型数
        $result = unpack('Vlong', fread($this->fp, 3) . chr(0));
        return $result['long'];
    }

    /**
     * 返回压缩后可进行比较的IP地址
     *
     * @access private
     * @param string $ip
     * @return string
     */
    private function packip($ip) {
        // 将IP地址转化为长整型数，如果在PHP5中，IP地址错误，则返回False，
        // 这时intval将Flase转化为整数-1，之后压缩成big-endian编码的字符串
        return pack('N', intval($this->wphp_ip2long($ip)));
    }

    /**
     * 返回读取的字符串
     *
     * @access private
     * @param string $data
     * @return string
     */
    private function getstring($data = "") {
        $char = fread($this->fp, 1);
        while (ord($char) > 0) { // 字符串按照C格式保存，以\0结束
            $data .= $char; // 将读取的字符连接到给定字符串之后
            $char = fread($this->fp, 1);
        }
        return $data;
    }

    /**
     * 返回地区信息
     *
     * @access private
     * @return string
     */
    private function getarea() {
        $byte = fread($this->fp, 1); // 标志字节
        switch (ord($byte)) {
            case 0: // 没有区域信息
                $area = "";
                break;
            case 1:
            case 2: // 标志字节为1或2，表示区域信息被重定向
                fseek($this->fp, $this->getlong3());
                $area = $this->getstring();
                break;
            default: // 否则，表示区域信息没有被重定向
                $area = $this->getstring($byte);
                break;
        }
        return $area;
    }

    private function get_isp($str) {
        $ret = '';

        foreach ($this->dict_isp as $k => $v) {
            if (false !== strpos($str, $v)) {
                $ret = $v;
                break;
            }
        }
        return $ret;
    }

    /**
     * @param $ip
     * @return bool
     */
    private function is_valid_ip($ip) {
        $preg       = '/^(\d|\d{2}|1\d{2}|2[0-4]\d|25[0-5])\.(\d|\d{2}|1\d{2}|2[0-4]\d|25[0-5])\.(\d|\d{2}|1\d{2}|2[0-4]\d|25[0-5])\.(\d|\d{2}|1\d{2}|2[0-4]\d|25[0-5])$/';
        $is_matched = false;
        if (preg_match($preg, $ip, $m)) {
            $is_matched = true;
        }
        return $is_matched;
    }

    /**
     * 析构函数，用于在页面执行结束后自动关闭打开的文件。
     */
    public function __destruct() {
        if ($this->fp) {
            fclose($this->fp);
        }
        $this->fp = 0;
    }
}

/* end of file */