$(function(){
	//为菜单添加单机事件
    function bindNavbarClick()
    {
        $("#J_navList  li").on('click',function(event){

            if($(this).find("li").length == 0)
            {
                //取消其他选项的选择状态
                $("#J_navList li.active").removeClass("active").parents("li.opened").removeClass('opened');
                $(this).addClass('active').parents('li').addClass("opened");
                //收缩其他选项
                //$("#J_navList > li:not(.opened)").find("a[aria-expanded='true']").click();
                $(this).parents("li:first").has(":not(.opened)").find("a[aria-expanded='true']").click();
            }else {
                $("#J_navList li.opened").removeClass("opened");
                $(this).addClass('opened').parents('li').addClass("opened");

            }
            //event.preventDefault();
        });
    }
    bindNavbarClick();
});	