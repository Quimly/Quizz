$(function () {
    $('.add-another-collection-widget').click(function (e) {

        e.preventDefault();

        let prot = $(this).attr('data-list');


        let list = $($(this).attr('data-list'));
        // Try to find the counter of the list
        let counter = list.data('widget-counter') | list.children().length;
        // If the counter does not exist, use the length of the list
        if (!counter) { counter = list.children().length; }

        // grab the prototype template
        let newWidget = list.attr('data-prototype');

        // newWidget.append('<a href="#" class="remove-tag">x</a>');

        console.log(newWidget);

        // replace the "__name__" used in the id and name of the prototype
        // with a number that's unique to your emails
        // end name attribute looks like name="contact[emails][2]"
        newWidget = newWidget.replace(/__name__/g, counter);


        // Increase the counter
        counter++;
        // And store it, the length cannot be used if deleting widgets is allowed
        list.data(' widget-counter', counter);

        $(newWidget).append('<a href="#" class="remove-tag">x</a>');

        // create a new list element and add it to the list
        let newElem = $(list.attr('data-widget-tags')).html(newWidget);
        newElem.append('<a href="#" class="remove-tag">x</a>');
        newElem.appendTo(list);


        $('.remove-tag').click(function(e) {

            e.preventDefault();

            $(this).parent().remove();

            return false;
        });
    });
});