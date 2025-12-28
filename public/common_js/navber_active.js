$(document).ready(function() {
    $(document).on("wheel", 'input', function(e) {
        $(this).blur();
    });
    const url_get = window.location.href;
    var search = url_get.search("voucher");
   
    if (url_get=='http://18.140.55.144/acc_demo/voucher') {
        $(".frist").removeClass('frist');
        $(".master").addClass('currentNav');
    }else if (url_get.search('acc_demo/voucher/')!= -1) {
        $(".frist").removeClass('frist');
        $(".master").addClass('currentNav');
    }else if (url_get.search('cash-flow')!= -1) {
        $(".frist").removeClass('frist');
         $(".report").addClass('currentNav');
    }else if (url_get.search('party-ledger-details')!= -1) {
        $(".frist").removeClass('frist');
         $(".report").addClass('currentNav');
    } 
    else if (url_get.search("user") != -1) {
        $(".frist").removeClass('frist');
        $(".user").addClass('currentNav');
    } else if (url_get.search("company") != -1) {
        $(".frist").removeClass('frist');
        $(".company").addClass('currentNav');
    } else if (url_get.search("branch") != -1) {
        $(".frist").removeClass('frist');
        $(".company").addClass('currentNav');
    } else if (url_get.search("master-dashboard") != -1) {
        $(".frist").removeClass('frist');
        $(".master").addClass('currentNav');
    } else if (url_get.search("main-dashboard") != -1) {
        $(".main-dashbaord").addClass('currentNav');
    } else if (url_get.search("report") != -1) {
        $(".frist").removeClass('frist');
        $(".report").addClass('currentNav');
    } else if (url_get.search("group-chart") != -1) {
        $(".frist").removeClass('frist');
        $(".master").addClass('currentNav');
    } else if (url_get.search("ledger") != -1) {
        $(".frist").removeClass('frist');
        $(".master").addClass('currentNav');
    } else if (url_get.search("godown") != -1) {
        $(".frist").removeClass('frist');
        $(".master").addClass('currentNav');
    } else if (url_get.search("distribution") != -1) {
        $(".frist").removeClass('frist');
        $(".master").addClass('currentNav');
    } else if (url_get.search("components") != -1) {
        $(".frist").removeClass('frist');
        $(".master").addClass('currentNav');
    } else if (url_get.search("customer") != -1) {
        $(".frist").removeClass('frist');
        $(".master").addClass('currentNav');
    } else if (url_get.search("supplier") != -1) {
        $(".frist").removeClass('frist');
        $(".master").addClass('currentNav');
    } else if (url_get.search("stock-group") != -1) {
        $(".frist").removeClass('frist');
        $(".master").addClass('currentNav');
    } else if (url_get.search("stock-commission") != -1) {
        $(".frist").removeClass('frist');
        $(".master").addClass('currentNav');
    } else if (url_get.search("stock-item") != -1) {
        $(".frist").removeClass('frist');
        $(".master").addClass('currentNav');
    } else if (url_get.search("stock-item") != -1) {
        $(".frist").removeClass('frist');
        $(".master").addClass('currentNav');
    } else if (url_get.search("material") != -1) {
        $(".frist").removeClass('frist');
        $(".master").addClass('currentNav');
    }else if (url_get.search("voucher") != -1) {
        $(".frist").removeClass('frist');
        $(".voucher").addClass('currentNav');
        
        
    }else if (url_get.search("measure") != -1) {
        $(".frist").removeClass('frist');
        $(".master").addClass('currentNav');
    }else if (url_get.search("size") != -1) {
        $(".frist").removeClass('frist');
        $(".master").addClass('currentNav');
    }
})