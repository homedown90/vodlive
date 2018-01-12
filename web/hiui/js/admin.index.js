$(function(){
	//为菜单添加单机事件
    function bindNavbarClick()
    {
        $("#J_navList  li").on('click',function(event){
            event.preventDefault();
            if($(this).find("li").length == 0)
            {
                //取消其他选项的选择状态
                $("#J_navList li.active").removeClass("active").parents("li.opened").removeClass('opened');
                $(this).addClass('active').parents('li').addClass("opened");
                var title = $(this).find('a:first-child').attr("title");
                var href = $(this).find('a:first-child').attr("href");
                var html_breadcrumb = $('.be-breadcrumb.breadcrumb').find("li:first").prop('outerHTML');
                if(title == "首页")
                {
                    $(window).attr('location','/');
                }else {
                     html_breadcrumb =html_breadcrumb+ '<li class="active">'+$(this).find('span').html()+'</li>';
                    $(".be-main .main-content:first-child").load(href);
                }
                $('.be-breadcrumb.breadcrumb').html(html_breadcrumb);
                //收缩其他选项---后期优化

            }else {
                $("#J_navList li.opened").removeClass("opened");
                $(this).addClass('opened').parents('li').addClass("opened");

            }
        });
    }
    bindNavbarClick();
});	