
String.prototype.my = function(search, start) {
    
};

var V = function(name, val) {
    name = my.path + '-my_' + name;
    if ('undefined' === typeof val)
        return localStorage.getItem(name);
    return null === val ? localStorage.removeItem(name) : localStorage.setItem(name, val);
    //localStorage.clear(); ! delete ALL
};


var my = {
    path: '',
    active: ['', ''],
    hole: 0,
    tables: [],
    tbl: (t) => {
        var name = $.trim(t.find('div:eq(0)').html())
        return name
    },
    database: function (tbl, i, act) {
        $('*').attr('unselectable', 'on').css('user-select', 'none').on('selectstart', false);

        var move = 0, sort = 0, xpos, ypos, nt = tbl, clk_title = (e, col, nt2) => {
            $('.my-table').removeAttr('active');
            if (nt2)
                nt = nt2;
            nt.attr('active', 1);
            if (!e || !col) {
                var tt = nt === my.active[0];
                if (!col)
                    col = tt ? my.active[1] : nt.find('.tbl-coln:eq(1)');
                if (!e || !tt)
                    $('.tbl-coln').removeAttr('active');
                col.attr('active', 1);
                my.active = [nt, col];
                V('act_t', my.tables[my.tbl(nt)]);
                if (!e)
                    return;
            }
            move = nt.position();
            xpos = move.left - e.pageX, ypos = move.top - e.pageY;
        };
        if (i == act)
            clk_title();
        tbl.find('div:eq(0)').mousedown(clk_title)
            .on('contextmenu', (e) => {
                //return false;
            });
        tbl.find('.tbl-coln').mousedown(function(e) {
            clk_title(0, $(this));
            ypos = e.pageY;
            for (var q, n, i = 0; q = tbl.find('.tbl-coln:eq(' + i + ')')[0]; i++)
                this !== q || (n = i);
            sort = [n, n, 17 * (i - n - 1)];
        });
        $('body').on('mousemove', (e) => {
            if (move) {
                var left = e.pageX + xpos, top = e.pageY + ypos;
                nt.css({left:left < 0 ? 0 : left, top:top < 0 ? 0 : top});
            } else if (sort) {
                var top = e.pageY - ypos, mod = top % 17;
                top += mod > 9 ? 17 - mod : -mod;
                top = top < -17 * sort[0] ? -17 * sort[0] : (top > sort[2] ? sort[2] : top);
                var n = sort[0] + top / 17;
                tbl.find('.tbl-coln').each((i, el) => {
                    i += n < sort[0] || n == sort[0] && n > sort[1] ? 0 : -1;
                    if (i >= n && i < sort[1])
                        $(el).css('top', parseInt($(el).css('top')) + 17);
                    if (i < n && i >= sort[1])
                        $(el).css('top', parseInt($(el).css('top')) - 17);
                });
                sort[1] = n;
                my.active[1].css('top', top);
            }
            if (1 == my.hole && (e.pageX >= 238 || e.pageY >= 61)) {
                my.hole = 2;
                $('#merc-db').append('<div class="my-table">' + my.active[0].html() + '</div>');
                var tbl2 = $('.my-table:last').css({left:e.pageX-170, top:e.pageY-26});
         //       tbl2.find('div:eq(0)').html('+' + my.tbl(tbl2));
                my.database(tbl2, 777, 777);
                clk_title(e, 0, tbl2);
            }
        }).on('mouseup', (e) => {
            if (move) {
                var left = (e.pageX + xpos) % 17, top = (e.pageY + ypos) % 17;
                xpos += e.pageX + (left < 9 ? -left : (17 - left));
                ypos += e.pageY + (top < 9 ? -top : (17 - top));
                var t = my.tbl(my.active[0]);
                V('t_' + t + '_x', xpos = xpos < 0 ? 0 : xpos);
                V('t_' + t + '_y', ypos = ypos < 0 ? 0 : ypos);
                nt.css({left:xpos, top:ypos});
                if (0 == my.hole && xpos < 68 && ypos < 35) {
                    V('act_t', 0);
                    ajax('drop', {tbl: my.tbl(tbl)}, my.draw);
                }
            } else if (sort) {
                sort[2] = my.tbl(tbl);
                if (sort[0] != sort[1])
                    ajax('alter', {sort:sort}, my.draw);
            }
            if (2 == my.hole) {
                if (xpos < 68 && ypos < 35) {
                    $('.my-table:last').remove();
                } else {
                    ajax('create', {tbl: my.tbl(my.active[0])}, my.draw);
                }
            }
                
            my.hole = move = sort = 0;
        });
    },
    draw: (r) => {
        if (r)
            $('#merc-db').html(r)
    //$('#my-pre').text(my.path)
        var len = 0;
        $.map($.makeArray($('.tbl-name')), (n, i) => {
            my.tables[$.trim(n.innerHTML)] = len = i;
        });
        $('.my-table').each((i, el) => {
            var act = V('act_t') || 0;
                x = V('t_' + my.tbl($(el)) + '_x') || 16 + i * 17,
                y = V('t_' + my.tbl($(el)) + '_y') || 50 + i * 17;
            if (parseInt(act) > len)
                act = 0;
            my.database($(el).css({left:x + 'px', top:y + 'px'}), i, act);
        });
    }
};

(function() {
    sky.a.error(function(r) {
        if (!r.soft)
            location.href = '_exception?' + r.err_no;
        $('#tail').html(r.catch_error);
    });
    sky.err = function(s) {
        $('#tail').html(s);
        //f1[0] ? ab.message(s, 0, 1) : alert(s); // red, no animation
    };
})();

$(function() {
    var m = sky.home.match(/\/\/([^\/]+)\/(([^\/]+)|)/);
    my.path = m[3]
    //my.$f = $('iframe:first');

    //$(window).resize(my.resize);
    //my.resize();

    sky.key[27] = function() { // Escape
        var esc = $('.escape:last');
        esc.get(0) ? esc.click() : run();
    };
    sky.key[113] = function() { // F2
        $('.f2:first').click();
    };
    sky.key[115] = function() { // F4
        $('.f4:first').click();
    };
    sky.key[117] = function() { // F6
        $('.f6:first').click();
    };
    sky.key[119] = function() { // F8
        $('.f8:first').click();
    };
    sky.key[120] = function() { // F9
        my.swap();
    };
    sky.key[121] = my._catch; // F10

    $(document).click(my.m_clk).mouseup(my.m_up).mousemove(my.m_move).mouseenter(function () {
//        my.info('-', 1);
    });
    //my.doc().mouseup(my.m_up).mousemove(my.m_move);

    //var v = V('main');
    if (0) {
        eval('my.switch(' + v + ')');
    } else {
      //  my.menu('t')
    }

    if ('db' == sky.a._1)
        my.draw();
});



       /*         .on('dragstart', (ev) => {
                    alert()
//draggable="true"  style="top:{{50+$_*17}}px; left:{{16+$_ *17}}px" ondragstart="my.dragstart(event)"
                });*/
