<?php
/**
 * Created by PhpStorm.
 * User: changtao
 * Date: 2018/1/15
 * Time: 9:44
 * descript: 此工具上传函数，相当于上传的是已经封装好的UploadedFile类，此类获取的就是通过Request中获取的相关信息file信息打包好的
 * 其中key值就是file ->UploadedFile文件类
 */

namespace Extend\Util;


use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UpLoad
{
//要配置的内容
    private $path = "";
    public  static $chunk_file = "chunk";//"";
    private $chunk_url = "";//"";
    public  static $merge_file = 'merge';// "/merge";
    public  static $file_struct = 'file.xml';// "/file保存的路径";
    private $merge_url = '';// "/merge";
    private $parent_file_name = '';
    private $parent_file_size = "";
    private $parent_file_id = "";
    private $parent_file_mask;
    private $parent_file_type = "mp4";

    private $chunk_tmp_name = "";
    private $chunk_nums = 0;
    private $chunk_index  = 0;
    private $chunk_tmp_path = "";
    private $chunk_size = 0;
    private $error = 0;
    private $error_msg = "";
    private $allow_type = array('mp4');
    private $allow_parent_size = 5*1024*1024*1024;
    private $allow_chunk_size = 5*1024*1024;
    /**
     * @var string|UploadedFile
     */
    private $file = '';

    //----------------error错误数据
    //------------chunk 错误
    const ERROR_CHUNK_INDEX = 11;           //校验chunk的上传是否正确
    const ERROR_CHUNK_SIZE = 12;            //分块超过最大限制
    const ERROR_CHUCK_MOVE = 13;            //移动文件出错
    //-----------父文件错误
    const ERROR_PARENT_PATH_EMPTY = 21;     //父目录为空
    const ERROR_PATENT_PATH_CREAT = 22;     //创建父目录失败
    const ERROR_PATENT_SIZE = 23;           //文件超过最大上传限制
    const ERROR_PARENT_TYPE = 24;           //文件类型错误

//    const ERROR_CHUNK_INDEX = 1;
//    const ERROR_CHUNK_INDEX = 1;
//    const ERROR_CHUNK_INDEX = 1;

    /**
     * UpLoad constructor.
     * 构造chunk的上传文件对象
     *
     * @param string $path
     * @param array $parent_file
     * @param UploadedFile $chunk
     */
    public function __construct($path,$parent_file,$chunk)
    {
        $this->path  = $path; // 这里得到的是app目录的绝对路

         $this->setChunkUrl($parent_file['upload_path']);
         $this->setMergUrl($parent_file['upload_path']);
         $this->parent_file_id = $parent_file['file_id'];
         $this->parent_file_mask = $parent_file['md5'];
         $this->parent_file_name = $parent_file['name'];
         $this->parent_file_size = $parent_file['size'];
         $this->parent_file_type = $parent_file['ext'];
         $this->parent_upload_path = $parent_file['upload_path'];
        if($chunk->getError() == 0 && !isset($parent_file['chunks']) && $chunk->getSize()==$parent_file['size'])
        {
            $this->chunk_nums = 1;
            $this->chunk_index = 0;
        }else{
            $this->chunk_nums = $parent_file['chunks'];
            $this->chunk_index = $parent_file['chunk'];
        }

         $this->chunk_tmp_name = $chunk->getFilename();//临时文件名称
         $this->chunk_size = $chunk->getSize();
         $this->chunk_tmp_path = $chunk->getPathname();
         $this->chunk_jsmask = $parent_file['chunk_md5'];
         $this->file = $chunk;
         $this->chunk_phpmask = md5_file($this->file->getPathname());
    }
    public function setChunkUrl($file_name)
    {
        $this->chunk_url = rtrim($this->path,'/').'/'.self::$chunk_file.'/'.$file_name;
        self::checkForCreat($this->chunk_url);
    }
    public function setMergUrl($file_name)
    {
        $this->merge_url = rtrim($this->path,'/').'/'.self::$merge_file.'/'.$file_name;
        self::checkForCreat($this->merge_url);
    }
    /**
     * 校验chunk分块是否合法
     * @return bool
     */
    private function checkChunk()
    {
        if( $this->chunk_nums > 0){
            if($this->chunk_index >= 0){
                return true;
            }
        }else{
            $this->setError(self::ERROR_CHUNK_INDEX);
            return false;
        }
    }

    /**
     *
     *校验父文件的路径是否创建
     *
     * @return bool
     */
    private function checkParentFilePath()
    {
        if (empty($this->path)) {
            $this->setError(self::ERROR_PARENT_PATH_EMPTY);
            return false;
        }

        if (!file_exists($this->path) || !is_writable($this->path)) {
            if (!@mkdir($this->path, 0755)) {
                $this->setError(self::ERROR_PATENT_PATH_CREAT);
                return false;
            }
        }
        return true;
    }

    /**
     *
     * 校验文件大小是否规范
     *
     * @return bool
     */
    private function checkFileSize(){
        if ($this->allow_parent_size < $this->parent_file_size) {
            $this->setError(self::ERROR_PATENT_SIZE);
            return false;
        }else if ($this->allow_chunk_size < $this->chunk_size) {
            $this->setError(self::ERROR_CHUNK_SIZE);
            return false;
        }else{
            return true;
        }
    }
    /**
     *
     * 校验文件类型是否符合规范
     *
     * @return bool
     */
    private function checkFileType(){
        if (in_array(strtolower($this->parent_file_type), $this->allow_type)) {
            return true;
        }else{
            $this->setError(self::ERROR_PARENT_TYPE);
            return false;
        }
    }
    /**
     *
     * 设置Error
     *
     * @param int $error
     * @return bool
     */
    private function setError($error)
    {
        $this->error = $error;
        $this->setErrorMsg();
        return true;
    }

    /**
     *
     * 设置error的消息
     *
     * @return bool
     */
    private function setErrorMsg()
    {
        $str = '';
        switch ($this->error) {
            case self::ERROR_CHUNK_INDEX:
                $str.= "文件分块有误";
                break;
            case self::ERROR_CHUNK_SIZE:
                $str.= "分块超过最大限制";
                break;
            case self::ERROR_CHUCK_MOVE:
                $str.= "移动文件出错";
                break;
            case self::ERROR_PARENT_PATH_EMPTY:
                $str.= "上传文件目录为空";
                break;
            case self::ERROR_PATENT_PATH_CREAT:
                $str.= "创建文件目录失败";
                break;
            case self::ERROR_PATENT_SIZE:
                $str.= "文件超过最大上传限制";
                break;
            case self::ERROR_PARENT_TYPE:
                $str.= "文件类型";
                break;
            default:
                $str .= "未知错误";
        }
        $this->error_msg = $str;
        return true;
    }
    public function getErrorMsg()
    {
        return $this->error_msg;
    }

    private function moveLoadChunk()
    {
        $path = rtrim($this->chunk_url, '/');
        $this->file->move($path,$this->chunk_index);
    }
    /**
     *
     * 查找Chunk是否存在
     *
     * @param array $chunk 块信息
     * @return int 是否存在
     */
    public function findChunk($chunk = array())
    {
        $target =  $this->merge_url.'/'.$chunk['file_name'].'/'.$chunk['chunk_index'];
        if(file_exists($target) && filesize($target) == $chunk['size']){
            return 1;
        }else{
            return 0;
        }
    }

    /**
     *
     * 上传分块
     *
     * @param array $file
     * @param UploadedFile $chunk
     * @return bool
     */
    public function uploadChunk()
    {
        //-------------校验chunk是否正确---------------
        if(!$this->checkChunk()){
            return false;
        }
        //-------------校验父文件的路径是否新建---------------
        if (!$this->checkParentFilePath()){
            return false;
        }
        //--------------校验文件的尺寸-----------------
        if(!$this->checkFileSize()) {
            return false;
        }
        //--------------校验文件类型-----------------
        if(!$this->checkFileType()) {
            return false;
        }
        //--------------移动加载的块到制定文件夹下-----------------
        $this->moveLoadChunk();
        //-------------保存文件信息到xml中-------------
        $this->updateFileChunkInfoToXml();
        return true;
    }
    public function updateFileChunkInfoToXml()
    {
        if(!file_exists($this->chunk_url."/".self::$file_struct))
        {
            $dom = new \DOMDocument('1.0','utf-8');
            $file = $dom->createElement('file');//创建普通节点：booklist
            $dom->appendChild($file);//把booklist节点加入到DOM文档中
            $file_id = $dom->createAttribute('id');
            $file_id_text = $dom->createTextNode($this->parent_file_id);
            $file_id->appendChild($file_id_text);
            $file_name = $dom->createAttribute('name');
            $file_name_text = $dom->createTextNode($this->parent_file_name);
            $file_name->appendChild($file_name_text);
            $file_chunks = $dom->createAttribute('chunks');
            $file_chunks_text = $dom->createTextNode($this->chunk_nums);
            $file_chunks->appendChild($file_chunks_text);
            $file_size = $dom->createAttribute('size');
            $file_size_text = $dom->createTextNode($this->parent_file_size);
            $file_size->appendChild($file_size_text);
            $file_type = $dom->createAttribute('type');
            $file_type_text = $dom->createTextNode($this->parent_file_type);
            $file_type->appendChild($file_type_text);
            $file->appendChild($file_id);
            $file->appendChild($file_name);
            $file->appendChild($file_type);
            $file->appendChild($file_chunks);
            $file->appendChild($file_size);
            $file->appendChild($file_type);
            $chunk = $dom->createElement('chunk');//创建普通节点：booklist
            $chunk_id = $dom->createAttribute('id');
            $chunk_id_text = $dom->createTextNode($this->chunk_index);
            $chunk_id->appendChild($chunk_id_text);
            $chunk_jsmd5 = $dom->createAttribute('jsMask');
            $chunk_jsmd5_text = $dom->createTextNode($this->chunk_jsmask);
            $chunk_jsmd5->appendChild($chunk_jsmd5_text);
            $chunk_phpmd5 = $dom->createAttribute('phpMask');
            $chunk_phpmd5_text = $dom->createTextNode($this->chunk_phpmask);
            $chunk_phpmd5->appendChild($chunk_phpmd5_text);
            $chunk_size = $dom->createAttribute('size');
            $chunk_size_text = $dom->createTextNode($this->chunk_size);
            $chunk_size->appendChild($chunk_size_text);
            $chunk->appendChild($chunk_id);
            $chunk->appendChild($chunk_jsmd5);
            $chunk->appendChild($chunk_phpmd5);
            $chunk->appendChild($chunk_size);
            $file->appendChild($chunk);
            $dom->save($this->chunk_url."/file.xml");
        }else{
            $dom = new \DOMDocument();
            $dom->load($this->chunk_url."/file.xml");
            $file = $dom->getElementsByTagName('file');
            $file = $file->item(0);
            $chunk = $dom->createElement('chunk');//创建普通节点：booklist
            $chunk_id = $dom->createAttribute('id');
            $chunk_id_text = $dom->createTextNode($this->chunk_index);
            $chunk_id->appendChild($chunk_id_text);
            $chunk_jsmd5 = $dom->createAttribute('jsMask');
            $chunk_jsmd5_text = $dom->createTextNode($this->chunk_jsmask);
            $chunk_jsmd5->appendChild($chunk_jsmd5_text);
            $chunk_phpmd5 = $dom->createAttribute('phpMask');
            $chunk_phpmd5_text = $dom->createTextNode($this->chunk_phpmask);
            $chunk_phpmd5->appendChild($chunk_phpmd5_text);
            $chunk_size = $dom->createAttribute('size');
            $chunk_size_text = $dom->createTextNode($this->chunk_size);
            $chunk_size->appendChild($chunk_size_text);
            $chunk->appendChild($chunk_id);
            $chunk->appendChild($chunk_jsmd5);
            $chunk->appendChild($chunk_phpmd5);
            $chunk->appendChild($chunk_size);
            $file->appendChild($chunk);
            $dom->save($this->chunk_url."/file.xml");
        }
    }
    public function chunksMerge($uniqueFileName, $chunksTotal, $fileExt){
        $targetDir = $this->path.'/'.$uniqueFileName;
        //检查对应文件夹中的分块文件数量是否和总数保持一致
        if($chunksTotal > 1 && (count(scandir($targetDir)) - 2) == $chunksTotal){
            //同步锁机制
            $lockFd = fopen($this->path.'/'.$uniqueFileName.'.lock', "w");
            if(!flock($lockFd, LOCK_EX | LOCK_NB)){
                fclose($lockFd);
                return false;
            }

            //进行合并
            $this->fileType = $fileExt;
            $finalName = $this->path.'/'.($this->setOption('newFileName', $this->proRandName()));
            $file = fopen($finalName, 'wb');
            for($index = 0; $index < $chunksTotal; $index++){
                $tmpFile = $targetDir.'/'.$index;
                $chunkFile = fopen($tmpFile, 'rb');
                $content = fread($chunkFile, filesize($tmpFile));
                fclose($chunkFile);
                fwrite($file, $content);

                //删除chunk文件
                unlink($tmpFile);
            }
            fclose($file);
            //删除chunk文件夹
            rmdir($targetDir);
            unlink($this->path.'/'.$uniqueFileName.'.tmp');

            //解锁
            flock($lockFd, LOCK_UN);
            fclose($lockFd);
            unlink($this->path.'/'.$uniqueFileName.'.lock');

            return $this->newFileName;

        }
        return false;
    }

    /**
     * 设置随机文件名
     *
    **/
    public static function createRandFileName(){
       /* $user_64 = UtilTool::hex10to64(1);
        $time = time()-strtotime('2012-4-21');
        $time_64 = UtilTool::hex10to64($time);
        $rand_64 = UtilTool::random(3);
        $file_name = $user_64.$time_64.$rand_64;*/
       $upload_path = date('Ymd').'_'.md5(uniqid());
        return $upload_path;
    }
    public static function checkForCreat($file_path)
    {

       if(!file_exists($file_path))
        {
            if(!mkdir($file_path,0755,true)){
                throw new Exception("创建文件失败");
            }
        }
        if(!is_writable($file_path))
        {
            throw new Exception("文件不可写");
        }
    }
    public static function checkUploadFinished($path,$file_info)
    {
        $file_path = $path.'/'.self::$chunk_file.'/'.$file_info->getSaveName();
        if(file_exists($file_path))
        {
            if(file_exists($file_path.'/'.self::$file_struct))
            {
                $dom = new \DOMDocument();
                $dom->load($file_path.'/'.self::$file_struct);
                $files = $dom->getElementsByTagName('file');
                $file = $files->item(0);
                $chunks = $file->getElementsByTagName('chunk');
                $total_size = intval($file->getAttribute('size'));
                $total_chunks =intval($file->getAttribute('chunks'));
                $chunks_statistic = array();
                foreach ($chunks as $k => $v)
                {
                    $chunks_statistic["{$v->getAttribute('id')}"] =  $file->getAttribute('size');
                    $total_size = $total_size - intval($file->getAttribute('size'));
                    $total_chunks--;
                }
                if(count($chunks_statistic) != $total_chunks || $total_size !== 0 || $total_chunks !==0)
                {
                    return false;
                }else{
                    //修改数据库上传完成
                    return true;
                }
            }
        }
        return false;
    }
}