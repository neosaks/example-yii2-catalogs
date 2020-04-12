$('.header-widget .cover-image').scroolly([
    {
        to: 'el-bottom = vp-top',
        onScroll: function (element, offset, length) {
            var progress = offset / length;
            
            element.css('background-position', 'center ' + $.scroolly.getTransitionFloatValue(50, 100, progress) + '%');
        }
    }
]);

$('.header-widget .title, .header-widget .description').scroolly([
    {
        to: 'con-bottom = top',
        cssFrom: {
            'text-shadow': '0 0 0px white',
            'transform': 'translateY(0px)',
            'opacity': '1'
        },
        cssTo: {
            'text-shadow': '0 0 30px white',
            'transform': 'translateY(100px)',
            'opacity': '.1'
        }
    }
], $('.header-widget .header-title, .header-widget .description'));