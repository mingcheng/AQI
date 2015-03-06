# 中国大陆重点城市空气质量历史数据库
 
> 声明：本项目提供的数据所有权权归原始方所有，在未获得所有方任何形式认可的情况下，请勿将本数据用作商业目的。本程序提供的数据仅用作参考，不做任何形式的承诺、担保以及负责。

## 下载

如果您只是想获取最终数据，请您访问 http://www.gracecode.com/aqi.html 。

## 数据源

目前基于 [中华人民共和国环境保护部信息中心]( http://datacenter.mep.gov.cn/ ) 等其他公共数据来源抓取并汇总而成，提供给网友作为当地空气质量的历史数据参考。<del>如有其他的数据源，欢迎网友提供。</del>


## 数据库 

数据库使用 SQLite 数据格式，单一的压缩文件，方便随时调用以及嵌入。

### 字段说明

以下是 SQLite 的主要表的字段说明，省去索引部分说明。

    sqlite> .schema
            -- 空气质量主数据表
    CREATE TABLE aqi (
                ID INTEGER NOT NULL PRIMARY KEY,          
                division UNSIGNED BIG INT(10) NOT NULL,     - 行政编码
                areaName VARCHAR(12) DEFAULT NULL,          - 地区名
                value INTEGER NOT NULL,                     - 值
                pollutant INTEGER DEFAULT NULL,             - 污染类型
                recordDate DATE NOT NULL,                   - 记录时间
                _fetchDate DATE NOT NULL,                   - 抓取时间
                source VARCHAR(8) DEFAULT NULL              - 来源
            );
            -- 地区表
    CREATE TABLE areas (
                ID INTEGER NOT NULL PRIMARY KEY,
                division UNSIGNED BIG INT(10) NOT NULL,     - 行政编码 
                name VARCHAR(12) NOT NULL,                  - 地区名
                engName VARCHAR(64),                        - 地区英文名
                pinyinName VARCHAR(64),                     - 地区中文拼音
                bottom BOOLEAN DEFAULT FALSE,               - 是否是最后一级地区
                superior UNSIGNED BIG INT(10)               - 上级地区编码，顶级为0
            );
            -- 污染类型表
    CREATE TABLE pollutant (
                ID INTEGER NOT NULL PRIMARY KEY,
                name VARCHAR(32) NOT NULL                   - 污染类型名称
            );
            

## 扩展


### 扩展使用其他数据源

主要功能为继承 `include/Base.inc.php` 中的基类，同时编写对对应的代码，程序入口为 `run` 函数。为了方便管理，建议将抓取的对应类文件放到 `fetcher` 目录中。

参考 `FetcherMep.inc.php`s 的实例代码：

    // 其中 DumpDataFromMep 基于 Base 函数
    class FetcherMep extends DumpDataFromMep {
        public function run() {
            // 请求线上地址
            $this->getDateFromUrl($url);
            // ...
            // 插入到数据库
            $this->insertAqiData($item['division_id'], 
                          $item['value'], $item['record_date'], $item['pollutant'], 
                          $item['area_name'], self::FLAG_SOURCE);
        }
    }
    
然后修改 `fetcher.php` 文件中需要执行的类加入到数组中：

    $builders = array('FetcherMep');

它们就会顺序执行。

#### 定时抓取

强烈建议使用 `crontab` 等工具定时抓取线上数据，可以考虑使用 

    $make fetch 

方法，详细可以参见 `Makefile 文件`。


## 参考

1. http://en.wikipedia.org/wiki/Air_quality_index#Mainland_China
2. 中国大陆地区的行政区划参见 assets/area.json 文件，JSON  格式

### 空气质量指数(AQI)范围及相应的空气质量类别对应表

<table>
    <tr>
        <th>空气质量指数</th><th>空气质量状况</th><th>对健康影响情况</th><th>建议采取的措施</th>
    </tr>
    <tr>
        <td>0~50</td><td>优</td><td>空气质量令人满意，基本无空气污染</td><td>各类人群可正常活动</td>
    </tr>
    <tr>        <td>51~100</td><td>良</td><td>空气质量可接受，但某些污染物可能对极少数异常敏感人群健康有较弱影响</td><td>极少数异常敏感人群应减少户外活动</td>
    </tr>
    <tr>        <td>101~150</td><td>轻度污染</td><td>易感人群症状有轻度加剧，健康人群出现刺激症状</td><td>儿童、老年人及心脏病、呼吸系统疾病患者应减少长时间、高强度的户外锻炼</td>
    </tr>
    <tr>        <td>151~200</td><td>度污染</td><td>进一步加剧易感人群症状，可能对健康人群心脏、呼吸系统有影响</td><td>儿童、老年人及心脏病、呼吸系统疾病患者避免长时间、高强度的户外锻炼，一般人群适量减少户外运动</td>
    </tr>
    <tr>        <td>201~300</td><td>重度污染</td><td>心脏病和肺病患者症状显著加剧，运动耐受力降低，健康人群普遍出现症状</td><td>儿童、老年人及心脏病、肺病患者应停留在室内，停止户外运动，一般人群减少户外运动</td>
    </tr>
    <tr>       <td>&gt;300</td><td>严重污染</td><td>健康人群运动耐受力降低，有明显强烈症状，提前出现某些疾病</td><td>儿童、老年人和病人应停留在室内，避免体力消耗，一般人群避免户外活动</td>
    </tr>
</table>


## 反馈&amp;联系

*  [@feelinglucky](https://twitter.com/feelinglucky)
*  http://www.gracecode.com/
*  lucky[at]gracecode.com
