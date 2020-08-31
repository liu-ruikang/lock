<?php

/**
 * @name IRenewalHandler
 * @package Lock\Util
 * @desc 续租线程回调接口
 * @author  Ruikang <liuruikang@360.cn>
 * @date 2020年8月13日 上午08:12:20
 * @version 1.0.0
 * 
 * @修改记录
 * <pre>
 * 版本         修改人               修改日期         修改内容描述
 * -------------------------------------------------------------------
 * 1.0.0        Ruikang              2020年8月13日    
 * -------------------------------------------------------------------
 * </pre>
 */

namespace Lock\Util;

interface IRenewalHandler
{
    public function callBack();
}

