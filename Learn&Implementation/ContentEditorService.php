<?php

/*
 * ContentEditorService
 */

namespace App\Services;

use DOMDocument;

class ContentEditorService
{   

    public function __construct(){
        $this->content = '';
        $this->folder_location = 'base64_images';
        $this->dir_name = '';
    }


    public function base64ContentDecode(){

        $htmlString = $this->content;

        $doc = new DOMDocument();
        $doc->loadHTML($htmlString);
        $tags = $doc->getElementsByTagName('img');
           
        foreach ($tags as $tag) {
           $oldSrc = $tag->getAttribute('src');
           $newScrURL = $this->saveBase64Data($oldSrc);
           $tag->setAttribute('src', $newScrURL);
        } 
        $body = $doc->documentElement->lastChild;
        $htmlString = $doc->saveHTML($body);
        $htmlString = str_replace('<body>', '', $htmlString);
        $htmlString = str_replace('</body>', '', $htmlString);

        return $htmlString;
    }

    public function saveBase64Data($data)
    {   
        $type = 'png';
        
        if(strpos($data,'data:image/jpeg;base64') !== false){
            $type = 'jpeg';
            $img = str_replace('data:image/jpeg;base64,', '', $data);
        }else if(strpos($data,'data:image/jpg;base64') !== false){
            $type = 'jpg';
            $img = str_replace('data:image/jpg;base64,', '', $data);
        }else if(strpos($data,'data:image/png;base64') !== false){
            $type = 'png';
            $img = str_replace('data:image/png;base64,', '', $data);
        }else{
            return $data;
        }

        $img = str_replace(' ', '+', $img);

        $data = base64_decode($img);
        $file_name = 'temp_64_'.uniqid().strtotime(date('Y-m-d H:i:s')) .'.'.$type;
        $file = $this->dir_name.'/'.$file_name;

        $success = file_put_contents($file, $data);

        $base_path = url('/');

        if(strpos($base_path,'public') === false){
            $base_path = url('/public');
        }

        return $base_path.'/'.$this->folder_location.'/'.$file_name;
    }

    /**
     * 
     * @param String $content - htmlContent
     * @param String $folder_location
     * @return String
     */
    public function getOutput($content,$folder_location = 'base64_images'){
        $this->content = $content;
        $this->folder_location = $folder_location;
        $this->makeFolder();
        return $this->base64ContentDecode($this->content);
    }

    public function makeFolder(){
        $this->dir_name = public_path('/'.$this->folder_location);
        if (!file_exists($this->dir_name)) {
            mkdir($this->dir_name, 0777, true);
        }
    }

}