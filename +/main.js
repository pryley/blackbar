import hljs from '@highlightjs/cdn-assets/es/core.js';
import php from '@highlightjs/cdn-assets/es/languages/php.min.js';
import sql from '@highlightjs/cdn-assets/es/languages/sql.min.js';

hljs.registerLanguage('php', php)
hljs.registerLanguage('sql', sql)
hljs.configure({
    cssSelector: '#glbb pre code',
    ignoreUnescapedHTML: true,
})

class Blackbar {
    constructor () {
        this.current = null;
        this.el = document.getElementById('glbb');
        this.panels = document.querySelectorAll('.glbb-panel');
    }

    close () {
        if (this.el) {
            this.el.style.display = 'none';
            document.querySelector('body').classList.remove('blackbar')
        }
    }

    formatTime (ns) {
        if (1e9 <= ns) {
            return this.nsToSeconds(ns) + ' s';
        }
        if (1e6 <= ns) {
            return this.nsToMiliseconds(ns) + ' ms';
        }
        if (1e3 <= ns) {
            return this.nsToMicroseconds(ns) + ' µs';
        }
        return ns + ' µs';
    }

    get (key, fallback) {
        const value = localStorage.getItem(key);
        if ('string' !== typeof value) {
            return fallback
        }
        try {
            return JSON.parse(value)
        } catch (err) {
            return value || fallback
        }
    }

    set (key, val) {
        localStorage.setItem(key, JSON.stringify(val))
    }

    switchPanel (id) {
        this.panels.forEach(el => {
            el.classList.add('glbb-hidden')
            const a = this.el.querySelector(`a.${el.id}`);
            a.classList.remove('glbb-active')
            a.blur()
            a.hideFocus = true;
        })
        if (id !== this.current) {
            this.current = id;
            this.el.querySelector(`#${id}`).classList.remove('glbb-hidden')
            this.el.querySelector(`a.${id}`).classList.add('glbb-active')
        } else {
            this.current = null;
        }
    }

    msToNanoseconds (ms) {
        return Math.round(ms) * 1e6;
    }

    nsToMicroseconds (ns) {
        return Math.round(ns / 1e3)
    }

    nsToMiliseconds (ns) {
        return parseFloat((ns / 1e6).toFixed(2))
    }

    nsToSeconds (ns) {
        return parseFloat((ns / 1e9).toFixed(2))
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const blackbar = new Blackbar();
    if (!blackbar.el) return;

    // Apply syntax highlighting
    setTimeout(() => hljs.highlightAll(), 250)

    // Toggle Blackbar
    const toggleEl = blackbar.el.querySelector('a.glbb-toggle');
    if (toggleEl) {
        const onClick = (ev) => {
            ev.preventDefault()
            const el = ev.currentTarget;
            if (blackbar.el.classList.contains('glbb-mini')) {
                blackbar.el.classList.remove('glbb-mini')
                blackbar.set('glbb_mini', false)
            } else {
                blackbar.panels.forEach(el => el.classList.add('glbb-hidden'))
                blackbar.el.querySelectorAll('a').forEach(el => el.classList.remove('glbb-active'))
                blackbar.el.classList.add('glbb-mini')
                blackbar.set('glbb_mini', true)
            }
            el.blur()
            el.hideFocus = true;
        };
        toggleEl.addEventListener('click', onClick)
        if (true === blackbar.get('glbb_mini')) {
            toggleEl.click()
        }
    }

    // Toggle Blackbar panels
    blackbar.el.querySelectorAll('a[data-panel]').forEach(el => {
        el.addEventListener('click', ev => {
            ev.preventDefault()
            blackbar.switchPanel(ev.currentTarget.dataset.panel)
        })
    })

    // Close Blackbar panels on ESC
    document.body.addEventListener('keydown', (ev) => {
        if (27 === ev.which && null !== blackbar.current) {
            blackbar.switchPanel(blackbar.current)
        }
    });

    // Close Blackbar
    const closeEl = blackbar.el.querySelector('a.glbb-close');
    if (closeEl) {
        closeEl.addEventListener('click', ev => {
            ev.preventDefault()
            blackbar.close()
        })
    }

    // Toggle row details
    blackbar.el.querySelectorAll('.glbb-row-toggle').forEach(el => {
        el.addEventListener('click', ev => {
            const tr = ev.currentTarget.closest('tr');
            if (tr) {
                tr.classList.toggle('glbb-row-collapsed')
            }
        })
    })

    // Console filters
    const consoleFilters = blackbar.el.querySelectorAll('#glbb-console [type=checkbox]');
    let consoleLevels = blackbar.get('glbb_console_levels', []);
    if ('object' !== typeof consoleLevels || 0 === consoleLevels.length) {
        consoleLevels = [];
        consoleFilters.forEach(el => { // enable all levels by default
            el.checked = true;
            consoleLevels.push(el.value)
        })
        blackbar.set('glbb_console_levels', consoleLevels)
    }
    const applyConsoleFilter = (value, checked) => {
        const set = new Set(consoleLevels);
        set[checked ? 'add' : 'delete'](value)
        consoleLevels = [...set];
        blackbar.el.querySelectorAll('#glbb-console tr[data-errname]').forEach(tr => {
            let level = tr.dataset.errname;
            tr.classList.remove('glbb-hidden')
            if (~['alert','critical','emergency'].indexOf(level)) {
                level = 'error';
            }
            if (!~consoleLevels.indexOf(level)) {
                tr.classList.add('glbb-hidden')
            }
        })
    }
    const onConsoleFilter = (ev) => {
        applyConsoleFilter(ev.target.value, ev.target.checked)
        blackbar.set('glbb_console_levels', consoleLevels)
    }
    consoleFilters.forEach(el => {
        el.addEventListener('click', onConsoleFilter)
        consoleLevels.forEach(level => {
            applyConsoleFilter(level, true)
            if (level === el.value) {
                el.checked = true;
            }
        })
    })

    // Hooks filters
    const hooksCallback = blackbar.el.querySelector('#glbb_hooks_callback');
    const hooksMinTime = blackbar.el.querySelector('#glbb_hooks_min_time');
    const hooksSortBy = blackbar.el.querySelector('#glbb_hooks_sort_by');
    if (hooksCallback && hooksMinTime && hooksSortBy) {
        const onHooksKeyup = (ev) => {
            ev.preventDefault()
            let ns = 0;
            let query = hooksCallback.value;
            let time = parseFloat(hooksMinTime.value);
            blackbar.el.querySelectorAll('#glbb-hooks tr[data-time]').forEach(tr => {
                let minTimeFilter = parseFloat(tr.dataset.time);
                let timeResult = time > 0 && minTimeFilter < blackbar.msToNanoseconds(time);
                if (timeResult) {
                    tr.classList.add('glbb-hidden')
                } else {
                    tr.classList.remove('glbb-hidden')
                    ns += minTimeFilter;
                }
                const opacity = query.length > 0 ? .5 : 1;
                tr.querySelectorAll('li').forEach(li => {
                    let queryCallback = li.textContent.indexOf(query);
                    let queryResult = query.length > 0 && queryCallback === -1;
                    if (queryResult) {
                        li.classList.add('glbb-hidden')
                    } else {
                        li.classList.remove('glbb-hidden')
                    }
                })
                tr.querySelectorAll('ol').forEach(ol => {
                    let children = [].slice.call(ol.children).filter(li => !li.classList.contains('glbb-hidden'));
                    let tr = ol.closest('tr');
                    if (0 === children.length) {
                        tr.classList.add('glbb-hidden')
                        ns -= parseFloat(tr.dataset.time);
                    }
                })
                tr.querySelectorAll('td:not(:first-child,:last-child)').forEach(el => {
                    el.style.opacity = opacity;
                })
            });
            blackbar.el.querySelector('a.glbb-hooks').dataset.info = blackbar.formatTime(ns);
        };
        const onHooksChange = (ev) => {
            ev.preventDefault()
            const sortby = ev.target.value;
            const table = blackbar.el.querySelector('#glbb-hooks tbody');
            table.append(...[...table.children].sort((a,b) => {
                if ('order' === sortby) {
                    return a.dataset.index.localeCompare(b.dataset.index, undefined, {'numeric': true})
                }
                return b.dataset.time.localeCompare(a.dataset.time, undefined, {'numeric': true})
            }))
        }
        hooksMinTime.addEventListener('keyup', onHooksKeyup)
        hooksCallback.addEventListener('keyup', onHooksKeyup)
        hooksSortBy.addEventListener('change', onHooksChange)
        hooksMinTime.value = '';
        hooksCallback.value = '';
        hooksSortBy.value = '';
    }

    // SQL filters
    const queriesSql = blackbar.el.querySelector('#glbb_queries_sql');
    const queriesMinTime = blackbar.el.querySelector('#glbb_queries_min_time');
    const queriesSortBy = blackbar.el.querySelector('#glbb_queries_sort_by');
    if (queriesSql && queriesMinTime && queriesSortBy) {
        const onQueriesKeyup = (ev) => {
            let ns = 0;
            let query = queriesSql.value;
            let time = parseFloat(queriesMinTime.value);
            blackbar.el.querySelectorAll('#glbb-queries tr[data-time]').forEach(tr => {
                let minTimeFilter = parseFloat(tr.dataset.time);
                let queriesSql = tr.querySelector('[data-sql]').textContent.indexOf(query);
                let timeResult = time > 0 && minTimeFilter < blackbar.msToNanoseconds(time);
                let queryResult = query.length > 0 && queriesSql == -1;
                if (timeResult || queryResult) {
                    tr.classList.add('glbb-hidden')
                } else {
                    tr.classList.remove('glbb-hidden')
                    ns += minTimeFilter;
                }
            })
            blackbar.el.querySelector('a.glbb-queries').dataset.info = blackbar.formatTime(ns);
        };
        const onQueriesChange = (ev) => {
            ev.preventDefault()
            const sortby = ev.target.value;
            const table = blackbar.el.querySelector('#glbb-queries tbody');
            table.append(...[...table.children].sort((a,b) => {
                if ('order' === sortby) {
                    return a.dataset.index.localeCompare(b.dataset.index, undefined, {'numeric': true})
                }
                return b.dataset.time.localeCompare(a.dataset.time, undefined, {'numeric': true})
            }))
        }
        queriesSql.addEventListener('keyup', onQueriesKeyup)
        queriesMinTime.addEventListener('keyup', onQueriesKeyup)
        queriesSortBy.addEventListener('change', onQueriesChange)
        queriesSql.value = '';
        queriesMinTime.value = '';
        queriesSortBy.value = '';
    }
});
