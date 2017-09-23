/*
@功能：购物车页面js
@作者：diamondwang
@时间：2013年11月14日
*/

$(function(){
	//删除
	$(".del-btn").click(function () {
		//找到点击商品的id
		var tr = $(this).closest("tr");
		var goods_id = tr.attr("data-id");
		//使用ajax调用删除接口并将id传入
		$.post('/index.php/member/delete.html',{goods_id:goods_id},function (data) {
			if(data) {
				tr.remove();
			} else {
				alert('删除失败');
			}
        })
    });
	
	//减少
	$(".reduce_num").click(function(){
        var tr = $(this).closest("tr");
        var goods_id = tr.attr('data-id');

        var amount = $(this).parent().find(".amount");
        var num = parseInt(amount.val()) - 1;

        //发起ajax请求，将要修改的商品id和数量发送到后台
        $.post('/index.php/member/ajax.html',{goods_id:goods_id,amount:num},function(data){
            if(data=='success'){
                //修改成功
                $(amount).val(num);
                //小计
                var subtotal = parseFloat(tr.find(".col3 span").text()) * parseInt($(amount).val());
                tr.find(".col5 span").text(subtotal.toFixed(2));
                //总计金额
                var total = 0;
                $(".col5 span").each(function(){
                    total += parseFloat($(this).text());
                });

                $("#total").text(total.toFixed(2));
            }else{
                console.log('修改失败：'+data);
            }
        });
	});

	//增加
	$(".add_num").click(function(){
        var tr = $(this).closest("tr");
        var goods_id = tr.attr('data-id');

		var amount = $(this).parent().find(".amount");
        var num = parseInt(amount.val()) + 1;

        //发起ajax请求，将要修改的商品id和数量发送到后台
        $.post('/index.php/member/ajax.html',{goods_id:goods_id,amount:num},function(data){
            if(data=='success'){
                //修改成功
                $(amount).val(num);
                //小计
                var subtotal = parseFloat(tr.find(".col3 span").text()) * parseInt($(amount).val());
                tr.find(".col5 span").text(subtotal.toFixed(2));
                //总计金额
                var total = 0;
                $(".col5 span").each(function(){
                    total += parseFloat($(this).text());
                });

                $("#total").text(total.toFixed(2));
            }else{
                console.log('修改失败：'+data);
            }
        });

	});


    //直接输入
	$(".amount").blur(function(){
		if (parseInt($(this).val()) < 1){
			alert("商品数量最少为1");
			$(this).val(1);
		}
		//小计
		var subtotal = parseFloat($(this).parent().parent().find(".col3 span").text()) * parseInt($(this).val());
		$(this).parent().parent().find(".col5 span").text(subtotal.toFixed(2));
		//总计金额
		var total = 0;
		$(".col5 span").each(function(){
			total += parseFloat($(this).text());
		});

		$("#total").text(total.toFixed(2));

	});
});