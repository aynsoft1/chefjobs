<?php
/**********************************************************
**********# Name          : Shambhu Prasad Patnaik  #**********
**********# Company       : Aynsoft Pvt. Ltd.   #**********
**********# Copyright (c) www.aynsoft.com 2005  #**********
**********************************************************/
class VideoportalPlayer
{
    var $width  = 425;
    var $height = 350;
   

    function _setReplace($posting, $results, $tmpl_player, $index=1)
    {
        $posting;
        foreach($results as $item)
        $posting = str_replace($item[0], str_replace('[ID]', $item[$index], $tmpl_player), $posting);
        return $posting;
    }
    
    
    function getYoutube($posting)
    {

        #@http://www.youtube.com/watch?v=fvjT5FrWpS0
       /* $tmpl_player= '<object width="'.$this->width.'" height="'.$this->height.'">
        <param name="movie" value="http://www.youtube.com/v/[ID]"></param>
        <param name="wmode" value="transparent"></param>
        <embed src="http://www.youtube.com/v/[ID]" type="application/x-shockwave-flash" wmode="transparent" width="'.$this->width.'" height="'.$this->height.'"></embed>
        </object>';
		*/
		 $tmpl_player='<iframe width="420" height="315" src="https://www.youtube.com/embed/[ID]?controls=0" frameborder="0" allowfullscreen>
		               </iframe>';
        preg_match_all('/@http:\/\/(www\.)?youtube\.com\/watch\?v=([\d\w-_]+)/', $posting, $results, PREG_SET_ORDER);
        if($results)
            $posting = $this->_setReplace($posting, $results, $tmpl_player, $index=2);
		elseif(preg_match('#youtu.be/([\d\w-_]+)#i', $posting, $results))
        return   str_replace('[ID]', $results[1], $tmpl_player);

        return $posting;
    }
    function getIshareRediff($posting)
    {
        #@http://ishare.rediff.com/filevideo.php?id=56982
        //$tmpl_player= '<embed src="http://ishare.rediff.com/images/player20071001.swf" FlashVars="videoURL=http://ishare.rediff.com/embedcodeplayer_config.php?content_id=[ID]" name="aplayer" allowScriptAccess="always" allowFullScreen="true" type="application/x-shockwave-flash" height="'.$this->height.'"  width="'.$this->width.'" ></embed>';
        $tmpl_player= '<embed src="http://ishare.rediff.com/images/player20071001.swf" FlashVars="videoURL=http://ishare.rediff.com/embedcodeplayer_config.php?content_id=[ID]" name="aplayer" allowScriptAccess="always" allowFullScreen="true" type="application/x-shockwave-flash" height="322"  width="400" ></embed>';
        preg_match_all('/@http:\/\/ishare\.rediff\.com\/filevideo\.php\?id=([\d-]+)/', $posting, $results, PREG_SET_ORDER);
        if($results)
            $posting = $this->_setReplace($posting, $results, $tmpl_player, $index=1);
        return $posting;
    }

    
    function getGoogle($posting)
    {
        #@http://video.google.com/googleplayer.swf?docId=-2205197918273229623
        #@http://video.google.com/videoplay?docid=-2205197918273229623
        $tmpl_player= '<object type="application/x-shockwave-flash" data="http://video.google.com/googleplayer.swf?docId=[ID]" height="'.$this->height.'" width="'.$this->width.'">
        <param name="movie" value="http://video.google.com/googleplayer.swf?docId=[ID]">
        </object>';
        preg_match_all('/@http:\/\/video\.google\.com\/(googleplayer\.swf|videoplay)\?doc[Ii]d=([\d-]+)/', $posting, $results, PREG_SET_ORDER); #funny one google :)
        if($results) 
            $posting = $this->_setReplace($posting, $results, $tmpl_player, $index=2);
        return $posting;
    }


    function getClipfish($posting)
    {
        #@http://www.clipfish.de/player.php?videoid=MTk0ODU4fDEw
        $tmpl_player = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="'.$this->width.'" height="'.$this->height.'" id="player" align="middle">
        <param name="allowScriptAccess" value="sameDomain" />
        <param name="movie" value="http://www.clipfish.de/videoplayer.swf?as=0&videoid=[ID]&r=1" />
        <param name="wmode" value="transparent">  
        <param name="quality" value="high" />
        <param name="bgcolor" value="#999999" />
        <embed src="http://www.clipfish.de/videoplayer.swf?as=0&videoid=[ID]&r=1" quality="high" bgcolor="#999999" width="'.$this->width.'" height="'.$this->height.'" name="player" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>
        </object>';
        preg_match_all('/@http:\/\/www\.clipfish\.de\/player\.php\?videoid=([\d\w-_]+)/', $posting, $results, PREG_SET_ORDER);
        if($results) 
            $posting = $this->_setReplace($posting, $results, $tmpl_player);
        return $posting;
    }
    

    function getMyvideo($posting)
    {
        #@http://www.myvideo.de/watch/65741
        $tmpl_player = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="'.$this->width.'" height="'.$this->height.'"><param name="movie" value="http://www.myvideo.de/movie/[ID]"></param><embed src="http://www.myvideo.de/movie/[ID]" width="'.$this->width.'" height="'.$this->height.'" type="application/x-shockwave-flash"></embed></object>';
        preg_match_all('/@http:\/\/www\.myvideo\.de\/watch\/([\d]+)/', $posting, $results, PREG_SET_ORDER);
        if($results)
            $posting = $this->_setReplace($posting, $results, $tmpl_player);
        return $posting;
    }

    
    function getVideotube($posting)
    {
        #@http://videotube.de/watch/31603
        $tmpl_player = '<object type="application/x-shockwave-flash" data="http://www.videotube.de/ci/flash/videotube_player_4.swf?videoId=[ID]&svsf=0&lang=german&host=www.videotube.de" width="'.$this->width.'" height="'.$this->height.'" wmode="transparent"><param name="movie" value="http://www.videotube.de/ci/flash/videotube_player_4.swf?videoId=[ID]&svsf=0&lang=german&host=www.videotube.de" /></object>';
        preg_match_all('/@http:\/\/videotube\.de\/watch\/([\d]+)/', $posting, $results, PREG_SET_ORDER);
        if($results)
            $posting = $this->_setReplace($posting, $results, $tmpl_player);
        return $posting;
    }

}
?>