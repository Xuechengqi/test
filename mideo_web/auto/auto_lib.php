<?php
//header("Content-type: text/html; charset=utf-8");
//用于定时任务
class mgo {

    protected $db=null;

    const API_URL = 'http://127.0.0.1/';

    //通用评论
    public $comment=array('Eu já sou o seu fã','Muito obrigado por compartilhar os vídeos','Este vídeo é muito engraçado','Eu gosto muito de você','Podemos ser amigos？','Onde foi filmado este vídeo？','kkkkk,quase morri de rir','Que estranho','Estou chorando de rir','Que engraçado','Você é tão fofinho','Muito interessante','Leve-me para jogar juntos','Oi','Posso conhecer você?','Isso é verdade','espero que você pode compartilhar mais coisas da sua vida','Quero ser seu amigo','Você é um anjo','espero que vocês podem compartilhar mais videos','Eu sou igual a você','pode-me passar o seu contato？','fazer isso não é muito legal','estou com dor de cabeça','Que sono,não quero ir trabalhar','Gosto muito da maneira que você vive','Gosto de viver assim','Como você acha da sua vida？','Oi, amigo','Eu adoro mideo,muito legal','Muito prazer em conhecer você aqui','Legal!','Já estou com fome,kkkk','Eu também quero compartilhar a minha vida','Muito obrigado por compartilhar','Muito prazer','Que legal!','Eu adoro os seus vídeos','Queria muito de conhecer você na vida real também','Que chato！','É você que está no vídeo？','Você é muito querida!','Lindo de mais!','você é tão feliz','Estou querendo muito de apaixonar por alguém','Muito obrigado por compartilhar','kkkkk,quase morri de rir','Você é tão fofinho','Qual é o seu nome？Quero muito de conhecer você','Todo dia eu vejo os seus vídeos e fico feliz','Este é o vídeo mais engraçado que vi hoje.','espero que hoje não preciso de ficar a trabalhar até mais tarde','Como é que você se chama？','Que fofinho!Quero muito de ter um na minha casa também','Você é muito bonita!','os seus olhos são lindo de mais!','Posso convidar você para jantar junto？','O tempo de hoje está bom','eu gosto da minha vida,kkkk','Espero que você esteja sempre feliz','eu quero ser o usuário com mais fãs de mideo,kkkkk','Adoro usar este aplicativo','Muito prazer','quero fazer regime para emagrecer','Que sorte de poder ver os seus vídeos','Hoje estou muito cansado','você é muito sexy','Este vídeo já revi várias vezes','Também são sei o que posso dizer','Quando eu abrir uma empresa,você pode vir trabalhar junto comigo？、','o que acha posso filmar hoje？','Eu gosto muito de você, e você gosta de mim？','Seja bem-vindo à minha casa','Você me conhece?','Eu também compartilhei uns vídeos engraçados','Vou dormir agora, estou com muito sono','que vídeo engraçado','kkkkk,quase morri de rir','esta pessoa é muito estranho','eu também quero fazer assim','estes dias estou com muito serviço, nem deu tempo para ir passear','não entendi o que você quer dizer','você é o melhor!','Adoro esta cara','Legal!','Você','gosto muito de ver os seus vídeos','Qual é o nome da música que está tocando no vídeo？','Que legal!','Muito interessante','não gostei muito do seu vídeo','Onde foi filmado este vídeo？','Espero que você esteja sempre feliz','os seus olhos são lindo de mais!','Que sorte de poder ver os seus vídeos','Desejo que você esteja feliz todos os dias','Qual é o seu team de futebol?','Muito obrigada por nos mostrar estes vídeos','Adoro usar este aplicativo','Você é tão fofinho','Eu já sou o seu fã','Muito obrigado por compartilhar os vídeos','Este vídeo é muito engraçado','Eu gosto muito de você','Podemos ser amigos？','Onde foi filmado este vídeo？','kkkkk,quase morri de rir','Que estranho','Estou chorando de rir','Que engraçado','Você é tão fofinho','Muito interessante','Leve-me para jogar juntos','Oi','Posso conhecer você?','Isso é verdade','espero que você pode compartilhar mais coisas da sua vida','Quero ser seu amigo','Você é um anjo','espero que vocês podem compartilhar mais videos','Eu sou igual a você','pode-me passar o seu contato？','fazer isso não é muito legal','estou com dor de cabeça','Que sono,não quero ir trabalhar','Gosto muito da maneira que você vive','Gosto de viver assim','Como você acha da sua vida？','Oi, amigo','Eu adoro mideo,muito legal','Muito prazer em conhecer você aqui','Já estou com fome,kkkk','Eu também quero compartilhar a minha vida','Muito obrigado por compartilhar','Muito prazer','Que legal!','Eu adoro os seus vídeos','Queria muito de conhecer você na vida real também','Que chato！','É você que está no vídeo？','Você é muito querida!','Lindo de mais!','você é tão feliz','Estou querendo muito de apaixonar por alguém','Muito obrigado por compartilhar','kkkkk,quase morri de rir','Você é tão fofinho','Qual é o seu nome？Quero muito de conhecer você','Todo dia eu vejo os seus vídeos e fico feliz','Este é o vídeo mais engraçado que vi hoje.','espero que hoje não preciso de ficar a trabalhar até mais tarde','Como é que você se chama？','Que fofinho!Quero muito de ter um na minha casa também','Você é muito bonita!','os seus olhos são lindo de mais!','Posso convidar você para jantar junto？','O tempo de hoje está bom','eu gosto da minha vida,kkkk','Espero que você esteja sempre feliz','eu quero ser o usuário com mais fãs de mideo,kkkkk','Adoro usar este aplicativo','Muito prazer','quero fazer regime para emagrecer','Que sorte de poder ver os seus vídeos','Hoje estou muito cansado','você é muito sexy','Este vídeo já revi várias vezes','Também são sei o que posso dizer','Quando eu abrir uma empresa,você pode vir trabalhar junto comigo？','o que acha posso filmar hoje？','Eu gosto muito de você, e você gosta de mim？','Seja bem-vindo à minha casa','Você me conhece?','Eu também compartilhei uns vídeos engraçados','Vou dormir agora, estou com muito sono','que vídeo engraçado','kkkkk,quase morri de rir','esta pessoa é muito estranho','eu também quero fazer assim','estes dias estou com muito serviço, nem deu tempo para ir passear','não entendi o que você quer dizer','você é o melhor!','Adoro esta cara','Legal!','Você','gosto muito de ver os seus vídeos','Qual é o nome da música que está tocando no vídeo？','Que legal!','Muito interessante','não gostei muito do seu vídeo','Onde foi filmado este vídeo？','Espero que você esteja sempre feliz','os seus olhos são lindo de mais!','Que sorte de poder ver os seus vídeos','Desejo que você esteja feliz todos os dias','Qual é o seu team de futebol?','Muito obrigada por nos mostrar estes vídeos','Legal','Daora','Incrível','Belo vídeo.','Que emocionante.','Hahahahahaha','Kkkkkkkk','Maravilhoso','Lindo','Bela performance','Huahuahuahuahua','Que engraçado','Huehuehuehue','Melhor vídeo','Vídeo espetacular','Sensacional','Fantástico','Inacreditável','Que impressionante','Surpreendente','Fenomenal','Excelente vídeo','Ótimo vídeo','Vídeo fantástico','Que criativo','Inédito','Irado','Que alegre!','Impressionante','Fascinante','Que empolgante!','Que divertido!','Que vídeo interessante','Top','xD','Que lindo','Nossa','Wow','Que incrível!','Que fantástico!','Que sensacional!','Extraordinário','Que vídeo extraordinário!','Que fenomenal!','Admirável','Aeeee','Parabéns pelo vídeo','Que extraordinário','Fabuloso','Que surpreendente','Linda','Esplêndido','Vídeo encantador','Deslumbrante','Maravilhoso vídeo!','Extraodinário vídeo!','Deslumbrante vídeo!','Fantástico vídeo!','Encantador','Que vídeo encantador','Fascinante vídeo','Lindo vídeo','Incrível vídeo','Que vídeo surpreendente','Magnífico.','Que vídeo magnífico','Que vídeo sensacional','Que vídeo impressionante','Que vídeo fabuloso!','Que vídeo bonito','Lol','Que vídeo chocante','Curti','HUASUHSHUHSA','rsrsrsrsrs','Omg',' huhauhauha','uhull','Sucesso','Mito','Mitou','Maneiro','Bacana','Animal','LOL','OMG','Adorei o vídeo','Curti o vídeo','Gostei do Vídeo','Amei','Amei o vídeo','YAAAS','; )',': )',':O','Ò_Ó','Nuss','Que maneiro','Que beleza');
    //评论用的用户
    public $comment_user=array(array("id"=>207,"name"=>"andrewceilo"),array("id"=>100,"name"=>"tiffanyhudd"),array("id"=>101,"name"=>"jackievdreamer"),array("id"=>102,"name"=>"crispycream"),array("id"=>103,"name"=>"UhButterfly"),array("id"=>104,"name"=>"LadiiSwagtastic"),array("id"=>105,"name"=>"OfficialEvaMarie"),array("id"=>106,"name"=>"JSO"),array("id"=>107,"name"=>"NoStringsAttached"),array("id"=>108,"name"=>"DTBMuzic"),array("id"=>109,"name"=>"leahjessica"),array("id"=>110,"name"=>"matho887"),array("id"=>112,"name"=>"lifeoflaurenj"),array("id"=>113,"name"=>"JadaBerg"),array("id"=>114,"name"=>"KAKA_22_Q8"),array("id"=>115,"name"=>"rfchoke33"),array("id"=>116,"name"=>"zgrty_bold"),array("id"=>117,"name"=>"4206prince"),array("id"=>118,"name"=>"meghancook"),array("id"=>119,"name"=>"Mouna_Alraoji123"),array("id"=>120,"name"=>"LinzLinz14"),array("id"=>121,"name"=>"Followspice"),array("id"=>122,"name"=>"ajskates"),array("id"=>123,"name"=>"SabanCerimovic"),array("id"=>127,"name"=>"haroldthomas14"),array("id"=>128,"name"=>"endang_indriani"),array("id"=>129,"name"=>"yolanda1206_"),array("id"=>130,"name"=>"nunofpedro_"),array("id"=>131,"name"=>"celine_berkani"),array("id"=>132,"name"=>"anne_gawelle_"),array("id"=>195,"name"=>"lupitamarques"),array("id"=>196,"name"=>"robert.m.gittens"),array("id"=>197,"name"=>"sashaloazi"),array("id"=>198,"name"=>"hosseneblis051"),array("id"=>199,"name"=>"dodiwahyu58"),array("id"=>200,"name"=>"giobia88"),array("id"=>201,"name"=>"lilliandribeiro"),array("id"=>202,"name"=>"wesleypisquila22"),array("id"=>203,"name"=>"priincesskayla_"),array("id"=>206,"name"=>"viniciusszs"),array("id"=>207,"name"=>"andrewceilo"),array("id"=>208,"name"=>"leticiacooking"),array("id"=>209,"name"=>"tommyswiss89"),array("id"=>210,"name"=>"pieroloyolas"),array("id"=>211,"name"=>"_dannisouza_"),array("id"=>212,"name"=>"justin_20155"),array("id"=>213,"name"=>"karlapiscope"),array("id"=>214,"name"=>"mateusantunesdepaula"),array("id"=>215,"name"=>"graci_rio"),array("id"=>216,"name"=>"ahmed___sellami"),array("id"=>217,"name"=>"carlita_you"),array("id"=>218,"name"=>"marcemendes15"),array("id"=>219,"name"=>"rd__wilson"),array("id"=>220,"name"=>"marcosfrist"),array("id"=>221,"name"=>"laryssa.pereira.s2"),array("id"=>222,"name"=>"rapunzeljess"),array("id"=>223,"name"=>"fasil_ap"),array("id"=>224,"name"=>"mech.gowtham.5"),array("id"=>100,"name"=>"tiffanyhudd"),array("id"=>101,"name"=>"jackievdreamer"),array("id"=>102,"name"=>"crispycream"),array("id"=>103,"name"=>"UhButterfly"),array("id"=>104,"name"=>"LadiiSwagtastic"),array("id"=>105,"name"=>"OfficialEvaMarie"),array("id"=>106,"name"=>"JSO"),array("id"=>107,"name"=>"NoStringsAttached"),array("id"=>108,"name"=>"DTBMuzic"),array("id"=>109,"name"=>"leahjessica"),array("id"=>110,"name"=>"matho887"),array("id"=>112,"name"=>"lifeoflaurenj"),array("id"=>113,"name"=>"JadaBerg"),array("id"=>114,"name"=>"KAKA_22_Q8"),array("id"=>115,"name"=>"rfchoke33"),array("id"=>116,"name"=>"zgrty_bold"),array("id"=>117,"name"=>"4206prince"),array("id"=>118,"name"=>"meghancook"),array("id"=>119,"name"=>"Mouna_Alraoji123"),array("id"=>120,"name"=>"LinzLinz14"),array("id"=>121,"name"=>"Followspice"),array("id"=>122,"name"=>"ajskates"),array("id"=>123,"name"=>"SabanCerimovic"),array("id"=>127,"name"=>"haroldthomas14"),array("id"=>128,"name"=>"endang_indriani"),array("id"=>129,"name"=>"yolanda1206_"),array("id"=>130,"name"=>"nunofpedro_"),array("id"=>131,"name"=>"celine_berkani"),array("id"=>132,"name"=>"anne_gawelle_"),array("id"=>195,"name"=>"lupitamarques"),array("id"=>196,"name"=>"robert.m.gittens"),array("id"=>197,"name"=>"sashaloazi"),array("id"=>198,"name"=>"hosseneblis051"),array("id"=>199,"name"=>"dodiwahyu58"),array("id"=>200,"name"=>"giobia88"),array("id"=>201,"name"=>"lilliandribeiro"),array("id"=>202,"name"=>"wesleypisquila22"),array("id"=>203,"name"=>"priincesskayla_"),array("id"=>206,"name"=>"viniciusszs"),array("id"=>207,"name"=>"andrewceilo"),array("id"=>208,"name"=>"leticiacooking"),array("id"=>209,"name"=>"tommyswiss89"),array("id"=>570,"name"=>"THIAGOFINKENAUER"),array("id"=>571,"name"=>"TAINAMORAES"),array("id"=>572,"name"=>"STÉFANIVIEIRA"),array("id"=>573,"name"=>"LIVIAVELLOSO"),array("id"=>574,"name"=>"FERNANDARIBEIRO"),array("id"=>575,"name"=>"JayneCavalheiro"),array("id"=>576,"name"=>"DALLVANABANDEIRA"),array("id"=>577,"name"=>"RAFAELBRUM"),array("id"=>578,"name"=>"KATIANED.LIMA"),array("id"=>579,"name"=>"HENRIQUEOLIVEIRA"),array("id"=>580,"name"=>"THAYSMACARIO"),array("id"=>581,"name"=>"SÔNIASIBELLE"),array("id"=>582,"name"=>"LaianeSilvaSantos"),array("id"=>583,"name"=>"JHONATHANOLIVEIRA"),array("id"=>584,"name"=>"MARCIACATFLORES"),array("id"=>585,"name"=>"JOYCEGOMES"),array("id"=>586,"name"=>"CAMILAFELIX"),array("id"=>587,"name"=>"MarcosAntonio"),array("id"=>588,"name"=>"WILLIENETORRES"),array("id"=>589,"name"=>"FLÁVINHASANTOS"),array("id"=>590,"name"=>"WANIELLYMELLO"),array("id"=>593,"name"=>"JÉSSICAO.SANTOS_1dtai"),array("id"=>594,"name"=>"FÁTIMALYRA_0g319"));

    public function __construct() {
    }

    public function DB(){
        if(is_null($this->db))
            $this->db = new mysql();
        return $this->db;
    }

    //获取文件前几行 并更新文件  只能cvs文件
    public function getNumLine($fn,$num=10){
        $list=array();
        $file = fopen($fn, "r") or exit("Unable to open file!");

        $i=1;
        while(!feof($file))
        {
            if($i>$num){
                break;
            }
            $row = fgets($file);
            $arr =  explode("\t", trim($row));
            if(count($arr)>1){
                $list[]=$arr;
            }
            $i++;
        }
        //保持剩余内容
        ob_start();
        fpassthru($file);
        fclose($file);
        file_put_contents($fn, ob_get_clean());
        return $list;
    }
    /**
     * 自动评论添加
     * @param  [type] $list array(array(1,3,"guoxu","i love you"),array(1,8,"sean","i love you")) //视频ID 用户id 用户名 评论内容
     * @return [type]       [description]
     */
    public function auto_comment($list){
        $url=self::API_URL."api.php/comment/add";
        foreach ($list as $value) {
            if(count($value)!=4){
                continue;
            }

            $data['videoid']    =   $value[0];
            $data['userid']     =   $value[1];
            $data['username']   =   $value[2];
            $data['content']    =   $value[3];
            $data['country']    =   "BR";

            $res = $this->curlrequest($url,$data);
        }
    }

    /**
     * 点赞自动添加
     * @param  [type] $list array(array(1,3,"BR"),array(1,8,"BR")) //视频ID 用户id 国家
     * @return [type]       [description]
     */
    public function auto_vlike($list){
        $url=self::API_URL."api.php/user/like";
        foreach ($list as $value) {
            if(count($value)!=3){
                continue;
            }

            $data['videoid']    =   $value[0];
            $data['userid']     =   $value[1];
            $data['country']    =   $value[2];

            $res = $this->curlrequest($url,$data);
        }
    }

    /**
     * 自动给新视频添加 通用评论（2分钟内上传的视频）
     *
     * @param   $num        默认添加1~num个评论
     * @param   $s          默认120s内的数据
     * @return [type]       [description]
     */
    public function auto_commom($num=1,$s=120){
        $db = $this->DB();
        $etime = time()-120;
        $stime = $etime-$s;
        $list = $db->findAllBySql("SELECT id, userid FROM `video` WHERE status=1 AND `createtime`>".$stime." AND `createtime`<".$etime);
        $ids=array();

        if($list){
            foreach ($list as $value) {
                //只评论第一个视频
                $min = $db->findBySql("SELECT MIN(id) AS id FROM `video` WHERE status=1 AND userid=".$value['userid']);
                if($min['id']==$value['id']){
                    $this->auto_commom_one($value['id'],$num);
                    $ids[]=$value['id'];
                }
            }
        }
        return $ids;
    }
    //随机添加1~num个评论
    public function auto_commom_one($videoid,$num){
        $k=rand(1,$num);
        $data=array();
        for ($i=0; $i < $k; $i++) {
            $tmp=array();
            $user = array_rand($this->comment_user,1);
            $user = $this->comment_user[$user];
            $content = array_rand($this->comment,1);
            $content = $this->comment[$content];
            $tmp[0]    =   $videoid;
            $tmp[1]    =   $user['id'];
            $tmp[2]    =   $user['name'];
            $tmp[3]    =   $content;
            $data[]=$tmp;
        }
        $res = $this->auto_comment($data);
    }

    /**
     * 自动给新视频添加 赞（2分钟内上传的视频）
     *
     * @param   $num        默认添加1~num个评论
     * @param   $s          默认120s内的数据
     * @param   $api        是否调用API操作
     * @return [type]       [description]
     */
    public function auto_add_like($num=1,$s=120,$api=true){
        $db = $this->DB();
        $etime = time()-120;
        $stime = $etime-$s;
        $list = $db->findAllBySql("SELECT id FROM `video` WHERE status=1 AND `createtime`>".$stime." AND `createtime`<".$etime);
        $ids=array();
        if($list){
            foreach ($list as $value) {
                $this->auto_like_one($value['id'],$num,$api);
                $ids[]=$value['id'];
            }
        }
        return $ids;
    }
    //随机添加1~num个赞
    public function auto_like_one($videoid,$num,$api=true){
        $k=rand(1,$num);
        $data=array();
        for ($i=0; $i < $k; $i++) {
            $tmp=array();
            $user = array_rand($this->comment_user,1);
            $user = $this->comment_user[$user];
            $tmp[0]    =   $videoid;
            $tmp[1]    =   $user['id'];
            $tmp[2]    =   'BR';
            $data[]=$tmp;
        }
        if($api){
            $res = $this->auto_vlike($data);
        }else{
            $db = $this->DB();
            $inc=0;
            $time = time();
            foreach ($data as $row) {
                $res = $db->findBySql("SELECT count(*) as num FROM `vlike` WHERE videoid={$row[0]} AND userid = {$row[1]}");
                if($res['num']==0){
                    $vid = $db->insert("INSERT INTO `vlike` (`videoid`, `userid`, `country`, `createtime`) VALUES ('".$row[0]."', '".$row[1]."', '".$row[2]."', '".$time."')");
                    $inc++;
                }
            }
            $db->update("update `video` set `likecount` = `likecount`+".$inc." WHERE `id` = ".$videoid);
        }
    }


    /**
     * 自动审核视频
     *
     * @return [type]       [description]
     */
    public function auto_check_one(){
        $db = $this->DB();
        $row = $db->findBySql("SELECT id FROM `video` WHERE status=0 ");
        $ids=array();
        if($row){
            $maxOrder = $db -> findBySql("SELECT MAX(`showorder`) as maxorder FROM `video` WHERE `status` = 1 AND `ishot` = 1 AND `orderfixed` = 0");
            if ($maxOrder) {
                $maxOrder = intval($maxOrder['maxorder']) + 1;
                $db->update("UPDATE `video` SET `status` = '1', `showorder` = ".$maxOrder.",`createtime`= ".time()." WHERE `id` =".$row['id']);
                $ids[]=$row['id'];
            }
        }
        return $ids;
    }


    /**
     * domo
     * @return [type] [description]
     */
    function domo(){
        $list = $this->getNumLine("/root/comment.txt",4);
        if(!empty($list)){
            $this->auto_comment($list);
            echo date("Y-m-d H:i")."-comment ok\r\n";
        }

        $list = $this->getNumLine("/root/vlike.txt",4);
        if(!empty($list)){
            $this->auto_vlike($list);
            echo date("Y-m-d H:i")."-vlike ok\r\n";
        }

        //自动添加通用评论
        $ids = $this->auto_commom();
        if(!empty($ids)){
            echo date("Y-m-d H:i")."-auto_comment videoid = (".implode(',', $ids).") ok\r\n";
        }
    }

    public function curlrequest($url,$data,$method='POST'){
        //post参数格式化
        $post = array();
        $post[]="MGO_TOKEN=ac9698f65294e3bd935e5d05d360e089";
        foreach ($data as $key => $value) {
            $post[]=$key."=".urlencode($value);
        }
        $post=implode("&",$post);

        $header = array();
        $header[] = "X-HTTP-Method-Override: $method";

        $ch = curl_init(); //初始化CURL句柄
        curl_setopt($ch, CURLOPT_URL, $url); //设置请求的URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); //设为TRUE把curl_exec()结果转化为字串，而不是直接输出
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); //设置请求方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);//设置提交的字符串
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);//设置HTTP头信息

        $document = curl_exec($ch);//执行预定义的CURL
        if(curl_errno($ch)){
          echo 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);
        return $document;
    }
}

class mysql {
    public $pdo=null;

    public function __construct() {
        $this->pdo = $this->connect();
    }
    /**
     * 主库连接
     * @param  [type] $config [description]
     * @return [type]         [description]
     */
    public function connect() {
        try {

            $dsn = "mysql:host=127.0.0.1;dbname=mideo";
            $pdo = new \PDO($dsn, 'root', '');
            //$pdo = new \PDO($dsn, 'root', '');
            $pdo->query("SET NAMES utf8");
            return $pdo;
        } catch ( \PDOException $e ) {
            throw new \Exception ( $e->getMessage(),$e->getCode());
        }
    }

    /**
     * 插入数据，返回最后插入行的ID或序列值
     * @param string $tableName 表名
     * @param array $data
     * @return int id;
     */
    public function insert($sql) {
        $this->_exec( $sql );
        return $this->pdo->lastInsertId();
    }

    public function update($sql) {
        return $this->_exec( $sql );
    }
    /**
     * 执行sql返回结果集
     * @param string $sql;
     * @return array or null
     */
    public function findAllBySql($sql){
        $res = $this->_query($sql);
        $res = $res->fetchAll(\PDO::FETCH_ASSOC);
        if(!$res) return null;
        return $res;
    }

    /**
     * 执行sql返回单条结果
     * @param string $sql;
     * @return array or null
     */
    public function findBySql($sql){
        $res = $this->_query( $sql );
        $res = $res->fetch(\PDO::FETCH_ASSOC);
        if(!$res) return null;
        return $res;
    }

    /**
     * 执行查询类sql
     * @param       string $sql
     * @return      pdo->res
     * @throws      CException
     */
    public function _query($sql){
        try {
            $res = $this->pdo->query( $sql );
            return $res;
        } catch ( \PDOException $e ) {
            throw new \Exception ( $e->getMessage(),$e->getCode());
        }
    }
    /**
     * 执行非查询类sql
     * @param       string $sql
     * @return      pdo->res
     * @throws      CException
     */
    public function _exec($sql){
        try {
            $res = $this->pdo->exec( $sql );
            return $res;
        } catch ( \PDOException $e ) {
            throw new \Exception ( $e->getMessage(),$e->getCode());
        }
    }
    /**
     * value分析
     * @param mixed $value
     * @return string
     */
    public function parseValue($value) {
        if(is_string($value)) {
            $value =  '\''.$this->escapeString($value).'\'';
        }elseif(isset($value[0]) && is_string($value[0]) && strtolower($value[0]) == 'exp'){
            $value =  $this->escapeString($value[1]);
        }elseif(is_array($value)) {
            $value =  array_map(array($this, 'parseValue'),$value);
        }elseif(is_bool($value)){
            $value =  $value ? '1' : '0';
        }elseif(is_null($value)){
            $value =  'null';
        }
        return $value;
    }
    /**
     * SQL指令安全过滤
     * @access public
     * @param string $str  SQL字符串
     * @return string
     */
    public function escapeString($str) {
        return addslashes($str);
    }

    /**
     * 释放
     */
    public function close() {
        $this->pdo=null;
    }
}


