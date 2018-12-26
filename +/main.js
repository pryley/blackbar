var hljs = require('highlight');
hljs.registerLanguage('bash', require('bash'));
hljs.registerLanguage('php', require('php'));
hljs.registerLanguage('sql', require('sql'));
module.exports = hljs;

window.Blackbar = {
	activeClass: 'glbb-active',
	element: ['glbb-globals', 'glbb-profiler', 'glbb-queries', 'glbb-templates', 'glbb-console'],
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

document.addEventListener( 'DOMContentLoaded', function() {
	var blocks = document.querySelectorAll( '#glbb-debug-bar pre code' );
	blocks.forEach( function( block ) {
		hljs.highlightBlock( block );
	});
});

document.addEventListener( 'DOMContentLoaded', function() {
	var blackbarEl = document.getElementById( Blackbar.id );
	if( !blackbarEl )return;
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
			var queryFilter = tr.querySelector( '.sql' ).textContent.indexOf( query );
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

	debugToggle.addEventListener( 'click', onClick );
	debugFilter.addEventListener( 'keyup', onKeyup );
	debugMinTime.addEventListener( 'keyup', onKeyup );

	// init
	if( Blackbar.readCookie( 'glbb-toggle' ) === 'on' ) {
		debugToggle.click();
	}

	debugFilter.value = Blackbar.readCookie( 'glbb_query_filter' );
	debugMinTime.value = Blackbar.readCookie( 'glbb_query_min_time' );
});
