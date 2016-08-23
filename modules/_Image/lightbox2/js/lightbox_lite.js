function getPageScroll(){var xScroll,yScroll;if(self.pageYOffset){yScroll=self.pageYOffset;xScroll=self.pageXOffset;}
else if(document.documentElement&&document.documentElement.scrollTop){yScroll=document.documentElement.scrollTop;xScroll=document.documentElement.scrollLeft;}
else if(document.body){yScroll=document.body.scrollTop;xScroll=document.body.scrollLeft;}
arrayPageScroll=[xScroll,yScroll];return arrayPageScroll;}
function getPageSize(){var xScroll,yScroll;if(window.innerHeight&&window.scrollMaxY){xScroll=window.innerWidth+window.scrollMaxX;yScroll=window.innerHeight+window.scrollMaxY;}
else if(document.body.scrollHeight>document.body.offsetHeight){xScroll=document.body.scrollWidth;yScroll=document.body.scrollHeight;}
else{xScroll=document.body.offsetWidth;yScroll=document.body.offsetHeight;}
var windowWidth,windowHeight;if(self.innerHeight){if(document.documentElement.clientHeight){windowWidth=document.documentElement.clientWidth;}
else{windowWidth=self.innerWidth;}
windowHeight=self.innerHeight;}
else if(document.documentElement&&document.documentElement.clientHeight){windowWidth=document.documentElement.clientWidth;windowHeight=document.documentElement.clientHeight;}
else if(document.body){windowWidth=document.body.clientWidth;windowHeight=document.body.clientHeight;}
if(yScroll<windowHeight){pageHeight=windowHeight;}
else{pageHeight=yScroll;}
if(xScroll<windowWidth){pageWidth=xScroll;}
else{pageWidth=windowWidth;}
arrayPageSize=[pageWidth,pageHeight,windowWidth,windowHeight];return arrayPageSize;}
function pause(ms){var date=new Date();var curDate=null;do{curDate=new Date();}
while(curDate-date<ms);}
function hideLightbox(){objOverlay=document.getElementById('overlay');objLightbox=document.getElementById('lightbox');objOverlay.style.display='none';objLightbox.style.display='none';selects=document.getElementsByTagName("select");for(i=0;i!=selects.length;i++){if(selects[i].style.display!="none"){selects[i].style.visibility="visible";}}
embed=document.getElementsByTagName("embed");for(i=0;i!=embed.length;i++){if(embed[i].style.display!="none"){embed[i].style.visibility="visible";}}
objects=document.getElementsByTagName("object");for(i=0;i!=objects.length;i++){if(objects[i].style.display!="none"){objects[i].style.visibility="visible";}}
document.onkeydown='';}
function getKey(e){if(e===null){keycode=event.keyCode;escapeKey=27;}
else{keycode=e.keyCode;escapeKey=e.DOM_VK_ESCAPE;}
key=String.fromCharCode(keycode).toLowerCase();if(key=='x'||key=='c'||keycode==escapeKey){hideLightbox();}}
function listenKey(){document.onkeydown=getKey;}
function imgLoadingError(image,objImage,objLink){var settings=Drupal.settings.lightbox2;image.src=settings.default_image;objImage.src=settings.default_image;objLink.href=settings.default_image;}
function showLightbox(objLink){var settings=Drupal.settings.lightbox2;var objOverlay=document.getElementById('overlay');var objLightbox=document.getElementById('lightbox');var objCaption=document.getElementById('lightboxCaption');var objImage=document.getElementById('lightboxImage');var objLoadingImage=document.getElementById('loadingImage');var objLightboxDetails=document.getElementById('lightboxDetails');var arrayPageSize=getPageSize();var arrayPageScroll=getPageScroll();objOverlay.style.height=(arrayPageSize[1]+'px');objOverlay.style.display='block';objOverlay.style.opacity=settings.overlay_opacity;objOverlay.style.backgroundColor='#'+settings.overlay_color;imgPreload=new Image();imgPreload.onerror=function(){imgLoadingError(this,objImage,objLink);};imgPreload.onload=function(){objImage.src=objLink.href;var lightboxTop=arrayPageScroll[1]+((arrayPageSize[3]-35-imgPreload.height)/2);var lightboxLeft=((arrayPageSize[0]-20-imgPreload.width)/2);objLightbox.style.top=(lightboxTop<0)?"0px":lightboxTop+"px";objLightbox.style.left=(lightboxLeft<0)?"0px":lightboxLeft+"px";objLightbox.style.width=imgPreload.width+'px';if(objLink.getAttribute('title')){objCaption.style.display='block';objCaption.innerHTML=objLink.getAttribute('title');}
else{objCaption.style.display='none';}
if(navigator.appVersion.indexOf("MSIE")!=-1){pause(250);}
if(objLoadingImage){objLoadingImage.style.display='none';}
selects=document.getElementsByTagName("select");for(i=0;i!=selects.length;i++){if(selects[i].style.display!="none"){selects[i].style.visibility="hidden";}}
embed=document.getElementsByTagName("embed");for(i=0;i!=embed.length;i++){if(embed[i].style.display!="none"){embed[i].style.visibility="hidden";}}
objects=document.getElementsByTagName("object");for(i=0;i!=objects.length;i++){if(objects[i].style.display!="none"){objects[i].style.visibility="hidden";}}
objLightbox.style.display='block';arrayPageSize=getPageSize();objOverlay.style.height=(arrayPageSize[1]+'px');listenKey();return false;};imgPreload.src=objLink.href;}
function initLightbox(){if(!document.getElementsByTagName){return;}
var anchors=document.getElementsByTagName("a");for(var i=0;i<anchors.length;i++){var anchor=anchors[i];var relAttribute=String(anchor.getAttribute("rel"));if(anchor.getAttribute("href")&&relAttribute.toLowerCase().match("lightbox")){anchor.onclick=function(){showLightbox(this);return false;};}}
var objBody=document.getElementsByTagName("body").item(0);var objOverlay=document.createElement("div");objOverlay.setAttribute('id','overlay');objOverlay.onclick=function(){hideLightbox();return false;};objOverlay.style.display='none';objOverlay.style.position='absolute';objOverlay.style.top='0';objOverlay.style.left='0';objOverlay.style.zIndex='90';objOverlay.style.width='100%';objBody.insertBefore(objOverlay,objBody.firstChild);var arrayPageSize=getPageSize();var arrayPageScroll=getPageScroll();var objLoadingImage=document.createElement("span");objLoadingImage.setAttribute('id','loadingImage');objOverlay.appendChild(objLoadingImage);var objLightbox=document.createElement("div");objLightbox.setAttribute('id','lightbox');objLightbox.style.display='none';objLightbox.style.position='absolute';objLightbox.style.zIndex='100';objBody.insertBefore(objLightbox,objOverlay.nextSibling);var objLink=document.createElement("a");objLink.setAttribute('href','#');objLink.setAttribute('title','Click to close');objLink.onclick=function(){hideLightbox();return false;};objLightbox.appendChild(objLink);var objCloseButton=document.createElement("span");objCloseButton.setAttribute('id','closeButton');objLink.appendChild(objCloseButton);var objImage=document.createElement("img");objImage.setAttribute('id','lightboxImage');objLink.appendChild(objImage);var objLightboxDetails=document.createElement("div");objLightboxDetails.setAttribute('id','lightboxDetails');objLightbox.appendChild(objLightboxDetails);var objCaption=document.createElement("div");objCaption.setAttribute('id','lightboxCaption');objCaption.style.display='none';objLightboxDetails.appendChild(objCaption);var settings=Drupal.settings.lightbox2;var objKeyboardMsg=document.createElement("div");objKeyboardMsg.setAttribute('id','keyboardMsg');objKeyboardMsg.innerHTML=settings.lite_press_x_close;objLightboxDetails.appendChild(objKeyboardMsg);}
if(Drupal.jsEnabled){$(document).ready(function(){initLightbox();});}