var favorite_array = new Array();
favorite_array = JSON.parse(localStorage.getItem('item_count') ? localStorage.getItem('item_count') : JSON.stringify([]));

Array.prototype.remove = function () {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};

$(document).ready(function () {
    $('span[data-count]').attr('data-count', favorite_array?.length);
})

function favorite_fruit(id, element) {
    if (favorite_array.length < 10) {
        if (element.checked)
            favorite_array.push(id);
        else
            favorite_array.remove(id);
        $('span[data-count]').attr('data-count', favorite_array.length);
        localStorage.setItem('item_count', JSON.stringify(favorite_array));
    } else
        alert('You can not add more than 10.');
}

function go_favorite() {
    var getData = localStorage.getItem('item_count') ? localStorage.getItem('item_count') : JSON.stringify([]);
    location.href = "/favorite?ids=" + getData;
}

