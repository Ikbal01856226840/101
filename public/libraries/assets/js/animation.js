"use strict";
$(document).ready(function () {
    //   console.log('Checking Console')
        $('.js--triggerAnimation').on('click',function(){
            // e.preventDefault();
            var anim = $('.js--animations').val();
            console.log(anim);

            testAnim(anim);
        });

        $('.js--animations').on('change',function(){
            var anim = $(this).val();
            console.log(anim);

            testAnim(anim);
        });

          function testAnim(x) {
        $('#animationSandbox').removeClass().addClass(x + ' animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
            $(this).removeClass();
        });
    };
    });