(function(){
  "use strict";
  var THEMES={minimal:"Minimal",dark:"Dark",retro:"Retro",neon:"Neon"};
  var links=[["index.html","Home"],["about.html","About"],["projects.html","Projects"],["contact.html","Contact"]];

  function currentPage(){
    var path=window.location.pathname.split("/").pop();
    return path||"index.html";
  }

  function renderHeader(){
    var target=document.getElementById("site-header");
    if(!target)return;
    target.className="site-header";
    var current=currentPage();
    var options="";
    Object.keys(THEMES).forEach(function(key){options+='<option value="'+key+'">'+THEMES[key]+"</option>";});
    var nav="";
    links.forEach(function(item){
      nav+='<li><a href="'+item[0]+'"'+(current===item[0]?' aria-current="page"':'')+'>'+item[1]+"</a></li>";
    });
    target.innerHTML='<div class="navbar"><a class="brand" href="index.html">Vanilla Starter</a><button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav">Menu</button><nav id="site-nav" aria-label="Primary navigation"><ul class="nav-links" data-open="false">'+nav+'</ul></nav><label class="theme-control"><span>Theme</span><select id="theme-picker" aria-label="Choose visual theme">'+options+'</select></label></div>';
    var toggle=target.querySelector(".nav-toggle");
    var navList=target.querySelector(".nav-links");
    toggle.addEventListener("click",function(){var open=navList.getAttribute("data-open")==="true";navList.setAttribute("data-open",String(!open));toggle.setAttribute("aria-expanded",String(!open));});
    target.querySelector("#theme-picker").addEventListener("change",function(){setTheme(this.value);});
  }

  function setTheme(theme){
    if(!THEMES[theme])theme="minimal";
    var sheet=document.getElementById("theme-stylesheet");
    if(sheet)sheet.href="css/themes/"+theme+".css";
    try{localStorage.setItem("vanilla-theme",theme);}catch(error){}
    var picker=document.getElementById("theme-picker");
    if(picker)picker.value=theme;
  }

  function loadTheme(){
    var saved="minimal";
    try{saved=localStorage.getItem("vanilla-theme")||"minimal";}catch(error){}
    setTheme(saved);
  }

  function renderFooter(){
    var target=document.getElementById("site-footer");
    if(!target)return;
    target.className="site-footer";
    target.innerHTML='<div class="footer-inner"><span>Vanilla Starter</span><span>Built with HTML, CSS &amp; JavaScript.</span></div>';
  }

  function setupForm(){
    var form=document.getElementById("contact-form");
    var status=document.getElementById("form-status");
    if(!form||!status)return;
    form.addEventListener("submit",function(event){
      event.preventDefault();
      var name=new FormData(form).get("name");
      status.textContent="Thanks, "+(name||"there")+"! This demo does not send data anywhere.";
      form.reset();
    });
  }

  document.addEventListener("DOMContentLoaded",function(){renderHeader();renderFooter();loadTheme();setupForm();});
}());
