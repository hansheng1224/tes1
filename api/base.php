<?php
session_start();
date_default_timezone_set("Asia/Taipei");

class DB{
    protected $dsn="mysql:host=localhost;charset=utf8;dbname=db12";
    protected $table;
    protected $pdo;

    public function __construct($table)
    {
        $this->table=$table;
        $this->pdo=new PDO($this->dsn,'root','');
    }


    public function find($id){
        $sql="select * from $this->table ";
        
        if(is_array($id)){
            $tmp=$this->arrayToSqlArray($id);

            $sql = $sql . " where " .join(" && ",$tmp);

        }else{
            $sql = $sql . " where `id`='$id'";
        }

        return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    }
    public function all(...$arg){
        $sql="select * from $this->table ";
        
        if(isset($arg[0])){
            if(is_array($arg[0])){
                $tmp=$this->arrayToSqlArray($arg[0]);
    
                $sql=$sql . " where " . join(" && ",$tmp);
            }else{
                $sql = $sql . $arg[0];
            }
        }

        if(isset($arg[1])){
            $sql = $sql . $arg[1];
        }

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    }
    public function save($array){
        if(isset($array['id'])){
            //更新
            $id=$array['id'];
            echo $id;
            unset($array['id']);
            $tmp=$this->arrayToSqlArray($array);
            dd($tmp);
            $sql="update $this->table set ".join(",",$tmp)." where `id`='$id'";
            echo $this->table;
                                            
        }else{
            //新增
            $cols=array_keys($array);
            $sql="insert into $this->table (`".join("`,`",$cols)."`) values('".join("','",$array)."')";
        }

        //echo $sql;

        // $this->pdo->exec($sql);

    }
    public function del($id){
        $sql="delete from $this->table ";
        
        if(is_array($id)){
            $tmp=$this->arrayToSqlArray($id);

            $sql = $sql . " where " .join(" && ",$tmp);

        }else{
            $sql = $sql . " where `id`='$id'";
        }

        return $this->pdo->exec($sql);
    }

    public function count(...$arg){
        return $this->math('count',...$arg); //...為解構賦值
    }

    public function sum($col,...$arg){//...為不定參數
        return $this->math('sum',$col,...$arg); //...為解構賦值
    }
    public function max($col,...$arg){
        return $this->math('max',$col,...$arg); //...為解構賦值
    }
    public function min($col,...$arg){
        return $this->math('min',$col,...$arg); //...為解構賦值
    }
    public function avg($col,...$arg){
        return $this->math('avg',$col,...$arg); //...為解構賦值
    }

    private function arrayToSqlArray($array){
        foreach($array as $key => $value){
            $tmp[]="`$key`='$value'";
        }

        return $tmp;
    }

    private function math($math,...$arg){
        switch($math){
            case 'count':
                $sql="select count(*) from $this->table ";
                if(isset($arg[0])){
                    $con=$arg[0]; 
                }
            break;
            default:
                $col=$arg[0];
                if(isset($arg[1])){
                    $con=$arg[1];
                }
                $sql="select $math($col) from $this->table ";

        }

        if(isset($con)){
            if(is_array($con)){
                $tmp=$this->arrayToSqlArray($con);
                $sql=$sql . " where " .  join(" && ",$tmp);
            }else{
                $sql=$sql . $con;
            }
        }
        //echo $sql;
        return $this->pdo->query($sql)->fetchColumn();
    }

}

function dd($array){
echo "<pre>";
print_r($array);
echo "</pre>";
}

function to($url){
    header("location:".$url);
}

function q($sql){
    $dsn="mysql:host=localhost;charset=utf8;dbname=db04";
    $pdo=new PDO($dsn,'root','');

    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}



$Bottom=new DB('bottom');
$Title=new DB('title');
$Ad=new DB('ad');
$Mvim=new DB('mvim');
$Image=new DB('image');
$News=new DB('news');
$Admin=new DB('admin');
$Menu=new DB('menu');
$Total=new DB('total');


if(!isset($_SESSION['visit'])){
    $_SESSION['visit']=1;
    $total=$Total->find(1);
    $total['total']++;
    $Total->save($total);
}

