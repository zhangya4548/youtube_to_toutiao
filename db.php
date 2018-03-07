<?php

/**@
 * 下面介绍几个基本是方法函数：
 * 1、 $Conn -> Table();
 * 选择数据表，参数是数据表名称
 * 2、$Conn -> Field();
 * 选择的字段名称，多个用逗号隔开，如不调用这个方法，则返回全部
 * 3、$Conn -> Where();
 * Sql Where子语句，根据条件筛选
 * 4、$Conn -> Order();
 * Sql 排序
 * 5、$Conn -> Page(int);
 * 参数是一个正整数数字，如调用这个方法，记录将分页显示
 * 6、$Conn -> Select(布尔值);
 * 执行查询，返回查询结果，如果有，则是一个二维数组，如果无，则返回假，参数可省略，如省略，默认为真，返回的数组包含数字元素
 * 7、$Conn -> Del();
 * 删除记录
 * 8、 $Conn -> Edit(array());
 * 修改记录，参数是一个一维数组，数组键是字段名称，数组值是字段值
 * 9、$Conn -> Into(array());
 * 添加记录，参数是一个一维数组，数组键是字段名称，数组值是字段值。
 * 以上方法可连续调用，比如：
 * $Rult = $Conn -> Table('user') -> Select(); //查询返回user表的所有记录
 * $Rult = $Conn -> Table('user') -> Page(20) -> Select();//查询返回user表的所有记录，并分页显
 */
class Mysql
{
    private $LocalHost = '127.0.0.1';
    private $LoaclUser = 'root';
    private $LocalPass = 'root';
    private $LocalBase = 'da';
    private $LocalCode = 'UTF8';
    private $PreFix;
    private $Conn;
    private $Start     = 0;
    private $Error     = false; //数据库连接状态, false表示未连接或连接不正常
    public  $Err       = true;  //Sql执行结果

    private $Table;
    private $Field = '*';
    private $Where = '';
    private $Order = '';

    private $PageSize  = 0; //分页显示->每页多少条，0为不分页显示
    private $PageCount = 1; //分页显示->总共有多少条
    private $PageNum   = 1; //分页显示->总共有多少页
    private $PageNo    = 1; //分页显示->当前第几页
    private $PageKey   = 'page'; //分页url参数键
    private $PageStart = 0; //分页显示->当前从第几条开始返回


    private $Select;
    private $Rest;

    private $Result = false;//结果集

    public $FormArray = array();

    public  $Instr_ID = 0;
    private $j        = 0;


    public function Parameter($Loca, $Root, $Pass, $Base, $Code, $PreFix = '')
    {
        $this->LoaclUser = $Root;
        $this->LocalBase = $Base;
        $this->LocalCode = $Code;
        $this->LocalHost = $Loca;
        $this->LocalPass = $Pass;
        $this->PreFix    = $PreFix;

        return $this;
    }

    private function Connection($Sql)
    {
        @!function_exists(mysqli_connect) ? die('查询失败，无法加载mysqli扩展') : null;
        $this->Conn  = @new mysqli($this->LocalHost, $this->LoaclUser, $this->LocalPass, $this->LocalBase);
        $this->Error = mysqli_connect_errno() == 0 ? true : false;
        !$this->Error ? die('数据库连接错误，请检查数据库连接参数') : null;
        $this->Conn->query('SET NAMES ' . $this->LocalCode);
        $this->Rest     = $this->Conn->query($Sql);
        $this->Err      = mysqli_error($this->Conn);
        $this->Instr_ID = mysqli_insert_id($this->Conn);
        @$this->Rest->free_result;
        @$this->Conn->close;
        $this->FormArray = '';

        return $this;
    }

    public function null()
    {
        $this->PageSize = 0;
        //$this->PageCount = 1;
        $this->PageStart = 1;
        $this->Field     = ' * ';
        $this->Select    = '';
        unset($this->Table, $this->Where, $this->Order, $this->Result);
    }

    public function Table($TableName)
    {//数据表
        $this->null();
        $this->Table = '`' . $this->PreFix . $TableName . '`';

        return $this;
    }

    public function Field($Array = '*')
    {//数据字段
        !empty($this->Field) ? $this->Field = '' : null;
        $Array = explode(',', $Array);
        foreach ($Array as $field)
        {
            $this->Field .= !$this->Start ? '`' . $field . '`' : ', `' . $field . '`';
            $this->Start++;
        }
        $this->Start = 0;

        return $this;
    }

    public function Where($Where)
    {//条件
        $this->Where = ' where ' . $Where;

        return $this;
    }

    public function Order($Order)
    {//排序
        $this->Order = ' order by ' . $Order;

        return $this;
    }

    public function pk($key)
    {//分页url参数键
        $this->PageKey = $key;

        return $this;
    }

    public function Page($PageSize)
    {//分页
        $this->PageSize = $PageSize;
        $this->PageNo   = $this->get($this->PageKey);
        $this->PageNo   = empty($this->PageNo) || !isset($this->PageNo) || !is_numeric($this->PageNo) || $this->PageNo < 1 ? 1 : $this->PageNo;

        return $this;
    }

    public function post($Key, $Filter = true)
    {
        return $Filter ? strip_tags($_POST[$Key]) : $_POST[$Key];
    }

    public function get($Key, $Filter = true)
    {
        return $Filter ? strip_tags($_GET[$Key]) : $_GET[$Key];
    }

    public function Sel()
    {
        $this->Select = 'Select ' . $this->Field . ' from ' . $this->Table . $this->Where . $this->Order;
        $this->Connection($this->Select);
        if ($this->Rest->num_rows)
        {
            while ($Rs = $this->Rest->fetch_assoc())
            {
                $this->Result[] = $Rs;
            }
        }
        $DataBase = $this->Result;

        return empty($DataBase) ? false : $DataBase;
    }

    public function querys($Sql = '', $Type = 'not', $biao = false)
    {
        $this->Select = $Sql;
        $this->Connection($this->Select);
        if ($this->Rest->num_rows)
        {
            if (!$biao)
            {
                while ($Rs = $this->Rest->fetch_array())
                {
                    $this->Result[] = !preg_match('/^\d+$/i', $Type) ? $Rs : $Rs[$Type];
                }
            }
            else
            {
                while ($Rs = $this->Rest->fetch_assoc())
                {
                    $this->Result[] = $Rs;
                }
            }
        }
        $DataBase = $this->Result;

        return empty($DataBase) ? false : $DataBase;

    }

    public function executes($Sql = '')
    {
        $this->Connection($Sql);

        return $this->Rest;
    }


    public function exists($T = '', $F = '', $W = '')
    {
        if (empty($F))
        {
            return 0;
        }
        $cmd = empty($W) ? 'Select sum(' . $F . ') as `baiyinum` from `' . $this->PreFix . $T . '`' : 'Select sum(' . $F . ') as `baiyinum` from `' . $this->PreFix . $T . '` Where ' . $W;
        $this->Connection($cmd);
        unset($T, $F, $W, $cmd);
        $Rel = $this->Rest->fetch_array();

        return round($Rel['baiyinum'], 2);
    }


    public function ExistsTo($Bili = 10000, $T = '', $F = '', $W = '')
    {
        if (empty($F))
        {
            return 0;
        }
        $cmd = empty($W) ? 'Select sum(' . $F . ') as `baiyinum` from `' . $this->PreFix . $T . '`' : 'Select sum(' . $F . ') as `baiyinum` from `' . $this->PreFix . $T . '` Where ' . $W;
        $this->Connection($cmd);
        unset($T, $F, $W, $cmd);
        $Rel = $this->Rest->fetch_array();

        return round($Rel['baiyinum'] * $Bili);
    }


    public function Select($Type = true, $ListNum = 1)
    { //返回记录（数组形式， 返回条数）
        @$this->Select = 'Select ' . $this->Field . ' from ' . $this->Table . $this->Where . $this->Order;
        if (is_numeric($ListNum))
        {
            if ($this->PageSize > 0)
            {
                $this->Connection($this->Select);//执行查询
                $this->PageCount = $this->Rest->num_rows;//取得记录总数
                $this->PageNum   = ceil($this->PageCount / $this->PageSize); //总共有多少页
                $this->PageNo    = $this->PageNo > $this->PageNum ? $this->PageNum : $this->PageNo;
                $this->PageStart = ($this->PageNo - 1) * $this->PageSize;   //当前从第几条开始返回
                $this->Select    .= ' limit ' . $this->PageStart . ', ' . $this->PageSize; //重新构造sql语句
            }
            else
            {
                $this->Select .= ' limit ' . $ListNum; //重新构造sql语句
            }
        }
        else
        {
            $this->Select .= ' limit 1'; //重新构造sql语句
        }
        //echo $this->Select; die;
        $this->Connection($this->Select);//再次执行查询
        if (@$this->Rest->num_rows)
        {//如果记录存在
            if ($Type)
            {
                while ($Rs = $this->Rest->fetch_array())
                {
                    $this->Result[] = $Rs;
                }
            }
            else
            {
                while ($Rs = $this->Rest->fetch_assoc())
                {
                    $this->Result[] = $Rs;
                }
            }
        }
        if ((@$ListNum == 1 or !is_numeric($ListNum)) && !$this->PageSize)
        {
            @$this->Result = $this->Result[0];
        }
        $DataBase = $this->Result;

        return empty($DataBase) ? false : $DataBase;
    }

    public function Num()
    { //返回记录总数
        $this->Select = 'Select ' . $this->Field . ' from ' . $this->Table . $this->Where . $this->Order;
        $this->Connection($this->Select);//执行查询

        return $this->Rest->num_rows;//取得记录总数
    }

    public function PageNav($NumNav = false)
    {  //分页
        $Action = $this->get('action');
        !empty($Action) or $Action = 'index';
        $Module = $this->get('module');
        !empty($Module) or $Module = 'index';
        $NavUrl   = '/' . $Module . '/' . $Action . '/' . $this->PageKey . '/';
        $NaIndex  = '/' . $Module . '/' . $Action;
        $PageHtml = "\n<div class=\"pagenav\">";
        $PageHtml .= '<span>' . $this->PageCount . '条记录        ' . $this->PageNo . '/' . $this->PageNum . '页</span>            ';
        $this->PageNo <= 1 or $PageHtml .= "<a href=\"" . $NaIndex . "\">首页</a>\n<a href=\"" . $NavUrl . ($this->PageNo - 1) . "\">上一页</a>\n";
        if ($NumNav)
        {
            $PageHtml .= $this->NumPage($NavUrl);
        }
        $this->PageNo >= $this->PageNum or $PageHtml .= "<a href=\"" . $NavUrl . ($this->PageNo + 1) . "\">下一页</a>\n<a href=\"" . $NavUrl . $this->PageNum . "\">尾页</a>\n";
        $PageHtml .= "</div>\n";

        return $PageHtml;
    }

    private function NumPage($Can = '')
    { //数字分页
        $NumHtml = '';
        $First   = 1;
        $Last    = $this->PageNum;
        if ($this->PageNum > 5)
        {
            if ($this->PageNo < $this->PageNum)
            {
                $First = $this->PageNo - 2;
                $Last  = $this->PageNo + 2;
            }
            else
            {
                $First = $this->PageNo - 4;
                $Last  = $this->PageNum;
            }
        }
        if ($First < 1)
        {
            $First = 1;
            $Last  = $First + 4;
        }
        if ($Last > $this->PageNum)
        {
            $First = $this->PageNum - 4;
            $Last  = $this->PageNum;
        }
        for ($i = $First; $i <= $Last; $i++)
        {
            $NumHtml .= $this->PageNo != $i ? "\n\t" . '<a href="' . $Can . $i . '">' . $i . '</a>' . "\n\t" : "\n\t" . '<a class="hover" disabled="disabled">' . $i . '</a>' . "\n\t";
        }
        unset($Can, $First, $i, $Last);

        return $NumHtml;
    }

    public function UserPage($NumNav = false, $PageName = 'index', $Mulu = 'user')
    {  //会员中心分页
        $NavUrl   = '/' . $Mulu . '/' . $PageName . '/' . $this->PageKey . '/';
        $PageHtml = "\n<div class=\"pagenav\">";
        $PageHtml .= '<span>' . $this->PageCount . '条记录        ' . $this->PageNo . '/' . $this->PageNum . '页</span>            ';
        $this->PageNo <= 1 or $PageHtml .= "<a href=\"" . $NavUrl . "1\">首页</a>\n<a href=\"" . $NavUrl . ($this->PageNo - 1) . "\">上一页</a>\n";
        if ($NumNav)
        {
            $PageHtml .= $this->NumPage($NavUrl);
        }
        $this->PageNo >= $this->PageNum or $PageHtml .= "<a href=\"" . $NavUrl . ($this->PageNo + 1) . "\">下一页</a>\n<a href=\"" . $NavUrl . $this->PageNum . "\">尾页</a>\n";
        $PageHtml .= "</div><div class=\"clear\"></div>\n";

        return $PageHtml;
    }


    //表单处理开始

    //判断表单时候提交
    public function FormIs($Keys = 'mm')
    {
        return $_POST[$Keys] == 1 ? true : false;
    }

    //post方式获取数据
    public function _post($Keys = '', $TiHuan = '')
    {
        $Values                 = strip_tags($_POST[$Keys]);
        $this->FormArray[$Keys] = empty($Values) ? $TiHuan : $Values;

        return empty($Values) ? $TiHuan : $Values;
    }

    //get方法获取数据
    public function _get($Keys = '', $TiHuan = '')
    {
        $Values = strip_tags($_GET[$Keys]);

        return empty($Values) ? $TiHuan : $Values;
    }

    //判断是否为数字并且不小于0
    public function IsNum($Num = 0, $Mesg = '参数必须为数字')
    {
        if (is_numeric($Num) && !empty($Num) && $Num >= 0)
        {
            return $Num;
        }
        else
        {
            die($Mesg);
        }
    }

    //判断是否为数字并且不小于0返回True/False
    public function NumBer($Num = 0)
    {
        return is_numeric($Num) && !empty($Num) && $Num >= 0 ? true : false;
    }

    //检测相关数据似乎存在
    public function IsData($Types = true, $memg = '数据已经存在')
    {
        $this->Connection('select ' . $this->Field . ' from ' . $this->Table . $this->Where);
        if ($Types)
        {
            $this->Rest->num_rows > 0 ? die($memg) : null;
        }
        else
        {
            return $this->Rest->num_rows;
        }
    }


    //写入数据库记录
    public function into($Mesg = '')
    {

        !is_array($Mesg) ? die($Mesg) : null;
        $Sql = 'insert into ' . $this->Table . ' (`';
        $I   = 0;
        $Vals = '';
        $Duan = '';
        foreach ($Mesg as $Key => $Val)
        {
            $Duan .= !$I ? $Key . '`' : ', `' . $Key . '`';
            if (is_numeric($Val))
            {
                $Vals .= !$I ? $Val : ', ' . $Val;
            }
            else
            {
                $Vals .= !$I ? '\'' . $Val . '\'' : ', \'' . $Val . '\'';
            }
            $I++;
        }
        $Sql .= $Duan . ') values (' . $Vals . ')';

        //@file_put_contents('1.sql', $Sql, FILE_APPEND);

        $this->Connection($Sql);

        return !empty($this->Err) ? false : true;
    }

    //数组形式写入数据
    public function MsgBox($Table = '', $Filed = array())
    {
        $this->Table($Table);
        foreach ($Filed as $Key => $Val)
        {
            $this->FormArray[$Key] = $Val;
        }

        return $this->Into('未取得数据');
    }

    //修改数据库记录
    public function Edit($Array = array())
    {
        if (empty($Array))
        {
            $Array = $this->FormArray;
        }
        if (!is_array($Array) || empty($Array))
        {
            return false;
        }
        else
        {
            $Sql  = 'update ' . $this->Table . ' set ';
            $I    = 0;
            $Sub  = '';
            $Huan = array('-' => '[jian]', '+' => '[jia]', '*' => '[cheng]', '/' => '[chu]');
            $Zhan = array('[jian]' => '-', '[jia]' => '+', '[cheng]' => '*', '[chu]' => '/');

            foreach ($Array as $Files => $Val)
            {
                $Val = !is_numeric($Val) && !preg_match('/\`\w+\`\s*(\+|\-|\*|\/)/i', $Val) ? '\'' . $Val . '\'' : $Val;
                foreach ($Huan as $key => $val)
                {
                    $Val = str_replace($key, $val, $Val);
                }
                $duan = !$I ? '`' . $Files . '` = ' : ', `' . $Files . '` = ';
                $Sub  .= $duan . $Val;
                $I++;
            }
            $Sql .= $Sub . $this->Where;
            foreach ($Zhan as $Fan => $Hui)
            {
                $Sql = str_replace($Fan, $Hui, $Sql);
            }

            //echo $Sql; die;

            $this->Connection($Sql);
            unset($Array, $duan, $Fan, $Files, $Huan, $Hui, $I, $key, $Sql, $Sub, $Val, $Zhan, $val);

            return !empty($this->Err) ? false : true;
        }
    }

    //删除数据库记录
    public function del()
    {
        $Sql = 'delete from ' . $this->Table . $this->Where;
        $this->Connection($Sql);
        unset($Sql);

        return !empty($this->Err) ? false : true;
    }

    //表单处理结束

    //页面跳转
    public function Msg($Text = '操作成功')
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '<script type="text/javascript">
  <!--
    alert("' . $Text . '");
    document.location="' . $_SERVER['HTTP_REFERER'] . '";
  //-->  
</script>';
        exit;
    }

    #取得系统当前时间
    public function Times()
    {
        return str_replace('-', '[jian]', date('Y-m-d H:i:s'));
    }

    #取得用户IP地址
    public function GetIP()
    {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        {
            $ip = getenv("HTTP_CLIENT_IP");
        }
        else
        {
            if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
            {
                $ip = getenv("HTTP_X_FORWARDED_FOR");
            }
            else
            {
                if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
                {
                    $ip = getenv("REMOTE_ADDR");
                }
                else
                {
                    if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
                    {
                        $ip = $_SERVER['REMOTE_ADDR'];
                    }
                    else
                    {
                        $ip = "unknown";
                    }
                }
            }
        }

        return ($ip);
    }


    //最后关闭数据库连接
    public function Close()
    {
        !is_object($this->Conn) or mysqli_close($this->Conn);
    }

}