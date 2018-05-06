(function(){
    if(typeof window.CustomEvent ==="function")return false;
    function CustomEvent (event,params){
               params=params||{bubbles:false,cancelable:false,detail:undefined};
               var ext=document.createEvent('CustomEvent');ext.initCustomEvent(event,params.bubbles,params.cancelable,params.detail);
               return ext;
           }
           CustomEvent.prototype=window.Event.prototype;
           window.CustomEvent=CustomEvent;
})();
(function(){
    if("classList" in Element.prototype)return false;
    var regExp = function(name){
        return new RegExp('(^|)'+name+'(|$)');
    };
    var forEach = function(list,fn,scope){
        for(var i=0;i<list.length;i++){
            fn.call(scope,list[i]);
        }
    };
    function ClassList(element){
        this.element=element;
    }
    ClassList.prototype={
        add: function(){
            forEach(arguments,function(name){
                if(!this.contains(name)){
                    this.element.className+=' '+name;
                }
            },this);
        },
        remove: function(){
            forEach(arguments,function(name){
                this.element.className= this.element.className.replace(regExp(name),'');
            },this);
        },
        toggle: function(name){
            return this.contains(name)?(this.remove(name),false):(this.add(name),true);
        },
        contains: function(name){
            return regExp(name).test(this.element.className);
        },
        replace: function(oldName,newName){
            this.remove(oldName),this.add(newName);
        }
    };
    Object.defineProperty(Element.prototype,'classList',{
        get: function(){
            return new ClassList(this);
        }
    });
    if(window.DOMTokenList&&DOMTokenList.prototype.replace==null){
        DOMTokenList.prototype.replace = ClassList.prototype.replace;
    }
})();
(function(){
    window.$focus=true;
    addEventListener('blur',function(e){
        window.$focus=false;
    });
    addEventListener('focus',function(e){
        window.$focus=true;
    });
    function notif(a,b,c,d,e,de){
        if(!window.$focus&&typeof Notification === "function"){
            var title=a.title||"Message From XODUS";a.tag=a.tag||a.label||"info";
            if(Notification.permission==="granted"){
                var n = new Notification(title,a);n.onclick=de||n.close();
                console.log(2);
                return n;
            }else if(Notification.permission!=="denied"){
                Notification.requestPermission(function(p){
                    if(p==='granted'){
                        var n = new Notification(title,a);n.onclick=de||n.close();
                        console.log(3);
                        return n;
                    }else{
                        window.$focus=true;notif(a);
                    }
                })
            }
        }else{
            var host = c||document.body;
            if(e === undefined){
                if(!a.label){
                    a.label='info';
                }
                var elem = document.getElementById('notify').innerHTML;
                var h = Handlebars.compile(elem),note = h(a);
                e=document.createElement('div');
                e.innerHTML=note;e.className="row expanded";
            }            
            e.className+=" notify";e.style.bottom=d?d:'4em';
            var done=false;
            document.body.appendChild(e);e.addEventListener('click',de||function(){e.className+=' fade-off';done=true;host.scrollIntoView();host.focus();setTimeout(function(){document.body.removeChild(e);},200);});            
            setTimeout(function(){if(done!==true){e.className+=' fade-off';
                setTimeout(function(){document.body.removeChild(e);done=true;},200);}},b||8000);
    }
    }    
    window.notify=notif;
    })();
    var TN,main;
    TN = function(){
        this.root = document.getElementsByTagName('main')[0];
        this.config = {storage:'js/localforage.min.js'};
        this.user={};
        this.el={};
        this.evs={
            mnav:(function(){
                var a = new CustomEvent('mob-nav');
                return a;
            })(),
            load:(function(){
                return new CustomEvent('load');
            })(),
            view:(function(){
                return new CustomEvent('view');
            })()
        };
    };
    TN.prototype.search=function(_,$$_,$_){
    var y;main.ge.call(document,_,function(g){return y=g;});
    y.onkeyup=function(e){
        var k=e.target.value;console.log(k);
        if(e.key==='Enter'||e.keyCode===13){            
            e.target.value='';e.target.blur();console.log(e);
            //TODO : change this url!
            var $$$_=$_||{verb:'GET',url:'http://localhost/tn/net.php?search='+k};
            main.worker($$$_,function(res){
                console.log(res.response||res.responseText);
            });
        }
        if($$_){
            $$_(k);
        }
    };
    };
    /**
     * 
     * @param {String} main
     * @param {Object} config
     * @returns {Object} TN
     */
    TN.prototype.init=function(main,config){
        if(main!==undefined && typeof main==="string" && main!==''){
            var lookup = document.body.querySelector('#'+main);
            if(lookup){
                this.root = document.getElementById(main);
            }
        }
        config=config||this.config;
        for(var i in config){
            if(config[i]){var s=document.createElement('script');s.src=this.config[i];document.body.appendChild(s);}
        }
        return this;
    };
    /**
     * 
     * @param {string} w
     * @param {function} f
     * @returns {node|Boolean}
     */
    TN.prototype.ge=function(w,f){
        var g=false;
        if(typeof w==="string" && w!==undefined && w!==''){
            g=this.getElementById(w);
        }if(g){
            return f(g);
        }
        return g;
    };
    /**
     * 
     * @param {Object} url This object contains details used for the ajax call.
     * @param {Function} res This is the callback function called with the ajax object as parameter.
     * @returns {Boolean} returns false is the object parameter is empty. this should not be watched.
     */
    TN.prototype.worker=function(url,res){
        if(typeof url !== 'object'||typeof url !== "string"){
            return false;
        }
//        eurl=encodeURI(url);
        wk = new XMLHttpRequest();
        wk.open(url.verb||"GET",encodeURI(url.url||url),url.async||true);
        wk.onreadystatechange = function(e){
            if(wk.readyState===4){
                if(Math.floor(wk.status/100)===2){
                  return res(wk);                   
//                   wk.abort();
                }
            }
        };
        wk.send();
    };
    TN.prototype.vp=function(element){
            var elem = document.getElementById(element);
            var rect = elem.getBoundingClientRect();
            var html = document.documentElement;
            return (rect.top>=0 && rect.left>=0 && rect.bottom <=(window.innerHeight||html.clientHeight)
                    && rect.right<=(window.innerWidth||html.clientWidth));
        };
    TN.prototype.render=function(el,d,wr){
        handler = Handlebars.compile(el.innerHTML);console.log(handler);
        html=handler(d);console.log(html); 
        return wr?wr.innerHTML=html:this.root.innerHTML=html;
    }
    main = new TN();
    main.el={
        mn:(function(){
            return document.getElementById('mn');
        })(),
        hds:(function(){
            var s=document.getElementById('hdr-s');main.search(s.id);return s;
        })(),
        tbar:(function(){
            return document.getElementById('title-bar');
        })(),
        sidebar:(function(){
            return document.getElementById('sidebar');
        })(),
        spin: (function(){var spin= document.createElement('div'),spinner= document.createElement('div');spin.className="loader";spinner.className='float-center spinner';
            spin.appendChild(spinner);spin.style.opacity=0;return spin;})(),
        scrum: (function(){
            return document.getElementById('scrum');
        })()
    };
    
    main.init('app');
    main.ms={
      'tbar':{
          'tbarun':main.user.username,//title bar username
          'tbarsb':{//title bar search bar
              'spl':'',//search placeholder
              'ssgt':[]//search suggestions
          }
      }  
    };
    main.st={
        mnav:(function(s){
            return s.classList.contains('cross')?true:false;
        })(main.el.sidebar),
        scrum:(function(s){
            return s.classList.contains('view')?true:false;
        })(main.el.scrum),
        tnt:0
    };
     main.mn=function(){
        var _=[main.el.tbar,main.el.mn,document.getElementById('m-n1'),document.getElementById('m-n2'),document.getElementById('m-n3'),
               document.getElementById('sidebar')];
        for(var i in _){_[i].classList.toggle('cross');}
        main.st.mnav=main.el.sidebar.classList.contains('cross')?true:false;        
    };
    main.view=function(){
        main.st.scrum=main.el.scrum.classList.toggle('view')?true:false;
    };
    
    main.submit=function (d){
        d.preventDefault();
        console.log(d,'k');return false;
    };
    function sbareventListener(e){
        if(main.st.mnav&&e.target!==(main.el.sidebar||main.el.mn)){main.mn();}console.log(e.target);
    };
    
    function hiss(url,state,title){
        if(url.charAt(0)!=='/'){
            url='/'+url;
        }
        history.pushState(state||{},title||"Biome",url);
        main.render()
    }
    //EVENT LISTENERS    
    
    main.root.addEventListener('click',sbareventListener,true);
    
    window.addEventListener('mob-nav',function(e){main.mn();setTimeout(function(){if(main.st.mnav===true){main.mn();}},18000);},true);
    window.addEventListener('view',function(e){main.view();},true);
    window.addEventListener('pageshow',function(e){notify({body:'DONE!'});main.el.tbar.scrollIntoView();},true);

    document.getElementById('mn').addEventListener('click',function(e){e.stopPropagation();dispatchEvent(main.evs.mnav);});
    
    var al=document.getElementsByTagName('a');
    for(var i=0;i<al.length;i++){
        al[i].addEventListener('click',function(e){
            e.preventDefault();e.stopPropagation();
            window.dispatchEvent(main.evs.load);//create main.worker calls here
            console.log(e.target.href);
        },false);            console.log('e.target.href');

    }
// REMOVE COMMENT   window.addEventListener('load',function(){spin();main.el.tbar.scrollIntoView();});
    
    // LOADER
    function spin(){
        document.body.appendChild(main.el.spin);setTimeout(function(){main.el.spin.style.opacity=1;},10);
    }
    function stopSpin(){
        main.el.spin.style.opacity=0;main.el.spin.classList.toggle("grayscale");
        setTimeout(function(){document.body.removeChild(main.el.spin);main.el.spin.classList.toggle("grayscale")},2000);
    }
    

//        if(!main.vp(main.el.tbar.id)){
//            main.el.tbar.style.display= "absolute";console.log(e);
//        }
//    },false);
