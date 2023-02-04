import hljs from '@highlightjs/cdn-assets/es/core.js';
import php from '@highlightjs/cdn-assets/es/languages/php.min.js';
import sql from '@highlightjs/cdn-assets/es/languages/sql.min.js';

hljs.registerLanguage('php', php)
hljs.registerLanguage('sql', sql)
hljs.configure({
    cssSelector: '#glbb-debug-bar pre code',
    ignoreUnescapedHTML: true,
})

window.Blackbar = {
    activeClass: 'glbb-active',
    element: [
        'glbb-actions',
        'glbb-console',
        'glbb-globals',
        'glbb-profiler',
        'glbb-queries',
        'glbb-templates',
    ],
    hljs,
    id: 'glbb-debug-bar',
    open: null,

    close: function() {
        document.getElementById( Blackbar.id ).style.display = 'none';
        document.querySelector( 'body' ).classList.remove( 'blackbar' );
    },

    createCookie: function( name, value, days ) {
        var expires;
        if( days ) {
            var date = new Date();
            date.setTime( date.getTime() + ( days * 24 * 60 * 60 * 1000 ));
            expires = '; expires=' + date.toGMTString();
        }
        else {
            expires = '';
        }
        document.cookie = name + '=' + value + expires + '; path=/';
    },

    eraseCookie: function( name ) {
        createCookie( name, '', -1 );
    },

    readCookie: function( name ) {
        var nameEQ = name + '=';
        var ca = document.cookie.split( ';' );
        for( var i = 0; i < ca.length; i++ ) {
            var c = ca[i];
            while( c.charAt(0) === ' ' ) {
                c = c.substring( 1, c.length );
            }
            if( c.indexOf( nameEQ ) === 0 ) {
                return c.substring( nameEQ.length, c.length );
            }
        }
        return null;
    },

    switchPanel: function( open ) {
        for( var i in Blackbar.element ) {
            var el = document.getElementById( Blackbar.element[i] );
            if( el ) {
                el.style.display = 'none';
                var a = document.querySelector( 'a.' + Blackbar.element[i] );
                a.classList.remove( Blackbar.activeClass );
                a.blur();
                a.hideFocus = true;
            }
        }
        if( open == Blackbar.open ) {
            Blackbar.open = null;
            return;
        }
        Blackbar.open = open;
        document.getElementById( open ).style.display = 'block';
        document.querySelector( 'a.' + open ).classList.add( Blackbar.activeClass );
    },
};

document.addEventListener('DOMContentLoaded', () => {

    var blackbarEl = document.getElementById( Blackbar.id );
    if( !blackbarEl )return;

    setTimeout(() => hljs.highlightAll(), 250)

    var debugToggle = blackbarEl.querySelector( '.glbb-toggle' );
    var debugFilter = blackbarEl.querySelector( '#glbb_query_filter' );
    var debugMinTime = blackbarEl.querySelector( '#glbb_query_min_time' );

    var onClick = function( ev ) {
        var toggle = ev.target.closest( 'a' );
        if( toggle.classList.contains( 'glbb-off' )) {
            toggle.classList.remove( 'glbb-off' );
            toggle.classList.add( 'glbb-on' );
            toggle.textContent = '';
            blackbarEl.classList.add( 'glbb-mini' );
            blackbarEl.querySelectorAll( '.glbb-debug-panel' ).forEach( function( el ) {
                el.style.display = 'none';
            });
            blackbarEl.querySelectorAll( 'a.glbb-active' ).forEach( function( el ) {
                el.classList.remove( Blackbar.activeClass );
            });
            Blackbar.createCookie( 'glbb-toggle', 'on' );
        }
        else {
            toggle.classList.remove( 'glbb-on' );
            toggle.classList.add( 'glbb-off' );
            toggle.textContent = 'Toggle';
            blackbarEl.classList.remove( 'glbb-mini' );
            Blackbar.createCookie( 'glbb-toggle', 'off' );
        }
        toggle.blur();
        toggle.hideFocus = true;
        ev.preventDefault();
    };
    var onKeyup = function( ev ) {
        var time = parseFloat( debugMinTime.value );
        var query = debugFilter.value;
        var qnum = 0;
        var qtime = 0;
        blackbarEl.querySelectorAll( '#glbb-queries tr' ).forEach( function( tr ) {
            var minTimeFilter = parseFloat( tr.querySelector( '.glbb-small' ).textContent.replace( ' [ms]', '' ));
            var queryFilter = tr.querySelector( '.language-sql' ).textContent.indexOf( query );
            var timeResult = time > 0 && minTimeFilter < time;
            var queryResult = query.length > 0 && queryFilter == -1;

            if( timeResult || queryResult ) {
                tr.style.display = 'none';
            }
            else {
                tr.style.display = '';
                qnum++;
                qtime += minTimeFilter;
            }
        });

        blackbarEl.querySelector( '.glbb-queries-count' ).textContent = qnum;
        blackbarEl.querySelector( '.glbb-queries-time' ).textContent = qtime.toFixed(2);

        Blackbar.createCookie( 'glbb_query_filter', query );
        Blackbar.createCookie( 'glbb_query_min_time', blackbarEl.querySelector( '#glbb_query_min_time' ).value );
    };

    document.body.addEventListener( 'keydown', function( ev ) {
        if( 27 !== ev.which || Blackbar.open == null )return;
        Blackbar.switchPanel( Blackbar.open );
    });

    blackbarEl.querySelectorAll('.glbb-row-toggle').forEach(el => {
        el.addEventListener('click', ev => {
            const row = ev.currentTarget.closest('tr');
            row.classList.toggle('glbb-row-collapsed')
        })
    })

    if( debugFilter && debugMinTime ) {
        debugFilter.addEventListener( 'keyup', onKeyup );
        debugFilter.value = Blackbar.readCookie( 'glbb_query_filter' );
        debugMinTime.addEventListener( 'keyup', onKeyup );
        debugMinTime.value = Blackbar.readCookie( 'glbb_query_min_time' );
    }
    if( debugToggle ) {
        debugToggle.addEventListener( 'click', onClick );
    }
    if( Blackbar.readCookie( 'glbb-toggle' ) === 'on' ) {
        debugToggle.click();
    }


    const actionsCallback = blackbarEl.querySelector('#glbb_actions_callback');
    const actionsMinTime = blackbarEl.querySelector('#glbb_actions_min_time');
    const onActionsKeyup = (ev) => {
        let time = parseFloat(actionsMinTime.value);
        let qtime = 0;
        blackbarEl.querySelectorAll('#glbb-actions [data-total]').forEach(el => {
            let minTimeFilter = parseFloat(el.dataset.total);
            let timeResult = time > 0 && minTimeFilter < time;
            let parentEl = el.closest('tr');
            if (timeResult) {
                parentEl.classList.add('glbb-row-hidden');
            } else {
                parentEl.classList.remove('glbb-row-hidden');
                qtime += minTimeFilter;
            }
        });
        Blackbar.createCookie('glbb_actions_min_time', blackbarEl.querySelector( '#glbb_actions_min_time').value);
        let query = actionsCallback.value;
        let qnum = 0;
        blackbarEl.querySelectorAll('#glbb-actions td li').forEach(li => {
            let queryFilter = li.textContent.indexOf(query);
            let queryResult = query.length > 0 && queryFilter === -1;
            let parentRow = li.closest('tr').previousElementSibling;
            if (queryResult) {
                li.style.display = 'none';
            } else {
                li.style.display = '';
                qnum++;
            }
        });
        blackbarEl.querySelectorAll('#glbb-actions td ol').forEach(ol => {
            let children = [].slice.call(ol.children).filter(el => 'none' !== getComputedStyle(el).display);
            let parentEl = ol.closest('tr').previousElementSibling;
            if (0 === children.length) {
                parentEl.classList.add('glbb-row-hidden');
            } else {
                parentEl.classList.remove('glbb-row-hidden');
            }
        })
        if (query.length > 0) {
            blackbarEl.querySelectorAll('#glbb-actions tbody th div:not(.glbb-row-toggle)').forEach(el => {
                el.style.opacity = .5;
            })
        } else {
            blackbarEl.querySelectorAll('#glbb-actions tbody th div:not(.glbb-row-toggle)').forEach(el => {
                el.style.opacity = 1;
            })
        }
        Blackbar.createCookie('glbb_actions_callback', query);

        // blackbarEl.querySelector( '.glbb-queries-count' ).textContent = qnum;
        // blackbarEl.querySelector( '.glbb-queries-time' ).textContent = qtime.toFixed(2);
    };

    if (actionsCallback && actionsMinTime) {
        actionsMinTime.addEventListener('keyup', onActionsKeyup);
        actionsMinTime.value = Blackbar.readCookie('glbb_actions_min_time');
        actionsCallback.addEventListener('keyup', onActionsKeyup);
        actionsCallback.value = Blackbar.readCookie( 'glbb_actions_callback');
    }
});
