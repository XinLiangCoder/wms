
$(function() {
    var nowDate = new Date();
    
    X.formPost($("#addPro") , {
          useTooltip : false,
          focusFirstField : false,
          modForm : function(form,  options){
                var $input = form.find("#cityName"),
                $list = $("#cityChooseBox>li"),
                $cityStr = form.find("#cityString"),
                arr = [],
                arr1 = [],
                obj = {},
                obj1 = {},
                temp = [],
                isAll = false;
                $list.each(function(i , v){
                      var $v = $(v),
                      id = $v.data("id"),
                      c = null,
                      p = null;
                      temp.push($v.text());
                      if(id == "all"){
                             arr1.push(id);
                             isAll = true;
                             return;
                      }else if (id.toString().indexOf("-") <= -1){
                            
                             obj[id] = [];
                      }else{

                          p = id.split("-")[0];
                          if(!!obj[p]){
                                obj[p].push(id);
                          }else{
                                obj[p] = [];
                                obj[p].push(id);
                          }
                      }
                });
               
                if(!!isAll){
                    $input.val(arr1.join(""));
                    return;
                }
                 $.each(obj , function(i , v){
                     arr.push(v.join(","));
                });
                 
                $input.val(arr.join(";"));
                $cityStr.val(temp.join(","));
                arr = null;
                temp =null;
          }
    });
    
    var setDate = function($obj) {
        $obj.DatePicker({
            format: 'Y-m-d',
            date: nowDate.getFullYear() + '-' + (nowDate.getUTCMonth() + 1) + '-' + (nowDate.getUTCDate()),
            starts: 1,
            position: 'r',
            onBeforeShow: function() {
                //$obj.DatePickerSetDate($obj.val(), true);
            },
            onChange: function(formated, dates, input, evt) {
                $obj.val(formated);
                if ( !! $(evt.target).closest("tbody").hasClass("datepickerDays")) {
                    $obj.DatePickerHide();
                }
                $obj.parent().find(".redTip").html("");

            }
        });
    }
    setDate($("#dateInput1"));
    setDate($("#dateInput2"));
    setDate($("#dateInput3"));
    setDate($("#dateInput4"));
    
    
    $("#cityBox").on("click", "input", function(evt) {
        evt.stopPropagation();
        var $t = $(this),
            $cBox = $("#cityChooseBox"),
            str = "",
            nameStr = "",
            $p = $t.closest("li"),
            arrL = [],
            $pp = $p.parent().parent(),
            makeLi = function($t) {
                return '<li data-id="' + $t.data("id") + '" class=""><span>' + $t.next().text() + '</span><em></em></li>';
            };

        if ($t.hasClass("province")) {

            arrL.push(makeLi($t)); 
            !! $p.find("li")[0] && ($p.find("li>input").each(function(i, v) {
                arrL.push(makeLi($(v)));
            }));
            $cBox.prepend(arrL.join(""));
            $p.remove();
        } else if ($t.hasClass("city")) {
            $cBox.prepend(makeLi($t));
            if (!$p.siblings().length) {
                $cBox.prepend(makeLi($pp.find(".province")));
                $pp.remove();
            }
            $p.remove();

        }else{
            arrL.push(makeLi($t));
             !! $p.find("li")[0] && ($p.find("li>input").each(function(i, v) {
                 arrL.push(makeLi($(v)));
             }));
             $cBox.prepend(arrL.join(""));
             $p.html("");
        }
    });
    
    $("#cityBox").find("input").prop("checked","");
    $("#cityChooseBox").on("click", "em", function() {
        var $t = $(this),
            $li = $t.closest("li"),
            $ul = $li.closest("ul"),
            $siblings = $li.siblings(),
            id = $li.data("id"),
            pName = "",
            cName = "",
            arr = [],
            vB = false,
            //$all = $("#cityChooseBox").find("li").data("id"),
            cBox = $("#cityBox>li>ul").length > 0 ? $("#cityBox>li>ul") : $("#cityBox>li").append("<ul></ul>").find("ul"),
            makeLi = function($t) {
                return '<li class="firstLi"><em class="on"></em><input type="checkbox" class="province" data-id="' + $t.data("id") + '"><label>' + $t.text() + '</label></li>';
            },
            makeUlli = function($t, id) {
                return '<li><input type="checkbox" class="city" data-id="' + id + '"><label>' + $t.text() + '</label></li>';

            };
        $("#cityChooseBox").find("li").each(function(i , v){
              if($(v).data("id") == "all"){
                   $(v).remove();
                   cBox.parent().prepend('<em class="on"></em><input type="checkbox" data-id="all" class="j_whole"><label>全国</label>');
              }
        });

        
        if (id.toString().indexOf("-") > -1) {
            $siblings.each(function(i, v) {
                var $v = $(v);
                if ($v.data("id") == id.split("-")[0]) {
                    pName = $v.text();
                    var $temp = $(makeLi($v));
                    $temp.append('<ul class="clearfix">' + makeUlli($li, id) + '</ul>');
                    cBox.prepend($temp);
                    $v.remove();

                    vB = true;
                }
            });
            if (!vB) {
                cBox.find("input").each(function(i, v) {
                    var $v = $(v);
                    if ($v.data("id") == id.split("-")[0]) {
                        $v.closest("li").find("ul").prepend(makeUlli($li, id));
                        return;
                    }
                });

            }
            $li.remove();

        }else if( id == "all" ){
            $li.remove();
            $("#cityBox").html($("#cityAll").html());
            $("#cityChooseBox").html("");
        } else {
            $siblings.each(function(i, v) {
                var $v = $(v);
                if ($v.data("id").toString().split("-")[0] == id) {
                    arr.push(makeUlli($v, $v.data("id")));
                    $v.remove();
                }
            });
            var $temp = $(makeLi($li));
            $temp.append('<ul class="clearfix">' + arr.join("") + '</ul>');
            cBox.prepend($temp);
            $li.remove();
            arr = null;

        }

    });

    $("#cityBox li").on("click", ">em", function() {
        var $t = $(this),
            $ul = $t.closest("li").find("ul"),
            $em = $t;

        $em.toggleClass("on").toggleClass("off");
        if ($em.hasClass("on")) {
            $ul.removeClass("none");
        } else {
            $ul.addClass("none");
        }
    });

    $('#category_id').change(function(){
           if(typeof saveType == "undefined"){
                return false;
           }
           var $t = $(this) ,
                $type = saveType,
                $oS =$("#saveType"),
                index = this.selectedIndex,
                option = $type[index].option,
                sIndex = $type[index].index,
                arr = [];
            
                
                 if(option.length > 0){
                        $oS.removeClass("none");
                        $("#typeText").html(option.text);
                        $.each(option , function(i , v){
                            if(i == sIndex){
                                arr.push('<option value="'+  $type[index].value[i] +'" selected="selected">' +option[i]+ '</option>');
                            }else{
                                arr.push('<option value="'+  $type[index].value[i] +'" >' +option[i]+ '</option>');
                            }
                            
                        });
                        $("#deadline").html(arr.join("\n"));
                        arr = null;
                 }else{
                        $oS.addClass("none");
                 }
           

    });
    $('#category_id').change();

});