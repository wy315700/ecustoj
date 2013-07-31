<?php
/**
 * if you have run set_ojinfo('highlight'), this model will be added to head;
 * @package Ecust Online Judge
 * @subpackage Code hightlight
 * @name Code hightlight style liberary
 * @license http://acm.ecust.edu.cn
 * @author  http://www.owent.net
 */

?>

<!-- Code hightlight style -->
<!--
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
-->

<link rel="highlight stylesheet" title="Default" href="libs/highlight/styles/default.css" />
<link rel="highlight stylesheet" title="Dark" href="libs/highlight/styles/dark.css" />
<link rel="highlight stylesheet" title="FAR" href="libs/highlight/styles/far.css" />
<link rel="highlight stylesheet" title="IDEA" href="libs/highlight/styles/idea.css" />
<link rel="highlight stylesheet" title="Sunburst" href="libs/highlight/styles/sunburst.css" />
<link rel="highlight stylesheet" title="Zenburn" href="libs/highlight/styles/zenburn.css" />
<link rel="highlight stylesheet" title="Visual Studio" href="libs/highlight/styles/vs.css" />
<link rel="highlight stylesheet" title="Ascetic" href="libs/highlight/styles/ascetic.css" />
<link rel="highlight stylesheet" title="Magula" href="libs/highlight/styles/magula.css" />
<link rel="highlight stylesheet" title="GitHub" href="libs/highlight/styles/github.css" />
<link rel="highlight stylesheet" title="Brown Paper" href="libs/highlight/styles/brown_paper.css" />
<link rel="highlight stylesheet" title="School Book" href="libs/highlight/styles/school_book.css" />
<link rel="highlight stylesheet" title="IR_Black" href="libs/highlight/styles/ir_black.css" />
<!-- Code hightlight script -->
<script type="text/javascript" src="libs/highlight/highlight.pack.js" ></script>

<script type="text/javascript" >
  hljs.tabReplace = '    ';
  hljs.initHighlightingOnLoad();

  (function(container_id) {
      if (window.addEventListener) {
          var attach = function(el, ev, handler) {
              el.addEventListener(ev, handler, false);
          }
      } else if (window.attachEvent) {
          var attach = function(el, ev, handler) {
              el.attachEvent('on' + ev, handler);
          }
      } else {
          var attach = function(el, ev, handler) {
              ev['on' + ev] = handler;
          }
      }

	  var currentStyleName = null, cookieStyle = null;
	  try{
	  if(oj && oj.cookie)
		  cookieStyle = oj.cookie('oj_hls');
	  } catch (e) {
	  }
      attach(window, 'load', function() {
          var current = null;

          var info = {};
		  
          var links = document.getElementsByTagName('link');
          var ul = document.createElement('ul');
		  var remark = document.createElement('span');
		  remark.innerHTML = "Select your code style: ";

          for (var i = 0; (link = links[i]); i++) {
              if (link.getAttribute('rel').indexOf('highlight') != -1
                  && link.title) {

                  var title = link.title;
				  
                  info[title] = {
                  'link': link,
                  'li': document.createElement('li')
                  }

                  ul.appendChild(info[title].li)
                  info[title].li.title = title;

                  info[title].link.disabled = true;

                  info[title].li.appendChild(document.createTextNode(title));
                  attach(info[title].li, 'click', (function (el) {
                      return function() {
                          current.li.className = '';
                          current.link.disabled = true;
                          current = el;
                          current.li.className = 'current';
                          current.link.disabled = false;
						  try{
							  if(oj && oj.cookie)
								  oj.cookie('oj_hls', current.li.title, {expires: 365});
						  } catch (e) {
						  }
                      }})(info[title]));
				  
				  if(cookieStyle && cookieStyle == title)
					  currentStyleName = title;
              }
          }

          current = info[currentStyleName || 'Default']
          current.li.className = 'current';
          current.link.disabled = false;

          ul.id = 'switch';
          container = document.getElementById(container_id);
		  if(container){
			  container.appendChild(remark);
			  container.appendChild(ul);
		  }
      });

  })('highlight_styleswitcher');
</script>
