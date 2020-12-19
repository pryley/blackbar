!function i(s,o,l){function c(t,e){if(!o[t]){if(!s[t]){var n="function"==typeof require&&require;if(!e&&n)return n(t,!0);if(d)return d(t,!0);var a=new Error("Cannot find module '"+t+"'");throw a.code="MODULE_NOT_FOUND",a}var r=o[t]={exports:{}};s[t][0].call(r.exports,function(e){return c(s[t][1][e]||e)},r,r.exports,i,s,o,l)}return o[t].exports}for(var d="function"==typeof require&&require,e=0;e<l.length;e++)c(l[e]);return c}({1:[function(e,t,n){var a=e("highlight");a.registerLanguage("bash",e("bash")),a.registerLanguage("php",e("php")),a.registerLanguage("sql",e("sql")),t.exports=a,window.Blackbar={activeClass:"glbb-active",element:["glbb-actions","glbb-console","glbb-globals","glbb-profiler","glbb-queries","glbb-templates"],id:"glbb-debug-bar",open:null,close:function(){document.getElementById(Blackbar.id).style.display="none",document.querySelector("body").classList.remove("blackbar")},createCookie:function(e,t,n){var a;if(n){var r=new Date;r.setTime(r.getTime()+24*n*60*60*1e3),a="; expires="+r.toGMTString()}else a="";document.cookie=e+"="+t+a+"; path=/"},eraseCookie:function(e){createCookie(e,"",-1)},readCookie:function(e){for(var t=e+"=",n=document.cookie.split(";"),a=0;a<n.length;a++){for(var r=n[a];" "===r.charAt(0);)r=r.substring(1,r.length);if(0===r.indexOf(t))return r.substring(t.length,r.length)}return null},switchPanel:function(e){for(var t in Blackbar.element){var n=document.getElementById(Blackbar.element[t]);if(n){n.style.display="none";var a=document.querySelector("a."+Blackbar.element[t]);a.classList.remove(Blackbar.activeClass),a.blur(),a.hideFocus=!0}}e!=Blackbar.open?(Blackbar.open=e,document.getElementById(e).style.display="block",document.querySelector("a."+e).classList.add(Blackbar.activeClass)):Blackbar.open=null}},document.addEventListener("DOMContentLoaded",function(){document.querySelectorAll("#glbb-debug-bar pre code").forEach(function(e){a.highlightBlock(e)})}),document.addEventListener("DOMContentLoaded",function(){var n=document.getElementById(Blackbar.id);if(n){function e(e){var i=parseFloat(r.value),s=a.value,o=0,l=0;n.querySelectorAll("#glbb-queries tr").forEach(function(e){var t=parseFloat(e.querySelector(".glbb-small").textContent.replace(" [ms]","")),n=e.querySelector(".sql").textContent.indexOf(s),a=0<i&&t<i,r=0<s.length&&-1==n;a||r?e.style.display="none":(e.style.display="",o++,l+=t)}),n.querySelector(".glbb-queries-count").textContent=o,n.querySelector(".glbb-queries-time").textContent=l.toFixed(2),Blackbar.createCookie("glbb_query_filter",s),Blackbar.createCookie("glbb_query_min_time",n.querySelector("#glbb_query_min_time").value)}var t=n.querySelector(".glbb-toggle"),a=n.querySelector("#glbb_query_filter"),r=n.querySelector("#glbb_query_min_time");document.body.addEventListener("keydown",function(e){27===e.which&&null!=Blackbar.open&&Blackbar.switchPanel(Blackbar.open)}),a&&r&&(a.addEventListener("keyup",e),a.value=Blackbar.readCookie("glbb_query_filter"),r.addEventListener("keyup",e),r.value=Blackbar.readCookie("glbb_query_min_time")),t&&t.addEventListener("click",function(e){var t=e.target.closest("a");t.classList.contains("glbb-off")?(t.classList.remove("glbb-off"),t.classList.add("glbb-on"),t.textContent="",n.classList.add("glbb-mini"),n.querySelectorAll(".glbb-debug-panel").forEach(function(e){e.style.display="none"}),n.querySelectorAll("a.glbb-active").forEach(function(e){e.classList.remove(Blackbar.activeClass)}),Blackbar.createCookie("glbb-toggle","on")):(t.classList.remove("glbb-on"),t.classList.add("glbb-off"),t.textContent="Toggle",n.classList.remove("glbb-mini"),Blackbar.createCookie("glbb-toggle","off")),t.blur(),t.hideFocus=!0,e.preventDefault()}),"on"===Blackbar.readCookie("glbb-toggle")&&t.click()}})},{bash:3,highlight:2,php:4,sql:5}],2:[function(e,t,n){var a,r;a=function(r){var n,u=[],l=Object.keys,h={},o={},t=/^(no-?highlight|plain|text)$/i,c=/\blang(?:uage)?-([\w-]+)\b/i,a=/((^(<[^>]+>|\t|)+|(?:\n)))/gm,v="</span>",y={classPrefix:"hljs-",tabReplace:null,useBR:!1,languages:void 0};function E(e){return e.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;")}function g(e){return e.nodeName.toLowerCase()}function w(e,t){var n=e&&e.exec(t);return n&&0===n.index}function d(e){return t.test(e)}function m(e){var t,n={},a=Array.prototype.slice.call(arguments,1);for(t in e)n[t]=e[t];return a.forEach(function(e){for(t in e)n[t]=e[t]}),n}function p(e){var r=[];return function e(t,n){for(var a=t.firstChild;a;a=a.nextSibling)3===a.nodeType?n+=a.nodeValue.length:1===a.nodeType&&(r.push({event:"start",offset:n,node:a}),n=e(a,n),g(a).match(/br|hr|img|input/)||r.push({event:"stop",offset:n,node:a}));return n}(e,0),r}function _(e,t,n){var a=0,r="",i=[];function s(){return e.length&&t.length?e[0].offset!==t[0].offset?e[0].offset<t[0].offset?e:t:"start"===t[0].event?e:t:e.length?e:t}function o(e){r+="<"+g(e)+u.map.call(e.attributes,function(e){return" "+e.nodeName+'="'+E(e.value).replace('"',"&quot;")+'"'}).join("")+">"}function l(e){r+="</"+g(e)+">"}function c(e){("start"===e.event?o:l)(e.node)}for(;e.length||t.length;){var d=s();if(r+=E(n.substring(a,d[0].offset)),a=d[0].offset,d===e){for(i.reverse().forEach(l);c(d.splice(0,1)[0]),(d=s())===e&&d.length&&d[0].offset===a;);i.reverse().forEach(o)}else"start"===d[0].event?i.push(d[0].node):i.pop(),c(d.splice(0,1)[0])}return r+E(n.substr(a))}function i(e){if(n&&!e.langApiRestored){for(var t in e.langApiRestored=!0,n)e[t]&&(e[n[t]]=e[t]);(e.contains||[]).concat(e.variants||[]).forEach(i)}}function x(s){function c(e){return e&&e.source||e}function o(e,t){return new RegExp(c(e),"m"+(s.case_insensitive?"i":"")+(t?"g":""))}!function t(n,e){if(!n.compiled){if(n.compiled=!0,n.keywords=n.keywords||n.beginKeywords,n.keywords){function a(n,e){s.case_insensitive&&(e=e.toLowerCase()),e.split(" ").forEach(function(e){var t=e.split("|");r[t[0]]=[n,t[1]?Number(t[1]):1]})}var r={};"string"==typeof n.keywords?a("keyword",n.keywords):l(n.keywords).forEach(function(e){a(e,n.keywords[e])}),n.keywords=r}n.lexemesRe=o(n.lexemes||/\w+/,!0),e&&(n.beginKeywords&&(n.begin="\\b("+n.beginKeywords.split(" ").join("|")+")\\b"),n.begin||(n.begin=/\B|\b/),n.beginRe=o(n.begin),n.endSameAsBegin&&(n.end=n.begin),n.end||n.endsWithParent||(n.end=/\B|\b/),n.end&&(n.endRe=o(n.end)),n.terminator_end=c(n.end)||"",n.endsWithParent&&e.terminator_end&&(n.terminator_end+=(n.end?"|":"")+e.terminator_end)),n.illegal&&(n.illegalRe=o(n.illegal)),null==n.relevance&&(n.relevance=1),n.contains||(n.contains=[]),n.contains=Array.prototype.concat.apply([],n.contains.map(function(e){return function(t){return t.variants&&!t.cached_variants&&(t.cached_variants=t.variants.map(function(e){return m(t,{variants:null},e)})),t.cached_variants||t.endsWithParent&&[m(t)]||[t]}("self"===e?n:e)})),n.contains.forEach(function(e){t(e,n)}),n.starts&&t(n.starts,e);var i=n.contains.map(function(e){return e.beginKeywords?"\\.?(?:"+e.begin+")\\.?":e.begin}).concat([n.terminator_end,n.illegal]).map(c).filter(Boolean);n.terminators=i.length?o(function(e,t){for(var n=/\[(?:[^\\\]]|\\.)*\]|\(\??|\\([1-9][0-9]*)|\\./,a=0,r="",i=0;i<e.length;i++){var s=a,o=c(e[i]);for(0<i&&(r+=t);0<o.length;){var l=n.exec(o);if(null==l){r+=o;break}r+=o.substring(0,l.index),o=o.substring(l.index+l[0].length),"\\"==l[0][0]&&l[1]?r+="\\"+String(Number(l[1])+s):(r+=l[0],"("==l[0]&&a++)}}return r}(i,"|"),!0):{exec:function(){return null}}}}(s)}function k(e,t,i,n){function o(e,t,n,a){var r='<span class="'+(a?"":y.classPrefix);return e?(r+=e+'">')+t+(n?"":v):t}function s(){g+=null!=d.subLanguage?function(){var e="string"==typeof d.subLanguage;if(e&&!h[d.subLanguage])return E(m);var t=e?k(d.subLanguage,m,!0,u[d.subLanguage]):N(m,d.subLanguage.length?d.subLanguage:void 0);return 0<d.relevance&&(p+=t.relevance),e&&(u[d.subLanguage]=t.top),o(t.language,t.value,!1,!0)}():function(){var e,t,n,a,r,i,s;if(!d.keywords)return E(m);for(a="",t=0,d.lexemesRe.lastIndex=0,n=d.lexemesRe.exec(m);n;)a+=E(m.substring(t,n.index)),r=d,i=n,void 0,s=c.case_insensitive?i[0].toLowerCase():i[0],(e=r.keywords.hasOwnProperty(s)&&r.keywords[s])?(p+=e[1],a+=o(e[0],E(n[0]))):a+=E(n[0]),t=d.lexemesRe.lastIndex,n=d.lexemesRe.exec(m);return a+E(m.substr(t))}(),m=""}function l(e){g+=e.className?o(e.className,"",!0):"",d=Object.create(e,{parent:{value:d}})}function a(e,t){if(m+=e,null==t)return s(),0;var n=function(e,t){var n,a,r;for(n=0,a=t.contains.length;n<a;n++)if(w(t.contains[n].beginRe,e))return t.contains[n].endSameAsBegin&&(t.contains[n].endRe=(r=t.contains[n].beginRe.exec(e)[0],new RegExp(r.replace(/[-\/\\^$*+?.()|[\]{}]/g,"\\$&"),"m"))),t.contains[n]}(t,d);if(n)return n.skip?m+=t:(n.excludeBegin&&(m+=t),s(),n.returnBegin||n.excludeBegin||(m=t)),l(n),n.returnBegin?0:t.length;var a=function e(t,n){if(w(t.endRe,n)){for(;t.endsParent&&t.parent;)t=t.parent;return t}if(t.endsWithParent)return e(t.parent,n)}(d,t);if(a){var r=d;for(r.skip?m+=t:(r.returnEnd||r.excludeEnd||(m+=t),s(),r.excludeEnd&&(m=t));d.className&&(g+=v),d.skip||d.subLanguage||(p+=d.relevance),(d=d.parent)!==a.parent;);return a.starts&&(a.endSameAsBegin&&(a.starts.endRe=a.endRe),l(a.starts)),r.returnEnd?0:t.length}if(function(e,t){return!i&&w(t.illegalRe,e)}(t,d))throw new Error('Illegal lexeme "'+t+'" for mode "'+(d.className||"<unnamed>")+'"');return m+=t,t.length||1}var c=C(e);if(!c)throw new Error('Unknown language: "'+e+'"');x(c);var r,d=n||c,u={},g="";for(r=d;r!==c;r=r.parent)r.className&&(g=o(r.className,"",!0)+g);var m="",p=0;try{for(var _,b,f=0;d.terminators.lastIndex=f,_=d.terminators.exec(t);)b=a(t.substring(f,_.index),_[0]),f=_.index+b;for(a(t.substr(f)),r=d;r.parent;r=r.parent)r.className&&(g+=v);return{relevance:p,value:g,language:e,top:d}}catch(e){if(e.message&&-1!==e.message.indexOf("Illegal"))return{relevance:0,value:E(t)};throw e}}function N(n,e){e=e||y.languages||l(h);var a={relevance:0,value:E(n)},r=a;return e.filter(C).filter(M).forEach(function(e){var t=k(e,n,!1);t.language=e,t.relevance>r.relevance&&(r=t),t.relevance>a.relevance&&(r=a,a=t)}),r.language&&(a.second_best=r),a}function b(e){return y.tabReplace||y.useBR?e.replace(a,function(e,t){return y.useBR&&"\n"===e?"<br>":y.tabReplace?t.replace(/\t/g,y.tabReplace):""}):e}function s(e){var t,n,a,r,i,s=function(e){var t,n,a,r,i=e.className+" ";if(i+=e.parentNode?e.parentNode.className:"",n=c.exec(i))return C(n[1])?n[1]:"no-highlight";for(t=0,a=(i=i.split(/\s+/)).length;t<a;t++)if(d(r=i[t])||C(r))return r}(e);d(s)||(y.useBR?(t=document.createElementNS("http://www.w3.org/1999/xhtml","div")).innerHTML=e.innerHTML.replace(/\n/g,"").replace(/<br[ \/]*>/g,"\n"):t=e,i=t.textContent,a=s?k(s,i,!0):N(i),(n=p(t)).length&&((r=document.createElementNS("http://www.w3.org/1999/xhtml","div")).innerHTML=a.value,a.value=_(n,p(r),i)),a.value=b(a.value),e.innerHTML=a.value,e.className=function(e,t,n){var a=t?o[t]:n,r=[e.trim()];return e.match(/\bhljs\b/)||r.push("hljs"),-1===e.indexOf(a)&&r.push(a),r.join(" ").trim()}(e.className,s,a.language),e.result={language:a.language,re:a.relevance},a.second_best&&(e.second_best={language:a.second_best.language,re:a.second_best.relevance}))}function f(){if(!f.called){f.called=!0;var e=document.querySelectorAll("pre code");u.forEach.call(e,s)}}function C(e){return e=(e||"").toLowerCase(),h[e]||h[o[e]]}function M(e){var t=C(e);return t&&!t.disableAutodetect}return r.highlight=k,r.highlightAuto=N,r.fixMarkup=b,r.highlightBlock=s,r.configure=function(e){y=m(y,e)},r.initHighlighting=f,r.initHighlightingOnLoad=function(){addEventListener("DOMContentLoaded",f,!1),addEventListener("load",f,!1)},r.registerLanguage=function(t,e){var n=h[t]=e(r);i(n),n.aliases&&n.aliases.forEach(function(e){o[e]=t})},r.listLanguages=function(){return l(h)},r.getLanguage=C,r.autoDetection=M,r.inherit=m,r.IDENT_RE="[a-zA-Z]\\w*",r.UNDERSCORE_IDENT_RE="[a-zA-Z_]\\w*",r.NUMBER_RE="\\b\\d+(\\.\\d+)?",r.C_NUMBER_RE="(-?)(\\b0[xX][a-fA-F0-9]+|(\\b\\d+(\\.\\d*)?|\\.\\d+)([eE][-+]?\\d+)?)",r.BINARY_NUMBER_RE="\\b(0b[01]+)",r.RE_STARTERS_RE="!|!=|!==|%|%=|&|&&|&=|\\*|\\*=|\\+|\\+=|,|-|-=|/=|/|:|;|<<|<<=|<=|<|===|==|=|>>>=|>>=|>=|>>>|>>|>|\\?|\\[|\\{|\\(|\\^|\\^=|\\||\\|=|\\|\\||~",r.BACKSLASH_ESCAPE={begin:"\\\\[\\s\\S]",relevance:0},r.APOS_STRING_MODE={className:"string",begin:"'",end:"'",illegal:"\\n",contains:[r.BACKSLASH_ESCAPE]},r.QUOTE_STRING_MODE={className:"string",begin:'"',end:'"',illegal:"\\n",contains:[r.BACKSLASH_ESCAPE]},r.PHRASAL_WORDS_MODE={begin:/\b(a|an|the|are|I'm|isn't|don't|doesn't|won't|but|just|should|pretty|simply|enough|gonna|going|wtf|so|such|will|you|your|they|like|more)\b/},r.COMMENT=function(e,t,n){var a=r.inherit({className:"comment",begin:e,end:t,contains:[]},n||{});return a.contains.push(r.PHRASAL_WORDS_MODE),a.contains.push({className:"doctag",begin:"(?:TODO|FIXME|NOTE|BUG|XXX):",relevance:0}),a},r.C_LINE_COMMENT_MODE=r.COMMENT("//","$"),r.C_BLOCK_COMMENT_MODE=r.COMMENT("/\\*","\\*/"),r.HASH_COMMENT_MODE=r.COMMENT("#","$"),r.NUMBER_MODE={className:"number",begin:r.NUMBER_RE,relevance:0},r.C_NUMBER_MODE={className:"number",begin:r.C_NUMBER_RE,relevance:0},r.BINARY_NUMBER_MODE={className:"number",begin:r.BINARY_NUMBER_RE,relevance:0},r.CSS_NUMBER_MODE={className:"number",begin:r.NUMBER_RE+"(%|em|ex|ch|rem|vw|vh|vmin|vmax|cm|mm|in|pt|pc|px|deg|grad|rad|turn|s|ms|Hz|kHz|dpi|dpcm|dppx)?",relevance:0},r.REGEXP_MODE={className:"regexp",begin:/\//,end:/\/[gimuy]*/,illegal:/\n/,contains:[r.BACKSLASH_ESCAPE,{begin:/\[/,end:/\]/,relevance:0,contains:[r.BACKSLASH_ESCAPE]}]},r.TITLE_MODE={className:"title",begin:r.IDENT_RE,relevance:0},r.UNDERSCORE_TITLE_MODE={className:"title",begin:r.UNDERSCORE_IDENT_RE,relevance:0},r.METHOD_GUARD={begin:"\\.\\s*"+r.UNDERSCORE_IDENT_RE,relevance:0},r},r="object"==typeof window&&window||"object"==typeof self&&self,void 0===n||n.nodeType?r&&(r.hljs=a({}),"function"==typeof define&&define.amd&&define([],function(){return r.hljs})):a(n)},{}],3:[function(e,t,n){t.exports=function(e){var t={className:"variable",variants:[{begin:/\$[\w\d#@][\w\d_]*/},{begin:/\$\{(.*?)}/}]},n={className:"string",begin:/"/,end:/"/,contains:[e.BACKSLASH_ESCAPE,t,{className:"variable",begin:/\$\(/,end:/\)/,contains:[e.BACKSLASH_ESCAPE]}]};return{aliases:["sh","zsh"],lexemes:/\b-?[a-z\._]+\b/,keywords:{keyword:"if then else elif fi for while in do done case esac function",literal:"true false",built_in:"break cd continue eval exec exit export getopts hash pwd readonly return shift test times trap umask unset alias bind builtin caller command declare echo enable help let local logout mapfile printf read readarray source type typeset ulimit unalias set shopt autoload bg bindkey bye cap chdir clone comparguments compcall compctl compdescribe compfiles compgroups compquote comptags comptry compvalues dirs disable disown echotc echoti emulate fc fg float functions getcap getln history integer jobs kill limit log noglob popd print pushd pushln rehash sched setcap setopt stat suspend ttyctl unfunction unhash unlimit unsetopt vared wait whence where which zcompile zformat zftp zle zmodload zparseopts zprof zpty zregexparse zsocket zstyle ztcp",_:"-ne -eq -lt -gt -f -d -e -s -l -a"},contains:[{className:"meta",begin:/^#![^\n]+sh\s*$/,relevance:10},{className:"function",begin:/\w[\w\d_]*\s*\(\s*\)\s*\{/,returnBegin:!0,contains:[e.inherit(e.TITLE_MODE,{begin:/\w[\w\d_]*/})],relevance:0},e.HASH_COMMENT_MODE,n,{className:"",begin:/\\"/},{className:"string",begin:/'/,end:/'/},t]}}},{}],4:[function(e,t,n){t.exports=function(e){var t={begin:"\\$+[a-zA-Z_-ÿ][a-zA-Z0-9_-ÿ]*"},n={className:"meta",begin:/<\?(php)?|\?>/},a={className:"string",contains:[e.BACKSLASH_ESCAPE,n],variants:[{begin:'b"',end:'"'},{begin:"b'",end:"'"},e.inherit(e.APOS_STRING_MODE,{illegal:null}),e.inherit(e.QUOTE_STRING_MODE,{illegal:null})]},r={variants:[e.BINARY_NUMBER_MODE,e.C_NUMBER_MODE]};return{aliases:["php","php3","php4","php5","php6","php7"],case_insensitive:!0,keywords:"and include_once list abstract global private echo interface as static endswitch array null if endwhile or const for endforeach self var while isset public protected exit foreach throw elseif include __FILE__ empty require_once do xor return parent clone use __CLASS__ __LINE__ else break print eval new catch __METHOD__ case exception default die require __FUNCTION__ enddeclare final try switch continue endfor endif declare unset true false trait goto instanceof insteadof __DIR__ __NAMESPACE__ yield finally",contains:[e.HASH_COMMENT_MODE,e.COMMENT("//","$",{contains:[n]}),e.COMMENT("/\\*","\\*/",{contains:[{className:"doctag",begin:"@[A-Za-z]+"}]}),e.COMMENT("__halt_compiler.+?;",!1,{endsWithParent:!0,keywords:"__halt_compiler",lexemes:e.UNDERSCORE_IDENT_RE}),{className:"string",begin:/<<<['"]?\w+['"]?$/,end:/^\w+;?$/,contains:[e.BACKSLASH_ESCAPE,{className:"subst",variants:[{begin:/\$\w+/},{begin:/\{\$/,end:/\}/}]}]},n,{className:"keyword",begin:/\$this\b/},t,{begin:/(::|->)+[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/},{className:"function",beginKeywords:"function",end:/[;{]/,excludeEnd:!0,illegal:"\\$|\\[|%",contains:[e.UNDERSCORE_TITLE_MODE,{className:"params",begin:"\\(",end:"\\)",contains:["self",t,e.C_BLOCK_COMMENT_MODE,a,r]}]},{className:"class",beginKeywords:"class interface",end:"{",excludeEnd:!0,illegal:/[:\(\$"]/,contains:[{beginKeywords:"extends implements"},e.UNDERSCORE_TITLE_MODE]},{beginKeywords:"namespace",end:";",illegal:/[\.']/,contains:[e.UNDERSCORE_TITLE_MODE]},{beginKeywords:"use",end:";",contains:[e.UNDERSCORE_TITLE_MODE]},{begin:"=>"},a,r]}}},{}],5:[function(e,t,n){t.exports=function(e){var t=e.COMMENT("--","$");return{case_insensitive:!0,illegal:/[<>{}*]/,contains:[{beginKeywords:"begin end start commit rollback savepoint lock alter create drop rename call delete do handler insert load replace select truncate update set show pragma grant merge describe use explain help declare prepare execute deallocate release unlock purge reset change stop analyze cache flush optimize repair kill install uninstall checksum restore check backup revoke comment values with",end:/;/,endsWithParent:!0,lexemes:/[\w\.]+/,keywords:{keyword:"as abort abs absolute acc acce accep accept access accessed accessible account acos action activate add addtime admin administer advanced advise aes_decrypt aes_encrypt after agent aggregate ali alia alias all allocate allow alter always analyze ancillary and anti any anydata anydataset anyschema anytype apply archive archived archivelog are as asc ascii asin assembly assertion associate asynchronous at atan atn2 attr attri attrib attribu attribut attribute attributes audit authenticated authentication authid authors auto autoallocate autodblink autoextend automatic availability avg backup badfile basicfile before begin beginning benchmark between bfile bfile_base big bigfile bin binary_double binary_float binlog bit_and bit_count bit_length bit_or bit_xor bitmap blob_base block blocksize body both bound bucket buffer_cache buffer_pool build bulk by byte byteordermark bytes cache caching call calling cancel capacity cascade cascaded case cast catalog category ceil ceiling chain change changed char_base char_length character_length characters characterset charindex charset charsetform charsetid check checksum checksum_agg child choose chr chunk class cleanup clear client clob clob_base clone close cluster_id cluster_probability cluster_set clustering coalesce coercibility col collate collation collect colu colum column column_value columns columns_updated comment commit compact compatibility compiled complete composite_limit compound compress compute concat concat_ws concurrent confirm conn connec connect connect_by_iscycle connect_by_isleaf connect_by_root connect_time connection consider consistent constant constraint constraints constructor container content contents context contributors controlfile conv convert convert_tz corr corr_k corr_s corresponding corruption cos cost count count_big counted covar_pop covar_samp cpu_per_call cpu_per_session crc32 create creation critical cross cube cume_dist curdate current current_date current_time current_timestamp current_user cursor curtime customdatum cycle data database databases datafile datafiles datalength date_add date_cache date_format date_sub dateadd datediff datefromparts datename datepart datetime2fromparts day day_to_second dayname dayofmonth dayofweek dayofyear days db_role_change dbtimezone ddl deallocate declare decode decompose decrement decrypt deduplicate def defa defau defaul default defaults deferred defi defin define degrees delayed delegate delete delete_all delimited demand dense_rank depth dequeue des_decrypt des_encrypt des_key_file desc descr descri describ describe descriptor deterministic diagnostics difference dimension direct_load directory disable disable_all disallow disassociate discardfile disconnect diskgroup distinct distinctrow distribute distributed div do document domain dotnet double downgrade drop dumpfile duplicate duration each edition editionable editions element ellipsis else elsif elt empty enable enable_all enclosed encode encoding encrypt end end-exec endian enforced engine engines enqueue enterprise entityescaping eomonth error errors escaped evalname evaluate event eventdata events except exception exceptions exchange exclude excluding execu execut execute exempt exists exit exp expire explain explode export export_set extended extent external external_1 external_2 externally extract failed failed_login_attempts failover failure far fast feature_set feature_value fetch field fields file file_name_convert filesystem_like_logging final finish first first_value fixed flash_cache flashback floor flush following follows for forall force foreign form forma format found found_rows freelist freelists freepools fresh from from_base64 from_days ftp full function general generated get get_format get_lock getdate getutcdate global global_name globally go goto grant grants greatest group group_concat group_id grouping grouping_id groups gtid_subtract guarantee guard handler hash hashkeys having hea head headi headin heading heap help hex hierarchy high high_priority hosts hour hours http id ident_current ident_incr ident_seed identified identity idle_time if ifnull ignore iif ilike ilm immediate import in include including increment index indexes indexing indextype indicator indices inet6_aton inet6_ntoa inet_aton inet_ntoa infile initial initialized initially initrans inmemory inner innodb input insert install instance instantiable instr interface interleaved intersect into invalidate invisible is is_free_lock is_ipv4 is_ipv4_compat is_not is_not_null is_used_lock isdate isnull isolation iterate java join json json_exists keep keep_duplicates key keys kill language large last last_day last_insert_id last_value lateral lax lcase lead leading least leaves left len lenght length less level levels library like like2 like4 likec limit lines link list listagg little ln load load_file lob lobs local localtime localtimestamp locate locator lock locked log log10 log2 logfile logfiles logging logical logical_reads_per_call logoff logon logs long loop low low_priority lower lpad lrtrim ltrim main make_set makedate maketime managed management manual map mapping mask master master_pos_wait match matched materialized max maxextents maximize maxinstances maxlen maxlogfiles maxloghistory maxlogmembers maxsize maxtrans md5 measures median medium member memcompress memory merge microsecond mid migration min minextents minimum mining minus minute minutes minvalue missing mod mode model modification modify module monitoring month months mount move movement multiset mutex name name_const names nan national native natural nav nchar nclob nested never new newline next nextval no no_write_to_binlog noarchivelog noaudit nobadfile nocheck nocompress nocopy nocycle nodelay nodiscardfile noentityescaping noguarantee nokeep nologfile nomapping nomaxvalue nominimize nominvalue nomonitoring none noneditionable nonschema noorder nopr nopro noprom nopromp noprompt norely noresetlogs noreverse normal norowdependencies noschemacheck noswitch not nothing notice notnull notrim novalidate now nowait nth_value nullif nulls num numb numbe nvarchar nvarchar2 object ocicoll ocidate ocidatetime ociduration ociinterval ociloblocator ocinumber ociref ocirefcursor ocirowid ocistring ocitype oct octet_length of off offline offset oid oidindex old on online only opaque open operations operator optimal optimize option optionally or oracle oracle_date oradata ord ordaudio orddicom orddoc order ordimage ordinality ordvideo organization orlany orlvary out outer outfile outline output over overflow overriding package pad parallel parallel_enable parameters parent parse partial partition partitions pascal passing password password_grace_time password_lock_time password_reuse_max password_reuse_time password_verify_function patch path patindex pctincrease pctthreshold pctused pctversion percent percent_rank percentile_cont percentile_disc performance period period_add period_diff permanent physical pi pipe pipelined pivot pluggable plugin policy position post_transaction pow power pragma prebuilt precedes preceding precision prediction prediction_cost prediction_details prediction_probability prediction_set prepare present preserve prior priority private private_sga privileges procedural procedure procedure_analyze processlist profiles project prompt protection public publishingservername purge quarter query quick quiesce quota quotename radians raise rand range rank raw read reads readsize rebuild record records recover recovery recursive recycle redo reduced ref reference referenced references referencing refresh regexp_like register regr_avgx regr_avgy regr_count regr_intercept regr_r2 regr_slope regr_sxx regr_sxy reject rekey relational relative relaylog release release_lock relies_on relocate rely rem remainder rename repair repeat replace replicate replication required reset resetlogs resize resource respect restore restricted result result_cache resumable resume retention return returning returns reuse reverse revoke right rlike role roles rollback rolling rollup round row row_count rowdependencies rowid rownum rows rtrim rules safe salt sample save savepoint sb1 sb2 sb4 scan schema schemacheck scn scope scroll sdo_georaster sdo_topo_geometry search sec_to_time second seconds section securefile security seed segment select self semi sequence sequential serializable server servererror session session_user sessions_per_user set sets settings sha sha1 sha2 share shared shared_pool short show shrink shutdown si_averagecolor si_colorhistogram si_featurelist si_positionalcolor si_stillimage si_texture siblings sid sign sin size size_t sizes skip slave sleep smalldatetimefromparts smallfile snapshot some soname sort soundex source space sparse spfile split sql sql_big_result sql_buffer_result sql_cache sql_calc_found_rows sql_small_result sql_variant_property sqlcode sqldata sqlerror sqlname sqlstate sqrt square standalone standby start starting startup statement static statistics stats_binomial_test stats_crosstab stats_ks_test stats_mode stats_mw_test stats_one_way_anova stats_t_test_ stats_t_test_indep stats_t_test_one stats_t_test_paired stats_wsr_test status std stddev stddev_pop stddev_samp stdev stop storage store stored str str_to_date straight_join strcmp strict string struct stuff style subdate subpartition subpartitions substitutable substr substring subtime subtring_index subtype success sum suspend switch switchoffset switchover sync synchronous synonym sys sys_xmlagg sysasm sysaux sysdate sysdatetimeoffset sysdba sysoper system system_user sysutcdatetime table tables tablespace tablesample tan tdo template temporary terminated tertiary_weights test than then thread through tier ties time time_format time_zone timediff timefromparts timeout timestamp timestampadd timestampdiff timezone_abbr timezone_minute timezone_region to to_base64 to_date to_days to_seconds todatetimeoffset trace tracking transaction transactional translate translation treat trigger trigger_nestlevel triggers trim truncate try_cast try_convert try_parse type ub1 ub2 ub4 ucase unarchived unbounded uncompress under undo unhex unicode uniform uninstall union unique unix_timestamp unknown unlimited unlock unnest unpivot unrecoverable unsafe unsigned until untrusted unusable unused update updated upgrade upped upper upsert url urowid usable usage use use_stored_outlines user user_data user_resources users using utc_date utc_timestamp uuid uuid_short validate validate_password_strength validation valist value values var var_samp varcharc vari varia variab variabl variable variables variance varp varraw varrawc varray verify version versions view virtual visible void wait wallet warning warnings week weekday weekofyear wellformed when whene whenev wheneve whenever where while whitespace window with within without work wrapped xdb xml xmlagg xmlattributes xmlcast xmlcolattval xmlelement xmlexists xmlforest xmlindex xmlnamespaces xmlpi xmlquery xmlroot xmlschema xmlserialize xmltable xmltype xor year year_to_month years yearweek",literal:"true false null unknown",built_in:"array bigint binary bit blob bool boolean char character date dec decimal float int int8 integer interval number numeric real record serial serial8 smallint text time timestamp tinyint varchar varying void"},contains:[{className:"string",begin:"'",end:"'",contains:[e.BACKSLASH_ESCAPE,{begin:"''"}]},{className:"string",begin:'"',end:'"',contains:[e.BACKSLASH_ESCAPE,{begin:'""'}]},{className:"string",begin:"`",end:"`",contains:[e.BACKSLASH_ESCAPE]},e.C_NUMBER_MODE,e.C_BLOCK_COMMENT_MODE,t,e.HASH_COMMENT_MODE]},e.C_BLOCK_COMMENT_MODE,t,e.HASH_COMMENT_MODE]}}},{}]},{},[1]);