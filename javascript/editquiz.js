
// http://stackoverflow.com/questions/1403888/get-url-parameter-with-jquery
function getURLParameter(name) {
    return decodeURI(
        (RegExp(name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1]
    );
}

(function() {
    var cmid = getURLParameter('cmid');

    // CATEGORIES
    var category = $('.interactivequiz-questionbank-category');
    var questionbank = $('.interactivequiz-questionbank-questions');

    var category_update = function(category_id, start) {
        $.ajax({
            url: 'ajax_controller.php',
            data: { query: 'category', category: category_id, cmid: cmid, start: start}
        }).done(function(result) {
            questionbank.html(result);
            if(MathJax) {
                MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
            }
            var arrows = $('.interactivequiz-questionbank-arrows span');
            arrows.each(function() {
                var arrow = $(this);
                arrow.click(function() {
                    category_update(arrow.data('category'), arrow.data('start'));
                });
            });
            $('.interactivequiz-questionbank-question').each(function() {
                $(this).draggable({
                    opacity: 0.7,
                    helper: 'clone',
                    start: function() {
                        $('.interactivequiz-builder-placeholder')
                            .addClass('interactivequiz-builder-placeholder-highlight');
                    },
                    stop: function() {
                        $('.interactivequiz-builder-placeholder')
                            .removeClass('interactivequiz-builder-placeholder-highlight');
                    }
                });
            });
        });
    };
    category.change(function() {
        var category_id = category.val();
        category_update(category_id, 0);
    });
    category.change();

    // MAIN BUILDER
    var builder = $('.interactivequiz-builder');

    var builder_update = function() {
        $.ajax({
            url: 'ajax_controller.php',
            data: { query: 'builder', cmid: cmid }
        }).done(function(result) {
            builder.html(result);
            if(MathJax) {
                MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
            }
            // Placeholder events
            $('.interactivequiz-builder-placeholder').droppable({
                hoverClass: 'interactivequiz-builder-placeholder-hover',
                drop: function(event, ui) {
                    var questionid = $(ui.draggable).data('questionid');
                    var order = $(this).data('order');
                    $.ajax({
                        url: 'ajax_controller.php',
                        type: 'POST',
                        data: { query: 'addquestion', cmid: cmid, question: questionid,
                            order: order }
                    }).done(function() {
                        builder_update();
                    });
                }
            });

            // Subquestion placeholder events
            $('.interactivequiz-builder-placeholdersmall').droppable({
                hoverClass: 'interactivequiz-builder-placeholdersmall-hover',
                drop: function(event, ui) {
                    var questionid = $(ui.draggable).data('questionid');
                    var answer = $(this).data('answer');
                    var iquestionid = $(this).data('iquestionid');
                    $.ajax({
                        url: 'ajax_controller.php',
                        type: 'POST',
                        data: { query: 'addsubquestion', cmid: cmid, question: questionid,
                            answer: answer, iquestion: iquestionid }
                    }).done(function() {
                        builder_update();
                    });
                }
            });

            // Delete button
            $('.interactivequiz-builder-question-delete').click(function() {
                var confirmText = $(this).next('.interactivequiz-builder-question-delete-confirm');
                if(confirmText.is(':visible')) {
                    var iquestionid = $(this).data('iquestionid');
                    $.ajax({
                        url: 'ajax_controller.php',
                        type: 'POST',
                        data: { query: 'deletequestion', cmid: cmid, iquestion: iquestionid }
                    }).done(function() {
                        builder_update();
                    });
                } else {
                    confirmText.show();
                }
                return false;
            });

            // Penalty value dropdowns
            $('.interactivequiz-builder-question-answer-subquestion-penalty').change(function() {
                var ianswerid = $(this).data('ianswerid');
                var penalty = $(this).val();
                $.ajax({
                        url: 'ajax_controller.php',
                        type: 'POST',
                        data: { query: 'penalty', cmid: cmid, ianswer: ianswerid, penalty: penalty }
                });
            });
        });
    };
    builder_update();
})();
