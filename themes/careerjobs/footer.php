<?
$social_footer_button='';
 if(tep_not_null(MODULE_FACEBOOK_FOOTER_LINK))
 $social_footer_button.='<li>
                        <a href="'.MODULE_FACEBOOK_FOOTER_LINK.'
                        " class="icoFacebook" title="Facebook"><i class="bi bi-facebook"></i>
                        </a>
                        </li>';

 if(tep_not_null(MODULE_LINKEDIN_FOOTER_LINK))
 $social_footer_button.='<li>
                        <a href="'.MODULE_LINKEDIN_FOOTER_LINK.'
                        " class="icoLinkedin" title="Linkedin"><i class="bi bi-linkedin"></i>
                        </a>
                        </li>';

 if(tep_not_null(MODULE_TWITTER_FOOTER_LINK))
 $social_footer_button.='<li>
                        <a href="'.MODULE_TWITTER_FOOTER_LINK.'
                        " class="icoTwitter" title="Twitter"><i class="bi bi-twitter"></i>
                        </a>
                        </li>';

 if(tep_not_null(MODULE_GOOGLEPLUS_FOOTER_LINK))
 $social_footer_button.='<li>
                        <a href="'.MODULE_GOOGLEPLUS_FOOTER_LINK.'
                        " class="icoGoogle-plus" title="Google-plus"><i class="bi bi-google"></i>
                        </a>
                        </li>';
define('FOOTER_HTML',
    '
<div class="container text-left mt-5">
	<div class="row">
        <div class="col-md-12 mt-4 mb-4 text-center">
                <a class="mx-3" href="'.getPermalink(FILENAME_ABOUT_US).'">'.INFO_TEXT_F_ABOUT_US.'</a>
                <a class="mx-3" href="'.getPermalink(FILENAME_TERMS).'">'.INFO_TEXT_F_TERMS.'</a>
                <a class="mx-3" href="'.getPermalink(FILENAME_PRIVACY).'">'.INFO_TEXT_F_PRIVACY.'</a>
                <a class="mx-3" href="'.getPermalink(FILENAME_ARTICLE).'">'.INFO_TEXT_F_ARTICLE.'</a>
                <a class="mx-3" href="'.getPermalink(FILENAME_SITE_MAP).'">'.INFO_TEXT_F_SITE_MAP.'</a>
                <a class="mx-3" href="'.getPermalink(FILENAME_CONTACT_US).'">'.INFO_TEXT_F_CONTACT.'</a>
        </div>
        <div class="col-md-12 text-center mb-4">
            <p class="p-2">&copy; '.date("Y").'<a href="'.tep_href_link('').'" style="border-bottom: 1px dashed;"> '.SITE_TITLE.'.</a>&nbsp;All rights reserved</p>
			<ul class="social-network social-circle">
                    '.$social_footer_button.'
                    <li><a href="'.tep_href_link(FILENAME_INDUSTRY_RSS).'" class="icoRss" title="Rss"><i class="bi bi-rss-fill"></i></a></li>
            </ul>
        </div>
</div>
</div>


	<script src="'.HOST_NAME.'jscript/jquery-3.5.1.min.js"></script>
    <script src="'.HOST_NAME.'jscript/bootstrap.bundle.min.js"></script>
    <script src="'.HOST_NAME.'jscript/dropdown.js"></script>
    <script src="'.HOST_NAME.'jscript/menu-active.js"></script>
    <script src="'.HOST_NAME.'jscript/skeleton.js"></script>
    <!--THis page.js is used for ajax or jquery delete operation-->
    <script src="'.tep_href_link(PATH_TO_LANGUAGE.$language."/jscript/page.js").'"></script>
    <!--THis timout js is used for timout the error or success message-->
    <script src="'.HOST_NAME.'jscript/error_success_message_timeout.js"></script>
    <script src="'.HOST_NAME.'jscript/cookiealert.js"></script>
	<!--<script src="'.HOST_NAME.'jscript/fix.js"></script>-->
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" 
        integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" 
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    '.tep_get_google_analytics_code().'

    <script src="'.HOST_NAME.'jscript/notification.js"></script>

</body>

</html>'
);

?>