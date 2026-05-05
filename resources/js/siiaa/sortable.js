import Sortable from 'sortablejs';

window.SIIAA = window.SIIAA || {};

window.SIIAA.sortable = function (element, options = {}) {
    if (!element) return null;

    return Sortable.create(element, {
        animation: 150,
        handle: options.handle || '[data-sortable-handle]',
        draggable: options.draggable || '[data-sortable-item]',
        ghostClass: options.ghostClass || 'opacity-40',
        chosenClass: options.chosenClass || 'bg-zinc-50',
        dragClass: options.dragClass || 'cursor-grabbing',

        onEnd(event) {
            const orderedIds = Array.from(element.querySelectorAll('[data-sortable-item]'))
                .map(item => item.dataset.id)
                .filter(Boolean);

            if (typeof options.onSort === 'function') {
                options.onSort(orderedIds, event);
            }
        },
    });
};
