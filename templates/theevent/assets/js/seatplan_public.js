// Official
Date.prototype.clearTime = function() {
    this.setHours(0);
    this.setMinutes(0);
    this.setSeconds(0);
    this.setMilliseconds(0);
    return this;
};

Date.prototype.addMilliseconds = function(value) {
    this.setMilliseconds(this.getMilliseconds() + value);
    return this;
};

Date.prototype.addSeconds = function(value) {
    return this.addMilliseconds(value * 1000);
};


Date.prototype._toString = Date.prototype.toString;
Date.prototype.toString = function(format) {
    var self = this;
    var p = function p(s) {
        return (s.toString().length == 1) ? "0" + s : s;
    };
    return format ? format.replace(/dd?d?d?|MM?M?M?|yy?y?y?|hh?|HH?|mm?|ss?|tt?|zz?z?/g, function(format) {
        switch (format) {
            case "hh":
                return p(self.getHours() < 13 ? self.getHours() : (self.getHours() - 12));
            case "h":
                return self.getHours() < 13 ? self.getHours() : (self.getHours() - 12);
            case "HH":
                return p(self.getHours());
            case "H":
                return self.getHours();
            case "mm":
                return p(self.getMinutes());
            case "m":
                return self.getMinutes();
            case "ss":
                return p(self.getSeconds());
            case "s":
                return self.getSeconds();
            case "yyyy":
                return self.getFullYear();
            case "yy":
                return self.getFullYear().toString().substring(2, 4);
            case "dddd":
                return self.getDayName();
            case "ddd":
                return self.getDayName(true);
            case "dd":
                return p(self.getDate());
            case "d":
                return self.getDate().toString();
            case "MMMM":
                return self.getMonthName();
            case "MMM":
                return self.getMonthName(true);
            case "MM":
                return p((self.getMonth() + 1));
            case "M":
                return self.getMonth() + 1;
            case "t":
                return self.getHours() < 12 ? Date.CultureInfo.amDesignator.substring(0, 1) : Date.CultureInfo.pmDesignator.substring(0, 1);
            case "tt":
                return self.getHours() < 12 ? Date.CultureInfo.amDesignator : Date.CultureInfo.pmDesignator;
            case "zzz":
            case "zz":
            case "z":
                return "";
        }
    }) : this._toString();
};


// toolip functionality
(function($) {
    $.fn.setTooltip = function(val) {
        this[0]._title = val;
    }, $.fn.getTooltip = function() {
        return this[0]._title;
    }, $.fn.easyTooltip = function(options) {
        var defaults = {
            xOffset: 10,
            yOffset: 25,
            tooltipId: "easyTooltip",
            clickRemove: false,
            content: "",
            useElement: ""
        };
        var options = $.extend(defaults, options);
        var content;
        this.each(function() {
            this._title = this.title;
            this.title = '';
            $(this).bind('mouseover', function(e) {
                content = (options.content != "") ? options.content : this._title;
                content = (options.useElement != "") ? $("#" + options.useElement).html() : content;
                if (content != "" && content != undefined) {
                    $("body").append("<div id='" + options.tooltipId + "'>" + content + "</div>");
                    $("#" + options.tooltipId).css("position", "absolute").css("top", (e.pageY - options.yOffset) + "px").css("left", (e.pageX + options.xOffset) + "px").css("display", "none").fadeIn("fast")
                }
            });
            $(this).bind('mouseout', function(e) {
                $("#" + options.tooltipId).remove();
            });
            $(this).mousemove(function(e) {
                $("#" + options.tooltipId).css("top", (e.pageY - options.yOffset) + "px").css("left", (e.pageX + options.xOffset) + "px")
            });
            if (options.clickRemove) {
                $(this).mousedown(function(e) {
                    $("#" + options.tooltipId).remove();
                });
            }
        });
    };
})(jQuery);


window.loader = function() {
    $.ajax({
        url: $.baseurl + "seatplan_ajax.php?mode=load" + $.ie + "&cPath=" + $.cPath,
        dataType: "json",
        success: function(t) {
            t.cart && (t.cart.length > 0 && $.timer === undefined && ($("div#ajax_status").slideDown(200),
            $.timer = setInterval(countdown, 1e3)),
           t.cart.length == 0 && typeof $.timer == "number" && (clearInterval($.timer),
            delete $.timer,
            $("div#ajax_status").slideUp(200).empty(),
            count = $.lifetime),
            $.each(t.cart, function(t, a) {
                $("li#s" + a).hasClass("s") && ($.cls = $("li#s" + a).attr("class").match(/(bl|rd|gr|or|fu|ye|sa|sb|te|th|pg)/gi),
                  $.cls != null && $("li#s" + a).removeClass("s").removeClass($.cls.toString()).addClass("y").addClass(flip($.cls.toString())))
            })),
            t.lock && $.each(t.lock, function(t, a) {
                $("li#s" + a).unbind("click").removeClass("s").addClass("z")
            }),
            t.sold && $.each(t.sold, function(t, a) {
                $("li#s" + a).unbind("click").removeClass("s").removeClass("z").addClass("x")
            }),
            t.prev && $.each(t.prev, function(t, a) {
                $("li#s" + a).unbind("click").removeClass("x").addClass("o")
            }),
            t.shopping_box && ($("#box_ajaxCart").html(t.shopping_box),
            alert(lng.tooslow))
        }
    })
}

window.freeSeats = function() {
    $.ajax({
        url: $.baseurl + "seatplan_ajax.php?mode=free" + $.ie + "&cPath=" + $.cPath,
        dataType: "json",
        success: function(t) {
            t.free && $.each(t.free, function(t, a) {
                $("li#s" + a).hasClass("z") && $("li#s" + a).removeClass("z").addClass("s")
            })
        }
    })
}

window.bindTriggers = function() {
	$(".sxy").bind("click", function() {
		return;
	});
    $(".s").bind("click", function() {
		return;
	}),

    // x click
    $("#ajax_cart").on("click", ".bd",  function() {
		return;
	}),

    $(".c").on("click", function() {
        $.id = $(this).attr("id").replace("c", ""),
        $("li#s" + $.id).lengt == 1 && $("li#s" + $.id).effect("pulsate", {
            times: 2,
            mode: "show"
        }, 360)
    }),
    $("#discount").on("click", ".xx", function() {
		return;
	}), 

	 
    $("#discount").on("mouseout", ".xx", function() {
        $("#discount_choice_text").html("")
    }),
    $("#discount").on("mouseover", ".xx", function() {
        $("#discount_choice_text").html($(this).attr("data-choice_warning"))
    }),
    $("#kill_discount").on("click", function() {
        $("div#ticket_discount").fadeOut()
    }),
    $("input#qty").length == 1 && $("input#qty").bind("keyup", function() {
        isNaN(parseInt($("input#qty").val())) && $("input#qty").val("1"),
        $.qty = parseInt($("input#qty").val()),
        product.discount_id !=0 ? ($.price = (product.saleMaker.sales[product.discount_id].price * $.qty).toFixed(2),
        $("span#totalProductsPrice").html(product.currency.symbolLeft + $.price + product.currency.symbolRight)) : setTotalPrice()
    })
}

window.countdown = function() {
    $("div#ajax_status").html(lng.expiry + " " + (new Date).clearTime().addSeconds(parseInt(count)).toString("mm:ss")),
    count > 0 ? ($.pct = count / $.lifetime * 100,
    $.col = $.pct > 30 ? "Green" : $.pct > 20 ? "Orange" : "Red",
    $("div#ajax_status").css({
        color: $.col
    }),
    count -= 1) : $.ajax({
        url: "seatplan_ajax.php",
        data: "mode=terminate" + $.ie + "&cPath=" + $.cPath,
        dataType: "json",
        success: function() {
            clearInterval($.tick),
            clearInterval($.free),
            clearInterval($.timer),
            $("div#easyTooltip").length > 0 && $("div#easyTooltip").fadeOut(0),
            $("ul#ajax_cart").html('<li class="timedout">' + lng.expired + "</li>"),
            $("div.clear").html('<div id="cleared">' + lng.cleared + "</div>"),
            $("span#total_price").html(lng.sym_left + "0.00" + lng.sym_right),
            $("span#total_seats").html("0 " + lng.seats),
			$("span#total_head_seats").html("0 " + lng.seats),
			$(".ticket_list").html(""),
            $("li#ticket_count span").html("0 " + lng.seats),
            $("div#ajax_status").slideUp(400),
		    $("div#btnCheckOut").fadeOut(200),
            $("div#res_display").fadeOut(200)
        }
    })
}

function mobileLayout() {
    var t = navigator.userAgent;
    ( t.indexOf("Android") != -1 || t.indexOf("iPhone") != -1) && ($("div.nav").children("a").css({
        height: "20px",
        paddingTop: "12px",
        paddingBottom: "8px"
    }),
    $("div.navSelect").children("a").css({
        height: "20px",
        paddingTop: "12px",
        paddingBottom: "8px"
    }),
    $("div.navGroup").children("a").css({
        height: "20px",
        paddingTop: "12px",
        paddingBottom: "8px"
    }))
}

window.flip = function(t) {
    return st = t.split(""),
    rt = st.reverse(),
    rt.join("")
}

//changes added for the vouchers
window.submitData =  function(is_voucher) {
    if (typeof is_voucher !== 'undefined') {
        is_voucher = "&voucher=true"
    } else {
        is_voucher = '';
    }
    isNaN(parseInt($("input#qty").val())) && $("input#qty").val("1"),
    $.sym_left = product.currency.symbolLeft,
    $.sym_right = product.currency.symbolRight,
    $.ajax({
        url: "seatplan_ajax.php",
        data: "mode=ga" + $.ie + "&products_id=" + parseInt(product.id) + "&discount_id=" + parseInt(product.discount_id) + "&quantity=" + parseInt($("input#qty").val()) + "&cPath=" + $.cPath + is_voucher,
        dataType: "json",
        success: function(t) {
            var a = 0;
            if (product.priceBreaks.enabled) {
                var i = t.ga_in_cart;
                obj = product.priceBreaks.prices;
                for (var s in obj)
                    t: if (Object.prototype.hasOwnProperty.call(obj, s)) {
                        var e = obj[s];
                        if (s > i)
                            break t;
                        a = e
                    }
            }

            if (typeof is_voucher !== 'undefined') {
                var overwrite = 1;
            } else {
                var overwrite = t.ga_in_cart;
            }
            t.voucher ? alert(lng.voucher) : t.discount && alert(lng.discount),
            t.max ? alert(lng.toomany) : t.granted && ($.price = product.discount_id == 0 ? parseFloat($("#price").val() - a) : product.saleMaker.sales[product.discount_id].price.toFixed(2),
            $.cht == "" && ($.cht = t.products_name),
            $("li#c" + product.id).length == 0 ? ($.html = '<span class="cht">' + $.cht + '</span></br><span class="pn">' + (overwrite > 1 ? t.ga_in_cart + "&nbsp;x&nbsp;" : "") + '<a href="' + $.baseurl + "product_info.php?products_id=" + product.id + '">' + t.products_name + '</a></span><span class="cnt">' + overwrite + '</span><div id="del' + product.id + '" class="bd"></div><span class="pp">' + $.sym_left + ($.price * t.ga_in_cart).toFixed(2) + $.sym_right + "</span>",
            $("ul#ajax_cart").append('<li id="c' + product.id + '" class="c gr">' + $.html + "</li>")) : ($.html = '<span class="cht">' + $.cht + '</span></br><span class="pn">' + (t.ga_in_cart > 1 ? t.ga_in_cart + "&nbsp;x&nbsp;" : "") + '<a href="' + $.baseurl + "product_info.php?products_id=" + product.id + '">' + t.products_name + '</a></span><span class="cnt">' + overwrite + '</span><div id="del' + product.id + '" class="bd"></div><span class="pp">' + $.sym_left + ($.price * t.ga_in_cart).toFixed(2) + $.sym_right + "</span>",
            $("li#c" + product.id).html($.html)),
            product.stock = t.ga_available_stock,
            $.timer === undefined && ($("div#ajax_status").slideDown(200),
            $.timer = setInterval(countdown, 1e3)),
            updateTotals(),
            t.ticketlimit && alert(lng.ticketlimit),
            t.GAticketlimit && alert(lng.GAticketlimit))
        }
    })
}

window.updateTotals = function() {
	
	//original update Totals that writes to the ajax cart
	if ($("ul#ajax_cart").length){

/* 	$.cnt = 0,
    $.sum = 0,
    $.each($("ul#ajax_cart").children("li"), function() {
        $.sum += parseFloat($(this).children(".pp").html().replace(lng.sym_left + lng.sym_right, "").replace(",", "")),
        $.cnt += parseInt($(this).children(".cnt").html())
    }),
    $("span#total_price").html(lng.sym_left + $.sum.toFixed(2) + lng.sym_right),
    $("span#total_res_price").html(lng.sym_left + $.sum_res.toFixed(2) + lng.sym_right),
    $("span#total_seats").html($.cnt == 1? "1 " + lng.seat : $.cnt + " " + lng.seats),total_head_seats
    $("li#ticket_count span").html($.cnt == 1 ? "1 " + lng.seat : $.cnt + " " + lng.seats),
    $("span#cart_subtotal").length > 0 && ($("span#cart_subtotal").html("&nbsp;" + lng.sym_left + $.sum.toFixed(2) + lng.sym_right),
    parseInt($.sum) == 0 && $("span#discount_notice").length > 0 && $("span#discount_notice").remove()),
    $("div#btnCheckOut").css($.cnt > 0 ? {
        display: "block"
    } : {
        display: "none"
    }) */
	/// new code
    $.cnt = 0, $.sum = 0, $.sum_res = 0, $.each($("ul#ajax_cart").children("li"), function() {
                                $money = 0;
                                if (lng.dec_point != "."){
                                $money = $(this).children(".pp").html().replace(lng.sym_left + lng.sym_right, "");
                                lastComma = $money.lastIndexOf(lng.dec_point);
								if(lastComma >0){
								  $money = $money.substring(0, lastComma) + '.' + $money.substring(lastComma + 1);
								}
                                $money = parseFloat($money.replace(",", ""))}
                                                else
                                 {$money = parseFloat($(this).children(".pp").html().replace(lng.sym_left + lng.sym_right, "").replace(",", ""));
                                                }
        $.sum += $money, 
		
		
		$.sum_res += parseFloat($(this).children(".res").html()), 
		$.cnt += parseInt($(this).children(".cnt").html())
   }), 
   $("span#total_price").html(lng.sym_left + $.sum.toFixed(2) + lng.sym_right), 
   $("span#total_res_price").html(lng.sym_left + $.sum_res.toFixed(2) + lng.sym_right), 
   $("span#total_seats").html(1 == $.cnt ? "1 " + lng.seat : $.cnt + " " + lng.seats), 
   $("li#ticket_count span").html(1 == $.cnt ? "1 " + lng.seat : $.cnt + " " + lng.seats), 
   $("span#cart_subtotal").length > 0 && ($("span#cart_subtotal").html("&nbsp;" + lng.sym_left + $.sum.toFixed(2) + lng.sym_right), 
   0 == parseInt($.sum) && $("span#discount_notice").length > 0 && $("span#discount_notice").remove()), 
   $("div#btnCheckOut").css($.cnt > 0 ? {
        display: "block"
    } : {
        display: "none"

    }), $("div#res_display").css($.sum_res > 0 ? {

        display: "block"

    } : {

        display: "none"

    })
	
	
	}else{
		
	// revised version for header cart
    $.cnt = 0,
    $.sum = 0,
   // $.sum_res = 0,
	
   $.each($(".navbar").find(".cnt"), function() {
        $.cnt += parseInt($(this).html())
    }), 
	  $.each($(".navbar").find(".pp"), function() {
       // $.sum += parseFloat($(this).html().replace(lng.sym_left + lng.sym_right, "").replace(",", ""))
		
		
	$money = 0;
		if (lng.dec_point != "."){
		$money = $(this).html().replace(lng.sym_left + lng.sym_right, "");
		lastComma = $money.lastIndexOf(lng.dec_point);
		if(lastComma >0){
		$money = $money.substring(0, lastComma) + '.' + $money.substring(lastComma + 1);
		}
		$money = parseFloat($money.replace(",", ""))}
						else
		 {$money = parseFloat($(this).html().replace(lng.sym_left + lng.sym_right, "").replace(",", ""));
						}
        $.sum += $money
	
    }), 

    $("span#total_seats").html($.cnt == 1? "1 " + lng.seat + " " + (lng.sym_left + $.sum.toFixed(2) + lng.sym_right): $.cnt + " " + lng.seats + " " + (lng.sym_left + $.sum.toFixed(2) + lng.sym_right)),
	$("span#total_head_seats").html($.cnt == 1? "1 " + lng.seat : $.cnt + " " + lng.seats),
    $("li#ticket_count span").html($.cnt == 1 ? "1 " + lng.seat : $.cnt + " " + lng.seats),
    $("span#cart_subtotal").length > 0 && ($("span#cart_subtotal").html("&nbsp;" + lng.sym_left + $.sum.toFixed(2) + lng.sym_right),
    parseInt($.sum) == 0 && $("span#discount_notice").length > 0 && $("span#discount_notice").remove()),
    $("div#btnCheckOut").css($.cnt > 0 ? {
        display: "block"
    } : {
        display: "none"
    })
	}
}

window.bindInputQuantity = function() {
    $("input#qty").bind("keyup", function() {
        checkStock("")
    })
}

$.ie = window.navigator.userAgent.indexOf('MSIE ') > 0 || window.navigator.userAgent.indexOf('Trident/') > 0 ? "_ie" : "";

var running = !1, timer, count = parseInt($.remaining);
$(function() {
    mobileLayout(),
    bindTriggers(),
    typeof $.cPath != "undefined" && $.cPath  != 0 ? (loader(),
    $("div.seatplan").length == 1 && ($.tick = setInterval("loader();", $.refresh),
    $.free = setInterval("freeSeats();", 24 * $.refresh)),
    $.timeout &&  $.remaining != 0 && ($("div#ajax_status").fadeIn(0),
    $.timer = setInterval(countdown, 1e3)),
    $("li.s").easyTooltip()) : (typeof product == "object" && ($.cPath = product.cPath,
    bindInputQuantity()),
    $.timeout && $.remaining != 0 && ($("div#ajax_status").fadeIn(0),
    $.timer = setInterval(countdown, 1e3))),
    $("ul#ajax_cart").children("li.c").length > 0 && $("div#btnCheckOut").fadeIn(0)

    var offset = 0;//adjust if rewd to start scroll
		
    $(window).scroll(function() 
    {
        if ($(this).scrollTop() > offset) 
        {		
            $('.fixed-cart').addClass('roving-cart');
            $('#cart-hide').removeClass('hidden');
        } else 
        {
            $('.fixed-cart').removeClass('roving-cart');
            $('#cart-hide').addClass('hidden');
            // if truly hidden this next line will reveal when scroll top
            $('.fixed-cart').removeClass('hidden');
        }
    });
    
    //'hide' cart
    $('#cart-hide').on('click', function (e) 
    {

        $('.fixed-cart').removeClass('roving-cart');
        // uncomment this line to really hide the cart
        $('.fixed-cart').addClass('hidden');
    })

});