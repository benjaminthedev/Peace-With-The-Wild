jQuery(function ($) {
    $(document).ready(init);

    function init() {
        $('[data-echld-search-toggle="echld-mobile-search"]').bind('click.echld-mobile-toggle-search', expandSearch);
    }

    function toggleSearch() {
        const switcher = $(this);
        const name = switcher.attr('data-echld-search-toggle');
        const status = switcher.attr('data-echld-search-status');

        const statusesTransition = {
            'collapsed': 'expanded',
            'expanded': 'collapsed'
        };
        const nextStatus = statusesTransition[status];

        switcher.attr('data-echld-search-status', nextStatus);

        const target = $(`[data-echld-search-target="${name}"]`);
        target.attr('data-echld-search-status', nextStatus);

        const collapseListenersMapping = {
            'expanded': addCollapseSearchByClickingOutsideListener,
            'collapsed': removeCollapseSearchByClickingOutsideListener
        };

        collapseListenersMapping[nextStatus](target);
    }

    function expandSearch() {
        $('[data-echld-search-toggle="echld-mobile-search"]').unbind('click.echld-mobile-toggle-search');

        const switcher = $(this);
        const name = switcher.attr('data-echld-search-toggle');
        const nextStatus = 'expanded';

        switcher.attr('data-echld-search-status', nextStatus);

        const target = $(`[data-echld-search-target="${name}"]`);
        target.attr('data-echld-search-status', nextStatus);

        addCollapseSearchByClickingOutsideListener(target);
    }

    function collapseSearch(name) {
        const switcher = $(`[data-echld-search-toggle="${name}"]`);
        const nextStatus = 'collapsed';

        switcher.attr('data-echld-search-status', nextStatus);

        const target = $(`[data-echld-search-target="${name}"]`);
        target.attr('data-echld-search-status', nextStatus);

        removeCollapseSearchByClickingOutsideListener(target);

        let toProcessToggleSearch = false;
        $('[data-echld-search-toggle="echld-mobile-search"]').bind('click.echld-mobile-toggle-search', (event) => {
            if ( ! toProcessToggleSearch) {
                toProcessToggleSearch = true;
                return;
            }
            expandSearch.bind(this)(event);
        });
    }

    function addCollapseSearchByClickingOutsideListener(target) {
        setTimeout(() => {
            const name = target.attr(`data-echld-search-target`);
            $(document).bind(
                `touchend.echld-search.${name} click.echld-search.${name}`,
                collapseSearchByClickingOutside.bind(target, target, name)
            );
        }, 1)
    }

    function removeCollapseSearchByClickingOutsideListener(target) {
        const name = target.attr(`data-echld-search-target`);
        $(document).unbind(`touchend.echld-search.${name} click.echld-search.${name}`);
    }

    function collapseSearchByClickingOutside(target, name, event) {
        const container = $(target);

        if (!container.is(event.target) && container.has(event.target).length === 0) {
            event.stopPropagation();
            event.stopImmediatePropagation();

            collapseSearch(name);
        }
    }
});