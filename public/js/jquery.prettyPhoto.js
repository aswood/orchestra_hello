/* ------------------------------------------------------------------------
	Class: prettyPhoto
	Use: Lightbox clone for jQuery
	Author: Stephane Caron (http://www.no-margin-for-errors.com)
	Version: 2.4.3
------------------------------------------------------------------------- */

var $pp_pic_holder;var $ppt;(function(A){A.fn.prettyPhoto=function(W){var E=true;var K=false;var O=[];var D=0;var R;var S;var V;var Y;var F="image";var Z;var M=G();A(window).scroll(function(){M=G();C()});A(window).resize(function(){C();U()});A(document).keypress(function(c){switch(c.keyCode){case 37:if(D==1){return }N("previous");break;case 39:if(D==setCount){return }N("next");break;case 27:L();break}});W=jQuery.extend({animationSpeed:"normal",padding:40,opacity:0.8,showTitle:true,allowresize:true,counter_separator_label:"/",theme:"light_rounded",callback:function(){}},W);if(A.browser.msie&&A.browser.version==6){W.theme="light_square"}A(this).each(function(){var e=false;var d=false;var f=0;var c=0;O[O.length]=this;A(this).bind("click",function(){J(this);return false})});function J(c){Z=A(c);theRel=Z.attr("rel");galleryRegExp=/\[(?:.*)\]/;theGallery=galleryRegExp.exec(theRel);isSet=false;setCount=0;b();for(i=0;i<O.length;i++){if(A(O[i]).attr("rel").indexOf(theGallery)!=-1){setCount++;if(setCount>1){isSet=true}if(A(O[i]).attr("href")==Z.attr("href")){D=setCount;arrayPosition=i}}}X();$pp_pic_holder.find("p.currentTextHolder").text(D+W.counter_separator_label+setCount);C();A("#pp_full_res").hide();$pp_pic_holder.find(".pp_loaderIcon").show()}showimage=function(f,c,j,h,g,d,e){A(".pp_loaderIcon").hide();if(A.browser.opera){windowHeight=window.innerHeight;windowWidth=window.innerWidth}else{windowHeight=A(window).height();windowWidth=A(window).width()}$pp_pic_holder.find(".pp_content").animate({height:g},W.animationSpeed);projectedTop=M.scrollTop+((windowHeight/2)-(h/2));if(projectedTop<0){projectedTop=0+$pp_pic_holder.find(".ppt").height()}$pp_pic_holder.animate({top:projectedTop,left:((windowWidth/2)-(j/2)),width:j},W.animationSpeed,function(){$pp_pic_holder.width(j);$pp_pic_holder.find(".pp_hoverContainer,#fullResImage").height(c).width(f);$pp_pic_holder.find("#pp_full_res").fadeIn(W.animationSpeed,function(){A(this).find("object,embed").css("visibility","visible")});I();if(e){A("a.pp_expand,a.pp_contract").fadeIn(W.animationSpeed)}})};function I(){if(isSet&&F=="image"){$pp_pic_holder.find(".pp_hoverContainer").fadeIn(W.animationSpeed)}else{$pp_pic_holder.find(".pp_hoverContainer").hide()}$pp_pic_holder.find(".pp_details").fadeIn(W.animationSpeed);if(W.showTitle&&hasTitle){$ppt.css({top:$pp_pic_holder.offset().top-22,left:$pp_pic_holder.offset().left+(W.padding/2),display:"none"});$ppt.fadeIn(W.animationSpeed)}}function Q(){$pp_pic_holder.find(".pp_hoverContainer,.pp_details").fadeOut(W.animationSpeed);$pp_pic_holder.find("#pp_full_res object,#pp_full_res embed").css("visibility","hidden");$pp_pic_holder.find("#pp_full_res").fadeOut(W.animationSpeed,function(){A(".pp_loaderIcon").show();a()});$ppt.fadeOut(W.animationSpeed)}function N(c){if(c=="previous"){arrayPosition--;D--}else{arrayPosition++;D++}if(!E){E=true}Q();A("a.pp_expand,a.pp_contract").fadeOut(W.animationSpeed,function(){A(this).removeClass("pp_contract").addClass("pp_expand")})}function L(){$pp_pic_holder.find("object,embed").css("visibility","hidden");A("div.pp_pic_holder,div.ppt").fadeOut(W.animationSpeed);A("div.pp_overlay").fadeOut(W.animationSpeed,function(){A("div.pp_overlay,div.pp_pic_holder,div.ppt").remove();if(A.browser.msie&&A.browser.version==6){A("select").css("visibility","visible")}W.callback()});E=true}function H(){if(D==setCount){$pp_pic_holder.find("a.pp_next").css("visibility","hidden");$pp_pic_holder.find("a.pp_arrow_next").addClass("disabled").unbind("click")}else{$pp_pic_holder.find("a.pp_next").css("visibility","visible");$pp_pic_holder.find("a.pp_arrow_next.disabled").removeClass("disabled").bind("click",function(){N("next");return false})}if(D==1){$pp_pic_holder.find("a.pp_previous").css("visibility","hidden");$pp_pic_holder.find("a.pp_arrow_previous").addClass("disabled").unbind("click")}else{$pp_pic_holder.find("a.pp_previous").css("visibility","visible");$pp_pic_holder.find("a.pp_arrow_previous.disabled").removeClass("disabled").bind("click",function(){N("previous");return false})}$pp_pic_holder.find("p.currentTextHolder").text(D+W.counter_separator_label+setCount);Z=(isSet)?A(O[arrayPosition]):Z;b();if(Z.attr("title")){$pp_pic_holder.find(".pp_description").show().html(unescape(Z.attr("title")))}else{$pp_pic_holder.find(".pp_description").hide().text("")}if(Z.find("img").attr("alt")&&W.showTitle){hasTitle=true;$ppt.html(unescape(Z.find("img").attr("alt")))}else{hasTitle=false}}function P(d,c){hasBeenResized=false;T(d,c);imageWidth=d;imageHeight=c;windowHeight=A(window).height();windowWidth=A(window).width();if(((Y>windowWidth)||(V>windowHeight))&&E&&W.allowresize&&!K){hasBeenResized=true;notFitting=true;while(notFitting){if((Y>windowWidth)){imageWidth=(windowWidth-200);imageHeight=(c/d)*imageWidth}else{if((V>windowHeight)){imageHeight=(windowHeight-200);imageWidth=(d/c)*imageHeight}else{notFitting=false}}V=imageHeight;Y=imageWidth}T(imageWidth,imageHeight)}return{width:imageWidth,height:imageHeight,containerHeight:V,containerWidth:Y,contentHeight:R,contentWidth:S,resized:hasBeenResized}}function T(d,c){$pp_pic_holder.find(".pp_details").width(d).find(".pp_description").width(d-parseFloat($pp_pic_holder.find("a.pp_close").css("width")));R=c+$pp_pic_holder.find(".pp_details").height()+parseFloat($pp_pic_holder.find(".pp_details").css("marginTop"))+parseFloat($pp_pic_holder.find(".pp_details").css("marginBottom"));S=d;V=R+$pp_pic_holder.find(".ppt").height()+$pp_pic_holder.find(".pp_top").height()+$pp_pic_holder.find(".pp_bottom").height();Y=d+W.padding}function b(){if(Z.attr("href").match(/youtube\.com\/watch/i)){F="youtube"}else{if(Z.attr("href").indexOf(".mov")!=-1){F="quicktime"}else{if(Z.attr("href").indexOf(".swf")!=-1){F="flash"}else{if(Z.attr("href").indexOf("iframe")!=-1){F="iframe"}else{F="image"}}}}}function C(){if($pp_pic_holder){if($pp_pic_holder.size()==0){return }}else{return }if(A.browser.opera){windowHeight=window.innerHeight;windowWidth=window.innerWidth}else{windowHeight=A(window).height();windowWidth=A(window).width()}if(E){$pHeight=$pp_pic_holder.height();$pWidth=$pp_pic_holder.width();$tHeight=$ppt.height();projectedTop=(windowHeight/2)+M.scrollTop-($pHeight/2);if(projectedTop<0){projectedTop=0+$tHeight}$pp_pic_holder.css({top:projectedTop,left:(windowWidth/2)+M.scrollLeft-($pWidth/2)});$ppt.css({top:projectedTop-$tHeight,left:(windowWidth/2)+M.scrollLeft-($pWidth/2)+(W.padding/2)})}}function a(){H();if(F=="image"){imgPreloader=new Image();nextImage=new Image();if(isSet&&D>setCount){nextImage.src=A(O[arrayPosition+1]).attr("href")}prevImage=new Image();if(isSet&&O[arrayPosition-1]){prevImage.src=A(O[arrayPosition-1]).attr("href")}pp_typeMarkup='<img id="fullResImage" src="" />';$pp_pic_holder.find("#pp_full_res")[0].innerHTML=pp_typeMarkup;$pp_pic_holder.find(".pp_content").css("overflow","hidden");$pp_pic_holder.find("#fullResImage").attr("src",Z.attr("href"));imgPreloader.onload=function(){var c=P(imgPreloader.width,imgPreloader.height);imgPreloader.width=c.width;imgPreloader.height=c.height;showimage(imgPreloader.width,imgPreloader.height,c.containerWidth,c.containerHeight,c.contentHeight,c.contentWidth,c.resized)};imgPreloader.src=Z.attr("href")}else{movie_width=(parseFloat(B("width",Z.attr("href"))))?B("width",Z.attr("href")):"425";movie_height=(parseFloat(B("height",Z.attr("href"))))?B("height",Z.attr("href")):"344";if(movie_width.indexOf("%")!=-1||movie_height.indexOf("%")!=-1){movie_height=(A(window).height()*parseFloat(movie_height)/100)-100;movie_width=(A(window).width()*parseFloat(movie_width)/100)-100;parsentBased=true}else{movie_height=parseFloat(movie_height);movie_width=parseFloat(movie_width)}if(F=="quicktime"){movie_height+=13}correctSizes=P(movie_width,movie_height);if(F=="youtube"){pp_typeMarkup='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'+correctSizes.width+'" height="'+correctSizes.height+'"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://www.youtube.com/v/'+B("v",Z.attr("href"))+'" /><embed src="http://www.youtube.com/v/'+B("v",Z.attr("href"))+'" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="'+correctSizes.width+'" height="'+correctSizes.height+'"></embed></object>'}else{if(F=="quicktime"){pp_typeMarkup='<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" height="'+correctSizes.height+'" width="'+correctSizes.width+'"><param name="src" value="'+Z.attr("href")+'"><param name="autoplay" value="true"><param name="type" value="video/quicktime"><embed src="'+Z.attr("href")+'" height="'+correctSizes.height+'" width="'+correctSizes.width+'" autoplay="true" type="video/quicktime" pluginspage="http://www.apple.com/quicktime/download/"></embed></object>'}else{if(F=="flash"){flash_vars=Z.attr("href");flash_vars=flash_vars.substring(Z.attr("href").indexOf("flashvars")+10,Z.attr("href").length);filename=Z.attr("href");filename=filename.substring(0,filename.indexOf("?"));pp_typeMarkup='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'+correctSizes.width+'" height="'+correctSizes.height+'"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="'+filename+"?"+flash_vars+'" /><embed src="'+filename+"?"+flash_vars+'" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="'+correctSizes.width+'" height="'+correctSizes.height+'"></embed></object>'}else{if(F=="iframe"){movie_url=Z.attr("href");movie_url=movie_url.substr(0,movie_url.indexOf("iframe")-1);pp_typeMarkup='<iframe src ="'+movie_url+'" width="'+(correctSizes.width-10)+'" height="'+(correctSizes.height-10)+'" frameborder="no"></iframe>'}}}}$pp_pic_holder.find("#pp_full_res")[0].innerHTML=pp_typeMarkup;showimage(correctSizes.width,correctSizes.height,correctSizes.containerWidth,correctSizes.containerHeight,correctSizes.contentHeight,correctSizes.contentWidth,correctSizes.resized)}}function G(){if(self.pageYOffset){scrollTop=self.pageYOffset;scrollLeft=self.pageXOffset}else{if(document.documentElement&&document.documentElement.scrollTop){scrollTop=document.documentElement.scrollTop;scrollLeft=document.documentElement.scrollLeft}else{if(document.body){scrollTop=document.body.scrollTop;scrollLeft=document.body.scrollLeft}}}return{scrollTop:scrollTop,scrollLeft:scrollLeft}}function U(){A("div.pp_overlay").css({height:A(document).height(),width:A(window).width()})}function X(){toInject="";toInject+="<div id='fr1' class='pp_overlay'></div>";if(F=="image"){pp_typeMarkup='<img id="fullResImage" src="" />'}else{pp_typeMarkup=""}toInject+='<div class="pp_pic_holder"><div class="pp_top"><div class="pp_left"></div><div class="pp_middle"></div><div class="pp_right"></div></div><div class="pp_content"><a href="#" class="pp_expand" title="Expand the image">Expand</a><div class="pp_loaderIcon"></div><div class="pp_hoverContainer"><a class="pp_next" href="#">next</a><a class="pp_previous" href="#">previous</a></div><div id="pp_full_res">'+pp_typeMarkup+'</div><div class="pp_details clearfix"><a class="pp_close" href="#">Close</a><p class="pp_description"></p><div class="pp_nav"><a href="#" class="pp_arrow_previous">Previous</a><p class="currentTextHolder">0'+W.counter_separator_label+'0</p><a href="#" class="pp_arrow_next">Next</a></div></div></div><div class="pp_bottom"><div class="pp_left"></div><div class="pp_middle"></div><div class="pp_right"></div></div></div>';toInject+='<div class="ppt"></div>';A("body").append(toInject);$pp_pic_holder=A(".pp_pic_holder");$ppt=A(".ppt");A("div.pp_overlay").css("height",A(document).height()).bind("click",function(){L()});$pp_pic_holder.css({opacity:0}).addClass(W.theme);A("a.pp_close").bind("click",function(){L();return false});A("a.pp_expand").bind("click",function(){$this=A(this);if($this.hasClass("pp_expand")){$this.removeClass("pp_expand").addClass("pp_contract");E=false}else{$this.removeClass("pp_contract").addClass("pp_expand");E=true}Q();$pp_pic_holder.find(".pp_hoverContainer, #pp_full_res, .pp_details").fadeOut(W.animationSpeed,function(){a()});return false});$pp_pic_holder.find(".pp_previous, .pp_arrow_previous").bind("click",function(){N("previous");return false});$pp_pic_holder.find(".pp_next, .pp_arrow_next").bind("click",function(){N("next");return false});$pp_pic_holder.find(".pp_hoverContainer").css({"margin-left":W.padding/2});if(!isSet){$pp_pic_holder.find(".pp_hoverContainer,.pp_nav").hide()}if(A.browser.msie&&A.browser.version==6){A("body").addClass("ie6");A("select").css("visibility","hidden")}A("div.pp_overlay").css("opacity",0).fadeTo(W.animationSpeed,W.opacity,function(){$pp_pic_holder.css("opacity",0).fadeIn(W.animationSpeed,function(){$pp_pic_holder.attr("style","left:"+$pp_pic_holder.css("left")+";top:"+$pp_pic_holder.css("top")+";");a()})})}};function B(E,D){E=E.replace(/[\[]/,"\\[").replace(/[\]]/,"\\]");var C="[\\?&]"+E+"=([^&#]*)";var G=new RegExp(C);var F=G.exec(D);if(F==null){return""}else{return F[1]}}})(jQuery);
