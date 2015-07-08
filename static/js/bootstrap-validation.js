!function($) {
    var obj;
    $.fn.validation = function(options) {
        return this.each(function() {
            globalOptions = $.extend({}, $.fn.validation.defaults, options);
            obj=this;
            reg(obj);
            validationForm(obj);
        });
    };
    
    $.fn.validation.defaults = {
        validRules : [
            {name: 'required', validate: function(value) {return ($.trim(value) == '');}, defaultMsg: '请输入内容。'},
            {name: 'number', validate: function(value) {return (!/^[0-9]\d*$/.test(value));}, defaultMsg: '请输入数字。'},
            {name: 'mail', validate: function(value) {return (!/^[a-zA-Z0-9]{1}([\._a-zA-Z0-9-]+)(\.[_a-zA-Z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+){1,3}$/.test(value));}, defaultMsg: '请输入邮箱地址。'},
            {name: 'char', validate: function(value) {return (!/^[a-z\_\-A-Z]*$/.test(value));}, defaultMsg: '请输入英文字符。'},
            {name: 'chinese', validate: function(value) {return (!/^[\u4e00-\u9fff]$/.test(value));}, defaultMsg: '请输入汉字。'}
        ]
    };

    
    
    var formState = false, fieldState = false, wFocus = false, globalOptions = {};

    var validateField = function(field, valid) { // 验证字段
        var el = $(field), error = false, errorMsg = '',
        crule=(el.attr('check-el')==undefined)?null:el.attr('check-el').split(' '),
        msg = (el.attr('check-err')==undefined)?null:el.attr('check-err').split(' ');
        if(crule){
            if( ! eval(crule[0]).test(el.val()) ) {
                error = true;
                errorMsg =msg;
            }
            
        } else {
            for (i = 0; (i < valid.length) ; i++) {
                var x = true, flag = valid[i];
                if (flag.substr(0, 1) == '!') {
                    x = false;
                    flag = flag.substr(1, flag.length - 1);
                }



                var rules = globalOptions.validRules;
                for (j = 0; j < rules.length; j++) {
                    var rule = rules[j];
                    if (flag == rule.name) {
                        if (rule.validate.call(field, el.val()) == x) {
                            error = true;
                            errorMsg = (msg == null)?rule.defaultMsg:msg;
                            break;
                        }
                    }
                }

                if (error) {break;}
            }
        }
            

        var controls = el.parent().find('a');
        var len=controls.length;
        if (error) {
             var cls= (el.attr('check-class')==undefined)?null:el.attr('check-class').split(' ');;
             if ( len<=0) {
                el.after('<a style="float:left;visibility:hidden; height:0px;" data-placement="bottom"  data-content="'+errorMsg+ '"  data-toggle="popover" href="#">msg</a>');
             };
             el.next().popover("show");
//             var pop=el.parent().find(".popover"),pos=pop.offset();
//             pos.top=pos.top-el.next().height();
//             pop.offset(pos);          
//             if ( cls  ) pop.addClass("checkclass");
             //$('#subButton').attr('disabled',"true");//添加disabled属性 
             el.parent().addClass("has-error");
             $(".glyphicon-ok").remove();
             el.after('<span style="margin-top:-18px;" class="glyphicon glyphicon-remove form-control-feedback"></span>');
        } else {
            controls.popover("hide"); 
            //$('#subButton').removeAttr("disabled"); //移除disabled属性 
            el.parent().removeClass("has-error");
            $(".glyphicon-remove").remove();
            el.after('<span style="margin-top:-18px;" class="glyphicon glyphicon-ok form-control-feedback"></span>');
        }
        return !error;
    };

    var reg=function(obj){
        $('input, textarea').each(function() {
            var el = $(this), valid = (el.attr('check-type')==undefined)?null:el.attr('check-type').split(' ');
            valid1 = (el.attr('check-el')==undefined)?null:el.attr('check-el').split(' ');
            if (valid != null && valid.length > 0   || valid1 != null && valid1.length > 0 ) {                       

                el.blur(function() { // 失去焦点时
                    validateField(this, valid);
                });
            }
        });
    }

    var validationForm = function(obj) { // 表单验证方法
        $(obj).submit(function() { // 提交时验证
            if (formState) { // 重复提交则返回
                return false;
            }
            formState = true;
            var validationError = false;
            $('input, textarea', this).each(function () {
                var el = $(this), valid = (el.attr('check-type')==undefined)?null:el.attr('check-type').split(' ');
                if (valid != null && valid.length > 0) {
                    if (!validateField(this, valid)) {
                        if (wFocus == false) {
                            scrollTo(0, el[0].offsetTop - 50);
                            wFocus = true;
                        }

                        validationError = true;
                    }
                }
            });

            wFocus = false;
            fieldState = true;

            if (validationError) {
                formState = false; 
                return false;
            }

            return true;
        });


    };

}(window.jQuery);