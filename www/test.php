<?php
require_once(__DIR__.'/../code/config.php');

db::connect($_CONFIG['db']);
unset($_CONFIG['db']);


// $mail=new \tools\mail();
// $mail->from='众网信通'; //称呼(来自于谁发的)
// $mail->setTitle('标题');
// $mail->setContent('内容');
// $mail->addAttachment(__DIR__.'/interface.php');//绝对地址
// // $mail->addAttachment(__DIR__.'/../note.txt');//绝对地址
// $mail->sendmail('1031850847@qq.com');

// $a=array("core/reset.css","core/base.css","core/layout.css","modules/site_icon.css","modules/site_btn.css","modules/site_form.css","modules/site_head_v3.css","modules/site_banner.css","modules/site_footer.css","modules/mod_guide.css","modules/mod_piclist.css","modules/mod_txtlist.css","modules/mod_title.css","modules/mod_search.css","modules/mod_pop.css","modules/mod_focus.css","modules/mod_ad.css","modules/mod_page.css","modules/mod_tab.css","modules/mod_others.css","modules/mod_login.css","modules/mod_autocomplete.css","modules/mod_custom_service.css","modules/mod_raise.css","modules/page_detail.css","modules/mod_response.css","pages/financial.css","pages/personalCenter.css","pages/houseSearch.css","modules/404.css","pages/join.css","pages/business.css","pages/appDownload.css","pages/housingDetail.css","pages/fund.css","pages/ask.css","pages/wiki.css","pages/immigrant.css","pages/country.css","pages/guide.css","pages/uoolu_plus.css","pages/entrust.css","pages/loan.css","pages/wd.css","pages/video.css","vendors/swiper.min.css","vendors/layer.css");

// foreach($a as $v){
// 	$data=http::curl('https://www.uoolu.com/css/'.$v);
// 	echo $v.PHP_EOL;
// 	file::put('cache/1/'.$v,$data);
// }
